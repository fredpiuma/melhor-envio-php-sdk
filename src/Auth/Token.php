<?php

namespace MelhorEnvio\Auth;

use DateTimeImmutable;

class Token
{
    public function __construct(
        private string $accessToken,
        private string $tokenType,
        private DateTimeImmutable $expiresAt,
        private ?string $refreshToken = null
    ) {
    }

    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    public function getTokenType(): string
    {
        return $this->tokenType;
    }

    public function getExpiresAt(): DateTimeImmutable
    {
        return $this->expiresAt;
    }

    public function getRefreshToken(): ?string
    {
        return $this->refreshToken;
    }

    public function isExpired(int $leewaySeconds = 30): bool
    {
        return $this->expiresAt->getTimestamp() <= (time() + $leewaySeconds);
    }
}
