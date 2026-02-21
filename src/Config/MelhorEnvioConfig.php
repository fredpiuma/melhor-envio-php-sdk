<?php

namespace MelhorEnvio\Config;

use Psr\Log\LoggerInterface;

class MelhorEnvioConfig
{
    public const BASE_URL_SANDBOX = 'https://sandbox.melhorenvio.com.br';
    public const BASE_URL_PRODUCTION = 'https://melhorenvio.com.br';

    public function __construct(
        private string $clientId,
        private string $clientSecret,
        private string $baseUrl = self::BASE_URL_SANDBOX,
        private string $redirectUri = '',
        private string $userAgent = 'FulfillmentSDK/1.0',
        private int $timeoutSeconds = 20,
        private bool $debugRawProvider = false,
        private ?LoggerInterface $logger = null
    ) {
    }

    public function getClientId(): string
    {
        return $this->clientId;
    }

    public function getClientSecret(): string
    {
        return $this->clientSecret;
    }

    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    public function getRedirectUri(): string
    {
        return $this->redirectUri;
    }

    public function getUserAgent(): string
    {
        return $this->userAgent;
    }

    public function getTimeoutSeconds(): int
    {
        return $this->timeoutSeconds;
    }

    public function isDebugRawProvider(): bool
    {
        return $this->debugRawProvider;
    }

    public function getLogger(): ?LoggerInterface
    {
        return $this->logger;
    }
}
