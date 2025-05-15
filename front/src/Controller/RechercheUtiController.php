<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class RechercheUtiController extends AbstractController
{
    #[Route('/rechercheuti', name: 'app_rechercheUti')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        
        return $this->render('RechercheUti/RechercheUti.html.twig', [
        ]);
    }
}
