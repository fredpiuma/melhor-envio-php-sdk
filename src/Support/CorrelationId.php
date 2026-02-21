<?php

namespace MelhorEnvio\Support;

class CorrelationId
{
    public static function generate(): string
    {
        return bin2hex(random_bytes(16));
    }
}
