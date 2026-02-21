<?php

namespace MelhorEnvio\Models;

class Address
{
    public function __construct(
        public string $name,
        public string $phone,
        public string $email,
        public string $document,
        public string $postalCode,
        public string $address,
        public string $number,
        public string $district,
        public string $city,
        public string $stateAbbr,
        public ?string $complement = null,
        public ?string $companyDocument = null,
        public ?string $stateRegister = null
    ) {
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email,
            'document' => $this->document,
            'company_document' => $this->companyDocument,
            'state_register' => $this->stateRegister,
            'postal_code' => $this->postalCode,
            'address' => $this->address,
            'number' => $this->number,
            'complement' => $this->complement,
            'district' => $this->district,
            'city' => $this->city,
            'state_abbr' => $this->stateAbbr,
            'country_id' => 'BR',
        ];
    }
}
