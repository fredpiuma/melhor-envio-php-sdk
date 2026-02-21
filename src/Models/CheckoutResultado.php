<?php

namespace MelhorEnvio\Models;

class CheckoutResultado
{
    public function __construct(
        public string $protocol,
        public string $status,
        public ?array $rawProvider = null
    ) {
    }
}
