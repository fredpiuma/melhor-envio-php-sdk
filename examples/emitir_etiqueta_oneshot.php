<?php

$sdk = require_once __DIR__ . '/bootstrap.php';

use MelhorEnvio\DTO\EmitirEtiquetaDTO;
use MelhorEnvio\Models\Address;

$from = new Address(
    name: 'Loja Exemplo',
    phone: '27999999999',
    email: 'loja@exemplo.com',
    document: '12345678909',
    postalCode: '29050560',
    address: 'Rua das Flores',
    number: '100',
    district: 'Centro',
    city: 'Vitória',
    stateAbbr: 'ES'
);

$to = new Address(
    name: 'Cliente Final',
    phone: '11999999999',
    email: 'cliente@gmail.com',
    document: '98765432100',
    postalCode: '01311000',
    address: 'Av. Paulista',
    number: '1500',
    district: 'Bela Vista',
    city: 'São Paulo',
    stateAbbr: 'SP'
);

$dto = new EmitirEtiquetaDTO(
    serviceId: 1, // Ex: Correios SEDEX
    from: $from,
    to: $to,
    packages: [
        [
            'weightKg' => 1.0,
            'heightCm' => 15,
            'widthCm' => 15,
            'lengthCm' => 15,
            'insuranceValue' => 100.0
        ]
    ],
    identification: 'PEDIDO-SDK-001',
    idempotencyKey: 'idemp-' . uniqid()
);

try {
    echo "Emitindo etiqueta (One-shot)...
";
    $resultado = $sdk->emitirEtiqueta($dto);

    echo "Sucesso!
";
    echo "ID do Envio: {$resultado->orderId}
";
    echo "Protocolo: {$resultado->protocol}
";
    echo "Status: {$resultado->status}
";
    echo "URL da Etiqueta: {$resultado->urlEtiqueta}
";

} catch (\Exception $e) {
    echo "Erro: " . $e->getMessage() . "
";
}
