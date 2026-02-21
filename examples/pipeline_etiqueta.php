<?php

$sdk = require_once __DIR__ . '/bootstrap.php';

use MelhorEnvio\DTO\CriarEnvioDTO;
use MelhorEnvio\Models\Address;

// ... (Reusar endereços do exemplo anterior para brevidade no exemplo real)
$from = new Address('Loja', '27...', 'loja@...', '...', '29050560', 'Rua A', '100', 'Bairro', 'Vitória', 'ES');
$to = new Address('Cliente', '11...', 'cli@...', '...', '01311000', 'Av B', '200', 'Bairro', 'São Paulo', 'SP');

$dto = new CriarEnvioDTO(1, $from, $to, [['weightKg' => 1, 'heightCm' => 10, 'widthCm' => 10, 'lengthCm' => 10]]);

try {
    echo "1. Criando envio no carrinho...
";
    $criar = $sdk->criarEnvio($dto);
    echo "ID: {$criar->orderId}

";

    echo "2. Realizando checkout...
";
    $checkout = $sdk->checkout($criar->orderId);
    echo "Protocolo: {$checkout->protocol}

";

    echo "3. Gerando etiqueta...
";
    $gerar = $sdk->gerarEtiqueta($criar->orderId);
    echo "Status: {$gerar->status}

";

    echo "4. Aguardando processamento (5s)...
";
    sleep(5);

    echo "5. Obtendo URL da etiqueta...
";
    $imprimir = $sdk->obterUrlEtiqueta($criar->orderId);
    echo "URL: {$imprimir->urlEtiqueta}
";

} catch (\Exception $e) {
    echo "Erro: " . $e->getMessage() . "
";
}
