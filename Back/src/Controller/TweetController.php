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
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TweetController extends AbstractController
{
    public function __construct(
        private TweetRepository $tweetRepository,
        private UserRepository $userRepository,
        private EntityManagerInterface $em,
        private ValidatorInterface $validator)
    { }

    #[Route('/api/tweet', name: 'app_tweet_all', methods: ['GET'])]
    public function getAll(): JsonResponse
    {
        $tweets = $this->tweetRepository->findAll();

        if (!$tweets)
            return $this->json(['error' => 'Aucun tqeet trouvé'], 404);

        $data = [];
        foreach ($tweets as $tweet) {
            $data[] = [
                'id' => $tweet->getId(),
                'content' => $tweet->getContent(),
                'user' => $tweet->getUsr()->getUsername(),
                'likes' => count($tweet->getLikes()),
                'createdAt' => $tweet->getCreatedAt()->format('Y-m-d H:i:s'),
            ];
        }
        return $this->json($data);
    }

    #[Route('/api/tweet/{id}', name: 'app_tweet_by_id', methods: ['GET'])]
    public function getById(int $id): JsonResponse
    {
        $tweet = $this->tweetRepository->find($id);
        if (!$tweet)
            return $this->json(['error' => 'Tweet introuvable'], 404);

        $data[] = [
            'id' => $tweet->getId(),
            'content' => $tweet->getContent(),
            'user' => $tweet->getUsr()->getUsername(),
            'likes' => count($tweet->getLikes()),
            'createdAt' => $tweet->getCreatedAt()->format('Y-m-d H:i:s'),
        ];

        return $this->json($data);
    }

    #[Route('/api/tweet/user/{userId}', name: 'app_tweet_by_user', methods: ['GET'])]
    public function getByUserId(int $userId): JsonResponse
    {
        // Récupération de l'utilisateur
        $user = $this->userRepository->find($userId);
        if (!$user)
            return $this->json(['error' => 'Utilisateur introuvable'], 404);

        // Récupération des tweets de l'utilisateur
        $tweets = $this->tweetRepository->findBy(['usr' => $user]);
        if (!$tweets)
            return $this->json(['error' => 'Aucun tweet trouvé pour cet utilisateur'], 404);

        $data = [];
        foreach ($tweets as $tweet) {
            $data[] = [
                'id' => $tweet->getId(),
                'content' => $tweet->getContent(),
                'user' => $tweet->getUsr()->getUsername(),
                'likes' => count($tweet->getLikes()),
                'createdAt' => $tweet->getCreatedAt()->format('Y-m-d H:i:s'),
            ];
        }

        return $this->json($data);
    }

    #[Route('/api/tweet', name: 'app_create_tweet', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        // Récupération de la requête
        $data = json_decode($request->getContent(), true);
        $tweet = new Tweet();
        $tweet->setContent($data['content'] ?? '');
        $tweet->setUsr($this->getUser());
        $tweet->setCreatedAt(new \DateTime());

        $errors = $this->validator->validate($tweet);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getPropertyPath() . ' : ' . $error->getMessage();
            }
            return $this->json(['errors' => $errorMessages], 400);
        }

        // Enregistrez le tweet
        $this->em->persist($tweet);
        $this->em->flush();

        return $this->json(['message' => 'Tweet créé avec succès']);
    }

    #[Route('/api/tweet/{id}', name: 'app_update_tweet', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $tweet = $this->tweetRepository->find($id);

        if (!$tweet)
            return $this->json(['error' => 'Tweet introuvable'], 404);

        // Vérifiez si l'utilisateur est le propriétaire du tweet
        $user = $this->userRepository->findOneBy(['username' => "testuser"]);
        if ($tweet->getUsr() !== $this->getUser())
            return $this->json(['error' => 'Vous n\'êtes pas propriétaire du tweet, impossible de le modifier'], 403);

        // Récupération de la requête
        $data = json_decode($request->getContent(), true);
        $content = $data['content'] ?? null;

        if (!$content)
            return $this->json(['error' => 'Le contenu du tweet est obligatoire'], 400);

        // Mettez à jour le contenu du tweet
        $tweet->setContent($content);
        $tweet->setUpdatedAt(new \DateTime());
        $this->em->flush();

        return $this->json(['message' => 'Tweet modifié avec succès']);
    }

    #[Route('/api/tweet/{id}', name: 'app_delete_tweet', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $tweet = $this->tweetRepository->find($id);

        if (!$tweet)
            return $this->json(['error' => 'Tweet introuvable'], 404);

        // Vérifiez si l'utilisateur est le propriétaire du tweet
        if ($tweet->getUsr() !== $this->getUser())
            return $this->json(['error' => 'Vous n\'êtes pas propriétaire du tweet, impossible de le supprimer'], 403);

        // Supprimez le tweet
        $this->em->remove($tweet);
        $this->em->flush();

        return $this->json(['message' => 'Tweet supprimé avec succès']);
    }

    #[Route('/api/tweet/like/{id}', name: 'app_like_tweet', methods: ['POST'])]
    public function like(int $id): JsonResponse
    {
        $tweet = $this->tweetRepository->find($id);

        if (!$tweet)
            return $this->json(['error' => 'Tweet introuvable'], 404);

        // Vérifiez si l'utilisateur a déjà aimé le tweet
        if ($tweet->getLikes()->contains($this->getUser()))
            return $this->json(['error' => 'Vous avez déjà liké ce tweet'], 400);

        // Ajoutez l'utilisateur à la liste des likes
        $user = $this->getUser();

        if (!$user instanceof \App\Entity\User) {
            return $this->json(['error' => 'Utilisateur non identifié ou invalide'], 401);
        }

        $tweet->addLike($user);
        $this->em->flush();

        return $this->json(['message' => 'Tweet liké avec succès']);
    }

    #[Route('/api/tweet/search/{research}', name: 'app_search_tweet', methods: ['GET'])]
    public function search(string $research): JsonResponse
    {
        $tweets = $this->tweetRepository->findByContent($research);

        if (!$tweets)
            return $this->json(['error' => 'Aucun tweet trouvé'], 404);

        $data = [];
        foreach ($tweets as $tweet) {
            $data[] = [
                'id' => $tweet->getId(),
                'content' => $tweet->getContent(),
                'user' => $tweet->getUsr()->getUsername(),
                'likes' => count($tweet->getLikes()),
                'createdAt' => $tweet->getCreatedAt()->format('Y-m-d H:i:s'),
            ];
        }

        return $this->json($data);
    }
}
