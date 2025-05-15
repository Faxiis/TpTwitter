<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class LogController extends AbstractController
{
    #[Route(
        path: ['/Log', '/Log/'], 
        name: 'app_log'
    )]
    public function index(EntityManagerInterface $entityManager): Response
    {
        return $this->render('Log/Log.html.twig');
    }
}

