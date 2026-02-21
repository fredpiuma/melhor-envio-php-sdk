<?php

namespace MelhorEnvio\Models;

class GerarEtiquetaResultado
{
    public function __construct(
        public string $status,
        public ?array $rawProvider = null
    ) {
    }
}
