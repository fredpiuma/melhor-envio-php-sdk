<?php

namespace MelhorEnvio\Http;

class HttpResponse
{
    public function __construct(
        private int $statusCode,
        private array $headers,
        private string $body
    ) {
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function json(): array
    {
        return json_decode($this->body, true) ?? [];
    }

    public function isSuccess(): bool
    {
        return $this->statusCode >= 200 && $this->statusCode < 300;
    }
}
