<?php

$sdk = require_once __DIR__ . '/bootstrap.php';

$codigoRastreio = 'RE123456789BR'; // Exemplo fictício

try {
    echo "Rastreando código: {$codigoRastreio}
";
    $historico = $sdk->obterHistoricoRastreamento($codigoRastreio);

    echo "Status Atual: {$historico->statusAtual}
";
    echo "Última Atualização: " . ($historico->dataUltimaAtualizacao?->format('d/m/Y H:i') ?? 'N/A') . "
";
    echo "--------------------------------------------------
";

    foreach ($historico->eventos as $evento) {
        echo "[" . ($evento->occurredAt?->format('d/m/Y H:i') ?? 'N/A') . "] ";
        echo "{$evento->statusProvider}: {$evento->description}
";
        if ($evento->location) echo "Local: {$evento->location}
";
        echo "--------------------------------------------------
";
    }

} catch (\Exception $e) {
    echo "Erro: " . $e->getMessage() . "
";
}
