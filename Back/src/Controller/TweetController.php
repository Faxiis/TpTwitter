<?php

namespace App\Controller;

use App\Entity\Tweet;
use App\Repository\TweetRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class TweetController extends AbstractController
{
    private $tweetRepository;
    private $em;
    private $userRepository;

    public function __construct(TweetRepository $tweetRepository, UserRepository $userRepository, EntityManagerInterface $em)
    {
        $this->tweetRepository = $tweetRepository;
        $this->userRepository = $userRepository;
        $this->em = $em;
    }

    #[Route('/tweet', name: 'app_tweet_all', methods: ['GET'])]
    public function getAll(): JsonResponse
    {
        $tweets = $this->tweetRepository->findAll();

        if (!$tweets)
            return $this->json(['error' => 'No tweets found'], 404);

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

    #[Route('/tweet/{id}', name: 'app_tweet_by_id', methods: ['GET'])]
    public function getById(int $id): JsonResponse
    {
        $tweet = $this->tweetRepository->find($id);
        if (!$tweet)
            return $this->json(['error' => 'Tweet not found'], 404);

        $data[] = [
            'id' => $tweet->getId(),
            'content' => $tweet->getContent(),
            'user' => $tweet->getUsr()->getUsername(),
            'likes' => count($tweet->getLikes()),
        ];

        return $this->json($data);
    }

    #[Route('/tweet/user/{userId}', name: 'app_tweet_by_user', methods: ['GET'])]
    public function getByUserId(int $userId): JsonResponse
    {
        // Récupération de l'utilisateur
        $user = $this->userRepository->find($userId);
        if (!$user)
            return $this->json(['error' => 'User not found'], 404);

        // Récupération des tweets de l'utilisateur
        $tweets = $this->tweetRepository->findBy(['usr' => $user]);
        if (!$tweets)
            return $this->json(['error' => 'No tweets found for this user'], 404);

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

    #[Route('/tweet', name: 'app_create_tweet', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        // Récupération de la requête
        $data = json_decode($request->getContent(), true);
        $content = $data['content'] ?? null;

        if (!$content)
            return $this->json(['error' => 'Content is required'], 400);

        $tweet = new Tweet();
        $tweet->setContent($content);

        // Pour test postman
       // $tweet->setUsr($this->getUser());
        $tweet->setUsr($this->userRepository->findOneBy(['username' => "testuser"]));

        // Enregistrez le tweet
        $this->em->persist($tweet);
        $this->em->flush();

        return $this->json(['message' => 'Tweet created successfully']);
    }

    #[Route('/tweet/{id}', name: 'app_update_tweet', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $tweet = $this->tweetRepository->find($id);

        if (!$tweet)
            return $this->json(['error' => 'Tweet not found'], 404);

        // Vérifiez si l'utilisateur est le propriétaire du tweet
        $user = $this->userRepository->findOneBy(['username' => "testuser"]);
        //if ($tweet->getUsr() !== $this->getUser())
        if ($tweet->getUsr() !== $user)
            return $this->json(['error' => 'You are not authorized to update this tweet'], 403);

        // Récupération de la requête
        $data = json_decode($request->getContent(), true);
        $content = $data['content'] ?? null;

        if (!$content)
            return $this->json(['error' => 'Content is required'], 400);

        // Mettez à jour le contenu du tweet
        $tweet->setContent($content);
        $this->em->flush();

        return $this->json(['message' => 'Tweet updated successfully']);
    }

    #[Route('/tweet/{id}', name: 'app_delete_tweet', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $tweet = $this->tweetRepository->find($id);

        if (!$tweet)
            return $this->json(['error' => 'Tweet not found'], 404);

        // Vérifiez si l'utilisateur est le propriétaire du tweet
        if ($tweet->getUsr() !== $this->getUser())
            return $this->json(['error' => 'You are not authorized to delete this tweet'], 403);

        // Supprimez le tweet
        $this->em->remove($tweet);
        $this->em->flush();

        return $this->json(['message' => 'Tweet deleted successfully']);
    }

    #[Route('/tweet/like/{id}', name: 'app_like_tweet', methods: ['POST'])]
    public function like(int $id): JsonResponse
    {
        $tweet = $this->tweetRepository->find($id);

        if (!$tweet)
            return $this->json(['error' => 'Tweet not found'], 404);

        $user = $this->userRepository->findOneBy(['username' => "testuser"]);

        // Vérifiez si l'utilisateur a déjà aimé le tweet
        //if ($tweet->getLikes()->contains($this->getUser()))
        if ($tweet->getLikes()->contains($user))
            return $this->json(['error' => 'You have already liked this tweet'], 400);

        // Ajoutez l'utilisateur à la liste des likes
        $tweet->addLike($user);
        $this->em->flush();

        return $this->json(['message' => 'Tweet liked successfully']);
    }
}
