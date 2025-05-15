<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AuthController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserRepository $userRepository,
        private UserPasswordHasherInterface $passwordHasher,
        private ValidatorInterface $validator
    ) {}

    #[Route('/api/register', name: 'app_register', methods: ['POST'])]
    public function register(Request $request): JsonResponse
    {
        // Récupération de la requête
        $data = json_decode($request->getContent(), true);
        $user = new User();
        $user->setUsername($data['username'] ?? '');
        $user->setPassword($data['password'] ?? '');
        $user->setCreatdAt(new \DateTime());

        // Vérifiez si l'utilisateur existe déjà
        if ($this->userRepository->findOneBy(['username' => $user->getUsername()])) {
            return $this->json([
                'success' => false,
                'message' => 'Un utilisateur avec le même identifiant existe déjà'
            ], 400);
        }

        $errors = $this->validator->validate($user);

        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            return $this->json([
                'success' => false,
                'errors' => $errorMessages
            ], 400);
        }

        $user->setPassword(
            $this->passwordHasher->hashPassword($user, $user->getPassword())
        );
        $user->setRoles(['ROLE_USER']);

        // Enregistrez l'utilisateur
        $this->em->persist($user);
        $this->em->flush();


        return $this->json([
            'success' => true,
            'message' => 'Utilisateur enregistré avec succès'
        ]);
    }

}