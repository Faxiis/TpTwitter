<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ApiClientService
{
    public function __construct(
        private HttpClientInterface $client,
        private RequestStack $requestStack
    ) {}

    public function login(string $username, string $password): array
    {
        $response = $this->client->request('POST', 'http://localhost:8080/api/login', [
            'json' => [
                'username' => $username,
                'password' => $password,
            ],
        ]);

        $data = $response->toArray();

        // On stocke le token dans la session
        $this->requestStack->getSession()->set('jwt_token', $data['token'] ?? null);

        return $data;
    }

    public function register(string $username, string $password): array
    {
        try {
            $response = $this->client->request('POST', 'http://localhost:8080/api/register', [
                'json' => [
                    'username' => $username,
                    'password' => $password,
                ]
            ]);

            // Si tout va bien
            return [
                'success' => true,
                'data' => $response->toArray(),
            ];
        } catch (\Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface $e) {
            // Exception pour codes 4xx (comme 400)
            $response = $e->getResponse();
            $content = $response->getContent(false); // Ne déclenche pas d'exception sur erreur HTTP
            $data = json_decode($content, true);

            return [
                'success' => false,
                'data' => $data,
                'status' => $response->getStatusCode(),
            ];
        }
    }
}