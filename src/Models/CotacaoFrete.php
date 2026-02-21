<?php

namespace MelhorEnvio\Models;

class CotacaoFrete
{
    public function __construct(
        public int|string $serviceId,
        public string $carrier,
        public string $serviceName,
        public Money $price,
        public PrazoEntrega $deliveryTime,
        public Money $insuranceValue,
        public bool $isAvailable = true,
        public array $observacoes = [],
        public array $metadata = []
    ) {
    }
}
