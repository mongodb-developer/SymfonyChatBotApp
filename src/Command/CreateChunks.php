<?php

namespace App\Command;

use App\Document\ChunkedDocuments;
use LLPhant\Embeddings\DataReader\FileDataReader;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use LLPhant\Embeddings\DocumentSplitter\DocumentSplitter; 
use Doctrine\ODM\MongoDB\DocumentManager;

#[AsCommand(
    name: 'app:create-chunks',
    description: 'This command will generate chunks from the rst files and store into MongoDB',
)]
class CreateChunks extends Command
{
    protected static $defaultName = 'app:create-chunks';
    private DocumentManager $documentManager;

    public function __construct(DocumentManager $documentManager)
{
    parent::__construct();
    $this->documentManager = $documentManager;
}

protected function execute(InputInterface $input, OutputInterface $output): int
{
    $io = new SymfonyStyle($input, $output);
    $io->title("Chunking all .rst files and storing them into MongoDB");

    $directory = '../../public/';
    if (!is_dir($directory)) {
        throw new \Exception("Directory not found: " . $directory);
    }

    $totalChunks = 0;

    foreach (new \DirectoryIterator($directory) as $fileInfo) {
        if ($fileInfo->isFile() && $fileInfo->getExtension() === 'rst') {
            $filePath = $fileInfo->getPathname();
            $io->section("Processing file: " . $fileInfo->getFilename());

            $dataReader = new FileDataReader($filePath);
            $documents = $dataReader->getDocuments();

            $splittedDocuments = DocumentSplitter::splitDocuments($documents, 1000, '.', 20);

            foreach ($splittedDocuments as $doc) {
                $chunk = new ChunkedDocuments();
                $chunk->setContent($doc->content);
                
                $chunk->setSourceName($doc->sourceName);
                $chunk->setCreatedAt(new \DateTime());

                $this->documentManager->persist($chunk);
                $totalChunks++;
            }

            $this->documentManager->flush();
            $this->documentManager->clear(); 
        }
    }

    $io->success("Successfully stored $totalChunks chunks in MongoDB.");
    return Command::SUCCESS;
}

}




