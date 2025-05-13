<?php

namespace App\Service;

use App\Document\ChunkedDocuments;
use Doctrine\ODM\MongoDB\DocumentManager;
use MongoDB\Builder\Pipeline;
use MongoDB\Builder\Stage;



class ResponseService
{

    public function __construct(
        private readonly DocumentManager $documentManager,
        private readonly string $openAiApiKey,
        private readonly string $openAiApiUrl,
        private readonly string $voyageAiApiKey,
        private readonly string $voyageEndpoint
    ) {}


    public function getResponseForQuestion(string $question): ?string
    {

        $embeddedquestion = $this->generateEmbedding($question);

        if (!$embeddedquestion) {
            return "Sorry, couldmt understand the question";
        }

        $collection = $this->documentManager->getDocumentCollection(ChunkedDocuments::class);
        
        $pipeline = new Pipeline(
            [Stage::vectorSearch(index: 'vector_index', path: 'contentEmbedding', queryVector: $embeddedquestion,  limit: 5,  numCandidates: 100)],
            [Stage::project(_id: 0, content: 1)]
        );

        $cursor = $collection->aggregate($pipeline)->toArray();

        $contents = array_map(fn($item) => $item['content'], $cursor);

        $result = $this->formatResponse($contents, $question);
        
        return $result;
    }

   

    public function formatResponse(array $contents, string $query): string
{

    $client = new \GuzzleHttp\Client();
    $combineResponse = implode("\n", array_map('trim', $contents));

    $messages = [
        [
            'role' => 'system',
            'content' => 'You are a Symfony chatbot. You help users by answering questions based on Symfony documentation.'
        ],
        [
            'role' => 'user',
            'content' => "Using the following context from Symfony documentation:\n\n" . $combineResponse
        ]
    ];

    if ($query) {
        $messages[] = [
            'role' => 'user',
            'content' => "User query: " . $query
        ];
    }
    try {
        $response = $client->post($this->openAiApiUrl, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->openAiApiKey,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'model' => 'gpt-4.1',
                'messages' => $messages
            ],
            'timeout' => 30,
        ]);

        $data = json_decode($response->getBody()->getContents(), true);
        $OpenAiResponse = $data['choices'][0]['message']['content'] ?? 'No reply from OpenAI.';

        return $OpenAiResponse;

    } catch (\Throwable $e) {
        return "Error generating response: " . $e->getMessage();
    }
}

private function generateEmbedding(string $text): ?array
{
    $client = new \GuzzleHttp\Client();

    try {
        $response = $client->post($this->voyageEndpoint, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->voyageAiApiKey,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'input' => [$text],
                'model' => 'voyage-3',
                'input_type' => 'query'
            ],
            'timeout' => 20,
        ]);

        $data = json_decode($response->getBody()->getContents(), true);
        return $data['data'][0]['embedding'] ?? null;
    } catch (\Throwable $e) {
        return null;
    }
}
}
