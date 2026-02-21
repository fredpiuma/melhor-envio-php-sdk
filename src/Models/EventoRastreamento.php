<?php

namespace MelhorEnvio\Models;

use DateTimeImmutable;

class EventoRastreamento
{
    public function __construct(
        public string $statusProvider,
        public string $description,
        public ?string $location = null,
        public ?DateTimeImmutable $occurredAt = null,
        public ?array $raw = null
    ) {
    }
}
