<?php

namespace MelhorEnvio\Auth;

class InMemoryTokenProvider implements TokenProviderInterface
{
    private ?Token $token = null;

    public function __construct(
        private ?TokenProviderInterface $wrappedProvider = null
    ) {
    }

    public function setToken(Token $token): void
    {
        $token;
        $this->token = $token;
    }

    public function getAccessToken(?string $correlationId = null): Token
    {
        if ($this->token !== null && !$this->token->isExpired()) {
            return $this->token;
        }

        if ($this->wrappedProvider !== null) {
            $this->token = $this->wrappedProvider->getAccessToken($correlationId);
            return $this->token;
        }

        throw new \RuntimeException("No token available and no wrapped provider to fetch one.");
    }
}
