<?php

namespace App\Controller;

use App\Repository\TweetRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserController extends AbstractController
{
    public function __construct(
        private UserRepository $userRepository)
    { }

    #[Route('/api/users', name: 'app_users_all', methods: ['GET'])]
    public function getAll(): JsonResponse
    {
        $users = $this->userRepository->findAll();

        if (!$users)
            return $this->json(['error' => 'Aucun utilisateur trouvé'], 404);

        $data = [];
        foreach ($users as $user) {
            $data[] = [
                'id' => $user->getId(),
                'username' => $user->getUsername(),
                'roles' => $user->getRoles(),
            ];
        }
        return $this->json($data);
    }


    #[Route('/api/users/{username}', name: 'app_users_all', methods: ['GET'])]
    public function getByUsername(string $username): JsonResponse
    {
        $user = $this->userRepository->findOneBy(['username' => $username]);
        if (!$user)
            return $this->json(['error' => 'Utilisateur introuvable'], 404);

        $data[] = [
            'id' => $user->getId(),
            'username' => $user->getUsername(),
            'roles' => $user->getRoles(),
        ];

        return $this->json($data);
    }
}