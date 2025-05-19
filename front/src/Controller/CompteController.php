<?php

namespace App\Controller;

use App\Service\ApiClientService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\Multipart\FormDataPart;
use Symfony\Component\Routing\Annotation\Route;

class CompteController extends AbstractController
{
    public function __construct(
        private ApiClientService $apiClient,
        private RequestStack $requestStack
    ){}

    #[Route('/Compte', name: 'app_Compte')]
    public function index(): Response
    {
        $username = $this->requestStack->getSession()->get('username');
        $userTweets = $this->apiClient->getTweetByUsername($username);

        // On ne passe que des tableaux de tweets à Twig
        $tweets = [];
        $error = null;

        if ($userTweets['success']) {
            $tweets = $userTweets['data'];
        } else {
            // On évite de passer la chaîne en tant que tweets
            $error = $userTweets['data']['error'] ?? 'Erreur inconnue';
        }

        return $this->render('Compte/Compte.html.twig', [
            'controller_name' => 'CompteController',
            'username' => $username,
            'userTweets' => $tweets,
            'tweetError' => $error,
        ]);
    }

    #[Route('/tweet/{id}', name: 'tweet_delete', methods: ['POST'])]
    public function deleteTweet(Request $request, int $id): Response
    {
        $result = $this->apiClient->deleteTweet($id);

        if ($result['success']) {
            $this->addFlash('success', 'Tweet supprimé avec succès.');
        } else {
            $this->addFlash('error', 'Erreur lors de la suppression : ' . ($result['data']['message'] ?? 'Erreur inconnue'));
        }

        return $this->redirectToRoute('app_Compte');
    }

    #[Route('/Compte/upload', name: 'upload_profile_picture', methods: ['POST'])]
    public function upload(Request $request): Response
    {
        $file = $request->files->get('profile_picture');

        // Limite taille max en octets (exemple 1 Mo)
        $maxFileSize = 1024 * 1024;

        if ($file->getSize() > $maxFileSize) {
            $this->addFlash('error', 'Le fichier est trop volumineux. Taille max autorisée : 1 Mo.');
            return $this->redirectToRoute('app_Compte');
        }

        if (!$file instanceof UploadedFile || $file->getError() !== UPLOAD_ERR_OK) {
            $this->addFlash('error', 'Erreur d\'envoi du fichier.');
            return $this->redirectToRoute('app_Compte');
        }

        $result = $this->apiClient->uploadFile(
            'http://localhost:8080/api/user/pp',
            $request->files->get('profile_picture')
        );
        //$result = $this->apiClient->uploadProfilePicture($file);

        if ($result['success']) {
            $this->addFlash('success', 'Image uploadée avec succès : ' . $result['data']['filename']);
        } else {
            $this->addFlash('error', 'Erreur : ' . ($result['data']['error'] ?? 'Erreur inconnue'));
        }
        return $this->redirectToRoute('app_Compte');
    }
}
