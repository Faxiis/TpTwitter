<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class UserController extends AbstractController
{
    public function __construct(
        private UserRepository $userRepository,
        private EntityManagerInterface $em)
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

    #[Route('/api/user/pp', name: 'api_upload_profile_picture', methods: ['POST'])]
    public function uploadPicture(Request $request): JsonResponse
    {
        $file = $request->files->get('profile_picture');

        if (!$file) {
            return new JsonResponse(['error' => 'Aucun fichier envoyé'], 400);
        }

        if (!in_array($file->getMimeType(), ['image/jpeg', 'image/png'])) {
            return new JsonResponse(['error' => 'Format non supporté, veuillez importer un fichier .jpeg ou .png'], 400);
        }

        $filename = uniqid() . '.' . $file->guessExtension();
        $file->move($this->getParameter('profile_pictures_directory'), $filename);

        // Met à jour l'utilisateur (par exemple)
        $user = $this->getUser(); // ou récupère depuis JWT
        $user->setProfilePicture($filename);
        $this->em->flush();

        return new JsonResponse(['success' => true, 'filename' => $filename]);
    }
}