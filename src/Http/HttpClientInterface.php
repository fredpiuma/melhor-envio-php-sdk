<?php

namespace MelhorEnvio\Http;

interface HttpClientInterface
{
    public function request(
        string $method,
        string $url,
        array $headers = [],
        ?array $body = [],
        int $timeout = 20
    ): HttpResponse;
}
