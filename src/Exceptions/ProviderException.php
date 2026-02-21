<?php

namespace MelhorEnvio\Exceptions;

class ProviderException extends MelhorEnvioException
{
    public function __construct(
        string $message,
        private int $httpStatus,
        private string $body,
        private ?array $json = null,
        ?string $correlationId = null,
        \Throwable $previous = null
    ) {
        parent::__construct($message, 0, $previous);
        $this->correlationId = $correlationId;
    }

    public function getHttpStatus(): int
    {
        return $this->httpStatus;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function getJson(): ?array
    {
        return $this->json;
    }
}
