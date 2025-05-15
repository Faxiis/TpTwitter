<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CompteController extends AbstractController
{
    #[Route('/compte', name: 'app_Compte')]
    public function index(Request $request): Response
    {
        return $this->render('Compte/Compte.html.twig', [
            'controller_name' => 'CompteController',
        ]);
    }
}
