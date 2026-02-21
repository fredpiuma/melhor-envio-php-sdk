<?php

namespace MelhorEnvio\Models;

class WarningIntegracao
{
    public function __construct(
        public string $code,
        public string $message
    ) {
    }
}
