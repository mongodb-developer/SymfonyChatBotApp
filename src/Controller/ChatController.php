<?php


namespace App\Controller;

use App\Service\ResponseService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ChatController extends AbstractController
{
    private ResponseService $responseService;

    public function __construct(ResponseService $responseService)
    {
        $this->responseService = $responseService;
    }

    #[Route('/', name: 'chat_home')]
public function index(Request $request): Response
{
    $question = null;
    $answer = null;

    if ($request->isMethod('POST')) {
        $question = trim(strtolower($request->request->get('question')));

        $answer = $this->getPredefinedResponse($question);

        if ($answer === null) {
            $answer = $this->responseService->getResponseForQuestion($question);
        }
    }

    return $this->render('chat.html.twig', [
        'question' => $question,
        'answer' => $answer,
    ]);
}

private function getPredefinedResponse(string $message): ?string
{
    $responses = [
        'hi' => 'Hello! How can I help you with Symfony today?',
        'hello' => 'Hello! What can I do for you today? We recommend asking a Symfony or Doctrine based question',
        'hey' => 'Hey there! You can ask me anything about Symfony',
        'how are you' => 'I am doing fine, how are you? Are you looking for something related to Symfony or Doctrine ?',
        'good morning' => 'Good morning! Do you have any Symfony related queries?',
        'good evening' => 'Good evening! Need help with something Symfony or Doctrine related?',
        'bye' => 'Goodbye! ',
        'goodbye' => 'Bbye',
        'thank you' => 'You are welcome! Please feel free to ask any further questions',
        'thanks' => 'Glad to be helpful',
        'good night' => 'Good night',
    ];

    foreach ($responses as $key => $response) {
        if (strpos($message, $key) !== false) {
            return $response;
        }
    }
    return null;
}

}
