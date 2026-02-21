<?php

namespace MelhorEnvio\DTO;

class EmitirEtiquetaDTO extends CriarEnvioDTO
{
    public function __construct(
        int|string $serviceId,
        \MelhorEnvio\Models\Address $from,
        \MelhorEnvio\Models\Address $to,
        array $packages = [],
        ?string $identification = null,
        ?array $invoice = null,
        ?array $options = null,
        ?string $idempotencyKey = null,
        ?string $correlationId = null,
        public bool $autoRetry = true,
        public int $retryMax = 2
    ) {
        parent::__construct(
            $serviceId,
            $from,
            $to,
            $packages,
            $identification,
            $invoice,
            $options,
            $idempotencyKey,
            $correlationId
        );
    }
}
