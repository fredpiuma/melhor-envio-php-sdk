<?php

namespace MelhorEnvio\Models;

class CriarEnvioResultado
{
    public function __construct(
        public string $orderId,
        public string $status,
        public ?array $rawProvider = null
    ) {
    }
}
