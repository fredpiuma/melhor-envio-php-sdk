<?php

namespace MelhorEnvio\Auth;

use DateTimeImmutable;
use MelhorEnvio\Config\MelhorEnvioConfig;
use MelhorEnvio\Exceptions\AuthException;
use MelhorEnvio\Http\HttpClientInterface;

class OAuthTokenProvider implements TokenProviderInterface
{
    public function __construct(
        private MelhorEnvioConfig $config,
        private HttpClientInterface $httpClient
    ) {
    }

    public function getAccessToken(?string $correlationId = null): Token
    {
        $url = $this->config->getBaseUrl() . '/oauth/token';

        $payload = [
            'grant_type' => 'client_credentials',
            'client_id' => $this->config->getClientId(),
            'client_secret' => $this->config->getClientSecret(),
            'scope' => '', // define default scopes if needed
        ];

        try {
            $response = $this->httpClient->request(
                'POST',
                $url,
                ['X-Correlation-Id' => $correlationId],
                $payload
            );

            $data = $response->json();

            if (!isset($data['access_token'])) {
                throw new AuthException("Invalid response from OAuth token endpoint: " . $response->getBody());
            }

            return new Token(
                $data['access_token'],
                $data['token_type'] ?? 'Bearer',
                new DateTimeImmutable('+' . ($data['expires_in'] ?? 3600) . ' seconds'),
                $data['refresh_token'] ?? null
            );
        } catch (\Exception $e) {
            throw new AuthException("Failed to obtain access token: " . $e->getMessage(), 0, $e);
        }
    }
}
