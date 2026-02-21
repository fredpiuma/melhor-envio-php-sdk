<?php

namespace MelhorEnvio\Models;

class PrazoEntrega
{
    public function __construct(
        private int $minDays,
        private ?int $maxDays = null,
        private bool $businessDays = true
    ) {
    }

    public function getMinDays(): int
    {
        return $this->minDays;
    }

    public function getMaxDays(): ?int
    {
        return $this->maxDays;
    }

    public function isBusinessDays(): bool
    {
        return $this->businessDays;
    }
}
