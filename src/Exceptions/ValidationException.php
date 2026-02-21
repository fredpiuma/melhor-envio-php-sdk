<?php

namespace MelhorEnvio\Exceptions;

class ValidationException extends MelhorEnvioException
{
    public function __construct(
        string $message,
        private array $errors = []
    ) {
        parent::__construct($message);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
