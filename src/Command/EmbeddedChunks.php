<?php

declare(strict_types=1);

namespace App\Command;

use App\Document\ChunkedDocuments;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[AsCommand(
    name: 'app:embed-chunks',
    description: 'This command will create embedding for the stored chunks using Voyage AI API key and store into MongoDB database',
)]
class EmbeddedChunks extends Command
{

    public function __construct(
        private readonly DocumentManager $documentManager,
        #[Autowire(env: 'VOYAGE_API_KEY')]
        private readonly string $voyageAiApiKey,

        #[Autowire(env: 'VOYAGE_ENDPOINT')]
        private readonly string $voyageEndpoint,

        #[Autowire(env: 'BATCH_SIZE')]
        private readonly int $batchSize,

        #[Autowire(env: 'MAX_RETRIES')]
        private readonly int $maxRetries,
        
        private HttpClientInterface $client,
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
                $embeddedCount += $this->embedAndPersist($this->client, $batchedContent, $originalDocs, $io);
                $batchedContent = [];
                $originalDocs = [];
                gc_collect_cycles();
            }
        }

        if (!empty($batchedContent)) {
            $embeddedCount += $this->embedAndPersist($this->client, $batchedContent, $originalDocs, $io);
        }

        $this->documentManager->flush();

        $io->success("Embedded {$embeddedCount} documents.");
        return Command::SUCCESS;
    }

    private function embedAndPersist(HttpClientInterface $client, array $inputTexts, array $originalDocs, SymfonyStyle $io): int
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

            $response = $client->request('POST', $this->voyageEndpoint, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->voyageAiApiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => $payload,
            ]);

            $data = json_decode($response->getContent(), true);

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
        } catch (\Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface |
                 \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface |
                 \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface $e) {
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
