<?php

namespace MelhorEnvio\DTO;

use MelhorEnvio\Exceptions\ValidationException;

class CalcularFreteDTO
{
    public function __construct(
        public string $fromCep,
        public string $toCep,
        public array $packages = [],
        public ?array $services = null,
        public array $options = [],
        public ?string $idempotencyKey = null,
        public ?string $correlationId = null
    ) {
    }

    public function validate(): void
    {
        $errors = [];

        if (empty($this->fromCep)) {
            $errors['fromCep'] = 'From CEP is required';
        }

        if (empty($this->toCep)) {
            $errors['toCep'] = 'To CEP is required';
        }

        if (empty($this->packages)) {
            $errors['packages'] = 'At least one package is required';
        } else {
            foreach ($this->packages as $index => $package) {
                if (!isset($package['weightKg'])) $errors["packages.{$index}.weightKg"] = 'Weight is required';
                if (!isset($package['heightCm'])) $errors["packages.{$index}.heightCm"] = 'Height is required';
                if (!isset($package['widthCm'])) $errors["packages.{$index}.widthCm"] = 'Width is required';
                if (!isset($package['lengthCm'])) $errors["packages.{$index}.lengthCm"] = 'Length is required';
            }
        }

        if (!empty($errors)) {
            throw new ValidationException("Validation failed for CalcularFreteDTO", $errors);
        }
    }

    public function toArray(): array
    {
        $data = [
            'from' => ['postal_code' => $this->fromCep],
            'to' => ['postal_code' => $this->toCep],
            'packages' => array_map(function ($p) {
                return [
                    'weight' => $p['weightKg'],
                    'height' => $p['heightCm'],
                    'width' => $p['widthCm'],
                    'length' => $p['lengthCm'],
                    'insurance_value' => $p['insuranceValue'] ?? 0,
                ];
            }, $this->packages),
        ];

        if ($this->services !== null) {
            $data['services'] = implode(',', $this->services);
        }

        if (!empty($this->options)) {
            $data['options'] = [
                'receipt' => $this->options['receipt'] ?? false,
                'own_hand' => $this->options['ownHand'] ?? false,
                'collect' => $this->options['collect'] ?? false,
            ];
        }

        return $data;
    }
}
