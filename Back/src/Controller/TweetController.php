<?php

namespace App\Controller;

use App\Repository\TweetRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class TweetController extends AbstractController
{
    private $tweetRepository;

    public function __construct(TweetRepository $tweetRepository)
    {
        $this->tweetRepository = $tweetRepository;
    }

    #[Route('/tweet', name: 'app_tweet', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $tweets = $this->tweetRepository->findAll();
        $data = [];
        foreach ($tweets as $tweet) {
            $data[] = [
                'id' => $tweet->getId(),
                'content' => $tweet->getContent(),
                'user' => $tweet->getUsr()->getUsername(),
                'likes' => count($tweet->getLikes()),
            ];
        }
        return $this->json($data);
    }
}
