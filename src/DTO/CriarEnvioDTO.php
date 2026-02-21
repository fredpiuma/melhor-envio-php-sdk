<?php

namespace MelhorEnvio\DTO;

use MelhorEnvio\Exceptions\ValidationException;
use MelhorEnvio\Models\Address;

class CriarEnvioDTO
{
    public function __construct(
        public int|string $serviceId,
        public Address $from,
        public Address $to,
        public array $packages = [],
        public ?string $identification = null,
        public ?array $invoice = null,
        public ?array $options = null,
        public ?string $idempotencyKey = null,
        public ?string $correlationId = null
    ) {
    }

    public function validate(): void
    {
        $errors = [];

        if (empty($this->serviceId)) $errors['serviceId'] = 'Service ID is required';
        if (empty($this->packages)) $errors['packages'] = 'At least one package is required';

        if (!empty($errors)) {
            throw new ValidationException("Validation failed for CriarEnvioDTO", $errors);
        }
    }

    public function toArray(): array
    {
        $data = [
            'service' => $this->serviceId,
            'from' => $this->from->toArray(),
            'to' => $this->to->toArray(),
            'packages' => array_map(function ($p) {
                return [
                    'height' => $p['heightCm'],
                    'width' => $p['widthCm'],
                    'length' => $p['lengthCm'],
                    'weight' => $p['weightKg'],
                    'insurance_value' => $p['insuranceValue'] ?? 0,
                ];
            }, $this->packages),
        ];

        if ($this->identification) $data['identification'] = $this->identification;
        if ($this->invoice) $data['invoice'] = $this->invoice;
        if ($this->options) $data['options'] = $this->options;

        return $data;
    }
}
