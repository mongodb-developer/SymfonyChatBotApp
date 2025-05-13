<?php

declare(strict_types=1);

namespace App\Command;

use App\Document\ChunkedDocuments;
use Doctrine\ODM\MongoDB\DocumentManager;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:embed-chunks',
    description: 'This command will create embedding for the stored chunks using Voyage AI API key and store into MongoDB database',
)]
class EmbeddedChunks extends Command
{

    public function __construct(
        private readonly DocumentManager $documentManager,
        private readonly string $voyageAiApiKey,
        private readonly string $voyageEndpoint,
        private readonly int $batchSize = 32,
        private readonly int $maxRetries = 3
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Batch embedding documents from MongoDB using Voyage AI');

        $documents = $this->documentManager
            ->getRepository(ChunkedDocuments::class)
            ->findAll();

        if (empty($documents)) {
            $io->warning('No documents found.');
            return Command::SUCCESS;
        }

        $client = new Client();
        $batchedContent = [];
        $originalDocs = [];
        $embeddedCount = 0;

        foreach ($documents as $doc) {
            $text = trim($doc->getContent());
            if (empty($text)) continue;

            $text = mb_substr($text, 0, 4000); 

            $batchedContent[] = $text;
            $originalDocs[] = $doc;

            if (count($batchedContent) >= $this->batchSize) {
                $embeddedCount += $this->embedAndPersist($client, $batchedContent, $originalDocs, $io);
                $batchedContent = [];
                $originalDocs = [];
                gc_collect_cycles();
            }
        }

        if (!empty($batchedContent)) {
            $embeddedCount += $this->embedAndPersist($client, $batchedContent, $originalDocs, $io);
        }

        $this->documentManager->flush();

        $io->success("Embedded {$embeddedCount} documents.");
        return Command::SUCCESS;
    }

    private function embedAndPersist(Client $client, array $inputTexts, array $originalDocs, SymfonyStyle $io): int
    {
        $payload = [
            'input' => $inputTexts,
            'model' => 'voyage-3',
            'input_type' => 'document',
        ];

        $embedded = 0;
        $attempt = 0;
        $success = false;

        while ($attempt < $this->maxRetries && !$success) {
            try {
                $attempt++;

                $response = $client->post($this->voyageEndpoint, [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $this->voyageAiApiKey,
                        'Content-Type' => 'application/json',
                    ],
                    'json' => $payload,
                    'timeout' => 60,
                ]);

                $data = json_decode($response->getBody()->getContents(), true);
                if (!isset($data['data'])) {
                    throw new \RuntimeException('Missing "data" in Voyage AI response');
                }

                foreach ($data['data'] as $index => $embeddingResult) {
                    $embedding = $embeddingResult['embedding'] ?? null;
                    if ($embedding === null) continue;

                    $originalDoc = $originalDocs[$index];
                    $originalDoc->setcontentEmbedding($embedding);
                    $originalDoc->setcreatedAt(new \DateTime());
                    $this->documentManager->persist($originalDoc);
                    $embedded++;
                }

                $success = true;
            } catch (RequestException $e) {
                $io->warning("Attempt $attempt failed: " . $e->getMessage());
                sleep(2 * $attempt);
            } catch (\Throwable $e) {
                $io->error('Unexpected error: ' . $e->getMessage());
                break;
            }
        }

        return $embedded;
    }
}
