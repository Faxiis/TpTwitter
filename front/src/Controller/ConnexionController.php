<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ConnexionController extends AbstractController
{
    #[Route('/connexion', name: 'app_Connexion')]
    public function index(): Response
    {
        return $this->render('Connexion/Connexion.html.twig', [
            'controller_name' => 'ConnexionController',
        ]);
    }
}
