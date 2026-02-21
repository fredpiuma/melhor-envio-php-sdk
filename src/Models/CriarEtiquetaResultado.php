<?php

namespace MelhorEnvio\Models;

class CriarEtiquetaResultado
{
    public function __construct(
        public string $orderId,
        public ?string $protocol = null,
        public ?string $codigoRastreio = null,
        public ?Money $valor = null,
        public ?string $status = null,
        public ?string $urlEtiqueta = null,
        public ?array $rawProvider = null
    ) {
    }
}
