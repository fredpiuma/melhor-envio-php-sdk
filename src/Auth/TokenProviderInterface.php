<?php

namespace MelhorEnvio\Auth;

interface TokenProviderInterface
{
    public function getAccessToken(?string $correlationId = null): Token;
}
