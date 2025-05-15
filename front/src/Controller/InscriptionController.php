<?php

namespace App\Controller;

use App\Service\ApiClientService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InscriptionController extends AbstractController
{
    public function __construct(
        private ApiClientService $apiClient
    ){}

    #[Route('/inscription', name: 'app_Inscription')]
    public function index(Request $request): Response
    {
        // Traitement du formulaire
        if ($request->isMethod('POST')) {
            $username = $request->request->get('identifiant');
            $password = $request->request->get('password');
            $confirmation = $request->request->get('confirmation');

            // Vérification basique
            if ($password !== $confirmation) {
                $this->addFlash('error', 'Les mots de passe ne correspondent pas.');
            } else {
                try {
                    $result = $this->apiClient->register($username, $password);
                    if(!$result['success']) {
                        if (!empty($result['data']['errors']) && is_array($result['data']['errors'])) {
                            foreach ($result['data']['errors'] as $error) {
                                $this->addFlash('error', $error);
                            }
                        } else {
                            $this->addFlash('error', $result['data']['message'] ?? 'Erreur inconnue lors de l’inscription.');
                        }
                    }
                    $this->addFlash('success', 'Inscription réussie, veuillez vous connecter.');
                    return $this->redirectToRoute('app_Connexion');
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Erreur lors de l\'inscription : ' . $e->getMessage());
                }
            }
        }

        return $this->render('Inscription/inscription.html.twig', [
            'controller_name' => 'InscriptionController',
        ]);
    }
}
