<?php

namespace App\Controller;

use App\Service\ApiClientService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class PagePersoController extends AbstractController
{
    public function __construct(
        private ApiClientService $apiClient,
        private RequestStack $requestStack
    ){}

    #[Route('/pageperso/{username}', name: 'app_pageperso')]
    public function index(string $username): Response
    {
        if($username == null || $username == '') {
            return $this->redirectToRoute('app_accueil');
        }

        if($username == $this->requestStack->getSession()->get('username')) {
            return $this->redirectToRoute('app_Compte');
        }

        $user = $this->apiClient->getUserByUsername($username);
        if (!$user['success']) {
            return $this->redirectToRoute('app_accueil');
        }

        $userTweets = $this->apiClient->getTweetByUsername($username);
        if (!$userTweets['success']) {
            return $this->redirectToRoute('app_accueil');
        }

        $tweets = $userTweets['data'];
        $error = null;
        if (!$tweets) {
            $error = $userTweets['data']['error'] ?? 'Erreur inconnue';
        }
        $userData = $user['data'];


        return $this->render('PagePerso/PagePerso.html.twig', [
            'controller_name' => 'PagePersoController',
            'username' => $username,
            'userData' => $userData,
            'userTweets' => $tweets,
            'tweetError' => $error,
        ]);
    }
}
