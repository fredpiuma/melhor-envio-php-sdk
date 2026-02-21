<?php

namespace MelhorEnvio\Models;

class ErroCalculoFrete
{
    public function __construct(
        public string $serviceId,
        public string $error,
        public ?string $message = null
    ) {
    }
}
