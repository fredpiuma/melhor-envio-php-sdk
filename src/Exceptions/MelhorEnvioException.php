<?php

namespace MelhorEnvio\Exceptions;

class MelhorEnvioException extends \Exception
{
    protected ?string $correlationId = null;

    public function setCorrelationId(?string $correlationId): self
    {
        $this->correlationId = $correlationId;
        return $this;
    }

    public function getCorrelationId(): ?string
    {
        return $this->correlationId;
    }
}
