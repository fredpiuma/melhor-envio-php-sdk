<?php

namespace MelhorEnvio\Models;

class ImprimirEtiquetaResultado
{
    public function __construct(
        public string $urlEtiqueta,
        public ?array $rawProvider = null
    ) {
    }
}
