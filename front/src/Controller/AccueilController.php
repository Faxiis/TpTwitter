<?php

namespace App\Controller;

use App\Service\ApiClientService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class AccueilController extends AbstractController
{
    public function __construct(
        private ApiClientService $apiClient,
        private RequestStack $requestStack
    ){}

    #[Route('/accueil', name: 'app_accueil')]
    public function index(): Response
    {
        $jwt = $this->requestStack->getSession()->get('jwt_token');
        // Optionnel : vérifie si c'est un token valide (ou si c'est juste une ancienne valeur)
        if (!$jwt || $jwt === '401') {
            return $this->redirectToRoute('app_Connexion');
        }

        $result = $this->apiClient->getTweets();

        if (!$result['success']) {
            // Option 1 : gérer l'erreur (afficher message, rediriger, etc.)
            $tweets = [];
            $errorMessage = "Erreur lors de la récupération des tweets : " . ($result['data']['message'] ?? 'Erreur inconnue');
        } else {
            $tweets = $result['data'];
            $errorMessage = null;
        }

        $users = $this->apiClient->get4Users();

        return $this->render('Accueil/Accueil.html.twig', [
            'tweets' => $tweets,
            'errorMessage' => $errorMessage,
            'users' => $users['data'],
        ]);
    }

    #[Route('/search', name: 'search')]
    public function search(Request $request): Response
    {
        $query = $request->query->get('research');

        // Appel API (par ex. via $this->apiClient)
        $result = $this->apiClient->searchTweet($query);

        if (!$result['success']) {
            // Option 1 : gérer l'erreur (afficher message, rediriger, etc.)
            $tweets = [];
            $errorMessage = "Erreur lors de la récupération des tweets : " . ($result['data']['message'] ?? 'Erreur inconnue');
        } else {
            $tweets = $result['data'];
            $errorMessage = null;
        }

        $users = $this->apiClient->get4Users();

        return $this->render('Accueil/Accueil.html.twig', [
            'tweets' => $tweets,
            'errorMessage' => $errorMessage,
            'users' => $users['data'],
        ]);
    }
}
