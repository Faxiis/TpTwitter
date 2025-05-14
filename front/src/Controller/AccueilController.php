<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class AccueilController extends AbstractController
{
    #[Route('/accueil', name: 'app_accueil')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        // Exemple d'utilisateurs avec photo (peut être une URL ou null si pas de photo)
        $utilisateurs = [
            ['name' => 'Alice', 'photo' => 'photo1.jpg'], // Utilisateur avec photo
            ['name' => 'Bob', 'photo' => null],          // Utilisateur sans photo
            ['name' => 'Charlie', 'photo' => 'photo3.jpg'], // Utilisateur avec photo
            ['name' => 'David', 'photo' => null],          // Utilisateur sans photo
            ['name' => 'Eve', 'photo' => 'photo5.jpg'],    // Utilisateur avec photo
        ];

        return $this->render('Accueil/Accueil.html.twig', [
            'utilisateurs' => $utilisateurs, // On passe la liste des utilisateurs avec leurs photos
        ]);
    }
}
