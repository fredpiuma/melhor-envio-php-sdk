<?php

namespace MelhorEnvio\Models;

use DateTimeImmutable;

class ResultadoCalculoFrete
{
    /**
     * @param CotacaoFrete[] $cotacoes
     * @param WarningIntegracao[] $warnings
     * @param ErroCalculoFrete[] $erros
     */
    public function __construct(
        public string $fromCep,
        public string $toCep,
        public array $cotacoes = [],
        public ?CotacaoFrete $melhorPreco = null,
        public ?CotacaoFrete $melhorPrazo = null,
        public array $warnings = [],
        public array $erros = [],
        public ?DateTimeImmutable $calculadoEm = null,
        public ?string $correlationId = null,
        public ?array $rawProvider = null
    ) {
        $this->calculadoEm ??= new DateTimeImmutable();
    }
}
