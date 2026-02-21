<?php

namespace MelhorEnvio\Models;

class Money
{
    public function __construct(
        private int $cents,
        private string $currency = 'BRL'
    ) {
    }

    public static function fromFloat(float $value, string $currency = 'BRL'): self
    {
        return new self((int) round($value * 100), $currency);
    }

    public function getCents(): int
    {
        return $this->cents;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function toFloat(): float
    {
        return $this->cents / 100;
    }

    public function __toString(): string
    {
        return number_format($this->toFloat(), 2, ',', '.') . ' ' . $this->currency;
    }
}
