<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class PagePersoController extends AbstractController
{
    #[Route('/pageperso', name: 'app_pageperso')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        
        return $this->render('PagePerso/PagePerso.html.twig', [
        ]);
    }
}
