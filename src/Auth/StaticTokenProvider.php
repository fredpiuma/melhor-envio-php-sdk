<?php

namespace MelhorEnvio\Auth;

class StaticTokenProvider implements TokenProviderInterface
{
    private Token $token;

    public function __construct(string $accessToken)
    {
        // Tokens pessoais geralmente não expiram rápido ou têm validade longa
        $this->token = new Token(
            $accessToken,
            'Bearer',
            new \DateTimeImmutable('+1 year')
        );
    }

    public function getAccessToken(?string $correlationId = null): Token
    {
        return $this->token;
    }
}
