<?php

namespace MelhorEnvio\Models;

use DateTimeImmutable;

class HistoricoRastreamento
{
    /**
     * @param EventoRastreamento[] $eventos
     */
    public function __construct(
        public string $codigo,
        public string $statusAtual,
        public ?DateTimeImmutable $dataUltimaAtualizacao = null,
        public array $eventos = []
    ) {
    }
}
