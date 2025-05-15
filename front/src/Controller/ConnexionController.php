<?php

namespace App\Controller;

use App\Service\ApiClientService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ConnexionController extends AbstractController
{
    public function __construct(
        private ApiClientService $apiClient
    ){}

    #[Route('/connexion', name: 'app_Connexion')]
    public function index(Request $request): Response
    {
        // Traitement du formulaire
        if ($request->isMethod('POST')) {
            $username = $request->request->get('identifiant');
            $password = $request->request->get('password');

            try {
                $result = $this->apiClient->login($username, $password);
                if(!$result['success']) {
                    if (!empty($result['data']['errors']) && is_array($result['data']['errors'])) {
                        foreach ($result['data']['errors'] as $error) {
                            $this->addFlash('error', $error);
                        }
                    } else {
                        $this->addFlash('error', $result['data']['message'] ?? 'Erreur inconnue lors de la connexion.');
                    }
                }
                return $this->redirectToRoute('app_accueil');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur lors de la connexion : ' . $e->getMessage());
            }
        }

        return $this->render('Connexion/Connexion.html.twig', [
            'controller_name' => 'ConnexionController',
        ]);
    }
}
