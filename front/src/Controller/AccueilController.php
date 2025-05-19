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

    #[Route('/', name: 'homepage_redirect')]
    public function redirectToAccueil(): Response
    {
        return $this->redirectToRoute('app_accueil'); // ou 'accueil' selon ton nom de route
    }

    #[Route('/accueil', name: 'app_accueil')]
    public function index(): Response
    {
        $jwt = $this->requestStack->getSession()->get('jwt_token');
        $username = $this->requestStack->getSession()->get('username');
        // Optionnel : vérifie si c'est un token valide (ou si c'est juste une ancienne valeur)
        if (!$jwt || $jwt === '401' || $username == null) {
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
            'username' => $username,
        ]);
    }

    #[Route('/tweet', name: 'create_tweet', methods: ['POST'])]
    public function createTweet(Request $request): Response
    {
        $content = trim($request->request->get('content'));

        if (empty($content)) {
            $this->addFlash('error', 'Le tweet ne peut pas être vide.');
            return $this->redirectToRoute('app_Compte');
        }
        $result = $this->apiClient->createTweet($content);

        if (!$result['success']) {
            $message = $result['data']['errors'][0] ?? $result['message'] ?? 'Une erreur est survenue.';
            $this->addFlash('error', $message);
        } else {
            $this->addFlash('success', 'Tweet publié avec succès.');
        }

        return $this->redirectToRoute('app_accueil');
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

    #[Route('/like/{id}', name: 'like_tweet', methods: ['POST'])]
    public function like(int $id): Response
    {
        $result = $this->apiClient->likeTweet($id); // ✅ appel que tu as déjà

        if(!$result['success']) {
            // Option 1 : gérer l'erreur (afficher message, rediriger, etc.)
            $errorMessage = "Erreur lors de l'ajout du like : " . ($result['data']['message'] ?? 'Erreur inconnue');
            $this->addFlash('error', $errorMessage);
        } else {
            $this->addFlash('success', 'Tweet liké avec succès !');
        }

        // Option : Rediriger vers la page d’accueil avec un scroll ou une ancre
        return $this->redirectToRoute('app_accueil');
    }

    #[Route('/logout', name: 'logout', methods: ['POST'])]
    public function logout(): Response
    {
        // Déconnexion de l'utilisateur
        $this->requestStack->getSession()->remove('jwt_token');
        $this->requestStack->getSession()->remove('username');

        return $this->redirectToRoute('app_Connexion');
    }

}
