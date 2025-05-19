<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\Multipart\FormDataPart;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ApiClientService
{
    public function __construct(
        private HttpClientInterface $client,
        private RequestStack $requestStack,
    ) {}

    public function decodeJwtPayload(string $jwt): ?array
    {
        $parts = explode('.', $jwt);
        if (count($parts) !== 3) {
            return null; // JWT invalide
        }

        $payload = $parts[1];
        // Base64URL decode (remplace les caractères et ajoute le padding)
        $payload = strtr($payload, '-_', '+/');
        $padding = strlen($payload) % 4;
        if ($padding > 0) {
            $payload .= str_repeat('=', 4 - $padding);
        }

        $json = base64_decode($payload);
        if ($json === false) {
            return null;
        }

        return json_decode($json, true);
    }

    public function login(string $username, string $password): array
    {
        try {
            $response = $this->client->request('POST', 'http://localhost:8080/api/login', [
                'json' => [
                    'username' => $username,
                    'password' => $password,
                ],
            ]);

            $data = $response->toArray(false);

            if (!empty($data['token'])) {
                // Stocker le token seulement s’il est présent
                $this->requestStack->getSession()->set('jwt_token', $data['token']);
                // Décoder le payload du JWT pour récupérer l'ID et le nom d'utilisateur
                $payload = $this->decodeJwtPayload($data['token']);
                if ($payload) {
                    if (isset($payload['username'])) {
                        $this->requestStack->getSession()->set('username', $payload['username']);
                    }
                }

                return [
                    'success' => true,
                    'data' => $data,
                ];
            }

            if (isset($data['message']) && $data['message'] === 'Invalid credentials.') {
                $data['message'] = 'Identifiant ou mot de passe incorrect.';
                return [
                    'success' => false,
                    'data' => $data,
                ];
            }else{
                return [
                    'success' => false,
                    'data' => $data,
                ];
            }

        } catch (\Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface $e) {
            $response = $e->getResponse();
            $content = $response->getContent(false);
            $data = json_decode($content, true);

            return [
                'success' => false,
                'data' => $data,
                'status' => $response->getStatusCode(),
            ];
        }
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

            return [
                'success' => true,
                'data' => $response->toArray(),
            ];
        } catch (\Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface $e) {
            $response = $e->getResponse();
            $content = $response->getContent(false);
            $data = json_decode($content, true);

            return [
                'success' => false,
                'data' => $data,
                'status' => $response->getStatusCode(),
            ];
        }
    }

    public function getTweets(): array
    {
        try {
            $response = $this->client->request('GET', 'http://localhost:8080/api/tweet', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->requestStack->getSession()->get('jwt_token'),
                ],
            ]);

            return [
                'success' => true,
                'data' => $response->toArray(),
            ];
        } catch (\Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface $e) {
            $response = $e->getResponse();
            $content = $response->getContent(false);
            $data = json_decode($content, true);

            return [
                'success' => false,
                'data' => $data,
                'status' => $response->getStatusCode(),
            ];
        }
    }

    public function get4Users(): array
    {
        try {
            $response = $this->client->request('GET', 'http://localhost:8080/api/user', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->requestStack->getSession()->get('jwt_token'),
                ],
            ]);

            return [
                'success' => true,
                'data' => $response->toArray(),
            ];
        } catch (\Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface $e) {
            $response = $e->getResponse();
            $content = $response->getContent(false);
            $data = json_decode($content, true);

            return [
                'success' => false,
                'data' => $data,
                'status' => $response->getStatusCode(),
            ];
        }
    }

    public function searchTweet(string $search): array
    {
        try {
            $response = $this->client->request('GET', 'http://localhost:8080/api/tweet/search/' . $search, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->requestStack->getSession()->get('jwt_token'),
                ],
            ]);

            return [
                'success' => true,
                'data' => $response->toArray(),
            ];
        } catch (\Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface $e) {
            $response = $e->getResponse();
            $content = $response->getContent(false);
            $data = json_decode($content, true);

            return [
                'success' => false,
                'data' => $data,
                'status' => $response->getStatusCode(),
            ];
        }
    }

    public function likeTweet(int $tweetId): array
    {
        try {
            $response = $this->client->request('POST', 'http://localhost:8080/api/tweet/like/' . $tweetId, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->requestStack->getSession()->get('jwt_token'),
                ],
            ]);

            return [
                'success' => true,
                'data' => $response->toArray(),
            ];
        } catch (\Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface $e) {
            $response = $e->getResponse();
            $content = $response->getContent(false);
            $data = json_decode($content, true);

            return [
                'success' => false,
                'data' => $data,
                'status' => $response->getStatusCode(),
            ];
        }
    }

    public function getTweetByUsername(): array{
        try {
            $response = $this->client->request('GET', 'http://localhost:8080/api/tweet/user/' . $this->requestStack->getSession()->get('username'), [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->requestStack->getSession()->get('jwt_token'),
                ],
            ]);

            return [
                'success' => true,
                'data' => $response->toArray(),
            ];
        } catch (\Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface $e) {
            $response = $e->getResponse();
            $content = $response->getContent(false);
            $data = json_decode($content, true);

            return [
                'success' => false,
                'data' => $data,
                'status' => $response->getStatusCode(),
            ];
        }
    }

    public function createTweet(string $content): array
    {
        $jwt = $this->requestStack->getSession()->get('jwt_token');

        if (!$jwt) {
            return [
                'success' => false,
                'message' => 'Utilisateur non authentifié.',
            ];
        }

        try {
            $response = $this->client->request('POST', 'http://localhost:8080/api/tweet', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $jwt,
                ],
                'json' => [
                    'content' => $content,
                ],
            ]);

            $data = $response->toArray(false);

            return [
                'success' => true,
                'data' => $data,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Erreur inattendue : ' . $e->getMessage(),
            ];
        }
    }

    public function deleteTweet(int $id){
        $jwt = $this->requestStack->getSession()->get('jwt_token');

        if (!$jwt) {
            return [
                'success' => false,
                'message' => 'Utilisateur non authentifié.',
            ];
        }

        try {
            $response = $this->client->request('DELETE', 'http://localhost:8080/api/tweet/' . $id, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $jwt,
                ],
            ]);

            $data = $response->toArray(false);

            return [
                'success' => true,
                'data' => $data,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Erreur inattendue : ' . $e->getMessage(),
            ];
        }
    }


    public function uploadProfilePicture(\Symfony\Component\HttpFoundation\File\UploadedFile $file): array
    {
        $boundary = uniqid();
        $eol = "\r\n";

        $filename = $file->getClientOriginalName();
        $mimeType = $file->getMimeType();
        $fileContents = file_get_contents($file->getPathname());

        dd($filename, $mimeType, $fileContents, $file->getPathname(), $file->getClientOriginalExtension(), $file->getSize(), $file->getError());

        $body =
            "--$boundary$eol" .
            "Content-Disposition: form-data; name=\"profile_picture\"; filename=\"$filename\"$eol" .
            "Content-Type: $mimeType$eol$eol" .
            $fileContents . $eol .
            "--$boundary--$eol";

        $headers = [
            'Authorization' => 'Bearer ' . $this->requestStack->getSession()->get('jwt_token'),
            'Content-Type' => 'multipart/form-data; boundary=' . $boundary,
        ];

        try {
            $response = $this->client->request('POST', 'http://localhost:8080/api/user/pp', [
                'headers' => $headers,
                'body' => $body,
            ]);

            return [
                'success' => true,
                'data' => $response->toArray(),
            ];
        } catch (\Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface $e) {
            $response = $e->getResponse();
            $content = $response->getContent(false);
            $data = json_decode($content, true);

            return [
                'success' => false,
                'data' => $data,
                'status' => $response->getStatusCode(),
            ];
        }
    }

    public function uploadFile(string $url, UploadedFile $file): array
    {
        $formData = new FormDataPart([
            'profile_picture' => DataPart::fromPath($file->getPathname(), $file->getClientOriginalName()),
        ]);

        $headers = array_merge([
            'Authorization' => 'Bearer ' . $this->requestStack->getSession()->get('jwt_token'),
        ], $formData->getPreparedHeaders()->toArray());

        try {
            $response = $this->client->request('POST', $url, [
                'headers' => $headers,
                'body' => $formData->bodyToIterable(),
            ]);

            return [
                'success' => true,
                'data' => $response->toArray(),
                'status' => $response->getStatusCode(),
            ];
        } catch (ClientExceptionInterface $e) {
            $response = $e->getResponse();
            $content = $response->getContent(false);
            $data = json_decode($content, true);

            return [
                'success' => false,
                'data' => $data,
                'status' => $response->getStatusCode(),
            ];
        }
    }

}