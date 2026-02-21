<?php

$sdk = require_once __DIR__ . '/bootstrap.php';

use MelhorEnvio\DTO\CalcularFreteDTO;

$dto = new CalcularFreteDTO(
    fromCep: '29050560',
    toCep: '01001000',
    packages: [
        [
            'weightKg' => 0.5,
            'heightCm' => 10,
            'widthCm' => 20,
            'lengthCm' => 15,
            'insuranceValue' => 100.0
        ]
    ],
    services: [1, 2] // Correios PAC e SEDEX (IDs fictícios para exemplo)
);

try {
    $resultado = $sdk->calcularFrete($dto);

    echo "Cálculo de Frete:
";
    echo "De: {$resultado->fromCep} Para: {$resultado->toCep}
";
    echo "--------------------------------------------------
";

    foreach ($resultado->cotacoes as $cotacao) {
        echo "Transportadora: {$cotacao->carrier}
";
        echo "Serviço: {$cotacao->serviceName}
";
        echo "Preço: {$cotacao->price}
";
        echo "Prazo: {$cotacao->deliveryTime->getMinDays()} a {$cotacao->deliveryTime->getMaxDays()} dias
";
        echo "--------------------------------------------------
";
    }

    if ($resultado->melhorPreco) {
        echo "Melhor Preço: {$resultado->melhorPreco->carrier} - {$resultado->melhorPreco->price}
";
    }

    if ($resultado->melhorPrazo) {
        echo "Melhor Prazo: {$resultado->melhorPrazo->carrier} - {$resultado->melhorPrazo->deliveryTime->getMaxDays()} dias
";
    }

} catch (\Exception $e) {
    echo "Erro: " . $e->getMessage() . "
";
    if (method_exists($e, 'getCorrelationId')) {
        echo "Correlation ID: " . $e->getCorrelationId() . "
";
    }
}
