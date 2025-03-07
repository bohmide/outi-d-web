<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class StabilityAIService
{
    private $httpClient;
    private $apiKey;
    private $apiUrl = 'https://api.stability.ai/v1/generation/stable-diffusion-xl-1024-v1-0/text-to-image';

    public function __construct(
        HttpClientInterface $httpClient,
        ParameterBagInterface $params
    ) {
        $this->httpClient = $httpClient;
        $this->apiKey = $params->get('stability_ai_api_key');
        
        if (empty($this->apiKey)) {
            throw new \RuntimeException('La clé API Stability AI n\'est pas configurée');
        }
    }

    public function generateImage(string $prompt): ?string
    {
        try {
            error_log('Tentative de génération d\'image avec le prompt: ' . $prompt);
            
            $response = $this->httpClient->request('POST', $this->apiUrl, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'text_prompts' => [
                        [
                            'text' => $prompt,
                            'weight' => 1
                        ],
                        [
                            'text' => 'blurry, bad quality, distorted',
                            'weight' => -1
                        ]
                    ],
                    'cfg_scale' => 7,
                    'height' => 1024,
                    'width' => 1024,
                    'samples' => 1,
                    'steps' => 30,
                ]
            ]);

            $data = $response->toArray();
            error_log('Réponse de l\'API Stability AI: ' . json_encode($data));
            
            if (isset($data['artifacts'][0]['base64'])) {
                return $data['artifacts'][0]['base64'];
            }

            error_log('Aucune image générée dans la réponse de l\'API');
            return null;
        } catch (\Exception $e) {
            error_log('Erreur lors de la génération d\'image: ' . $e->getMessage());
            error_log('Trace: ' . $e->getTraceAsString());
            return null;
        }
    }
} 