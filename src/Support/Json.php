<?php

namespace MelhorEnvio\Support;

class Json
{
    public static function encode(mixed $value): string
    {
        $json = json_encode($value, JSON_THROW_ON_ERROR);
        return $json;
    }

    public static function decode(string $json, bool $associative = true): mixed
    {
        return json_decode($json, $associative, 512, JSON_THROW_ON_ERROR);
    }
}
