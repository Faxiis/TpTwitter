<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class ResultatRechercheController extends AbstractController
{
    #[Route('/resultatrecherche', name: 'app_resultatrecherche')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        
        return $this->render('ResultatRecherche/ResultatRecherche.html.twig', [
        ]);
    }
}
