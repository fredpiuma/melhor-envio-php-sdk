<?php

require_once __DIR__ . '/../vendor/autoload.php';

use MelhorEnvio\MelhorEnvio;
use MelhorEnvio\Config\MelhorEnvioConfig;
use MelhorEnvio\Http\CurlHttpClient;
use MelhorEnvio\Auth\OAuthTokenProvider;
use MelhorEnvio\Auth\InMemoryTokenProvider;

// Simples carregador de .env para os exemplos
if (file_exists(__DIR__ . '/../.env')) {
    $lines = file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($name, $value) = explode('=', $line, 2);
        putenv(sprintf('%s=%s', trim($name), trim($value)));
    }
}

// Configurações
$config = new MelhorEnvioConfig(
    clientId: getenv('ME_CLIENT_ID') ?: 'SEU_CLIENT_ID',
    clientSecret: getenv('ME_CLIENT_SECRET') ?: 'SEU_CLIENT_SECRET',
    baseUrl: getenv('ME_BASE_URL') ?: MelhorEnvioConfig::BASE_URL_SANDBOX,
    debugRawProvider: getenv('ME_DEBUG') === 'true'
);

$httpClient = new CurlHttpClient();

// Se houver um token estático (Pessoal), usa o StaticTokenProvider
if ($staticToken = getenv('ME_TOKEN')) {
    $tokenProvider = new \MelhorEnvio\Auth\StaticTokenProvider($staticToken);
} else {
    $authProvider = new OAuthTokenProvider($config, $httpClient);
    // Opcional: Usar cache in-memory para não pedir token toda hora no mesmo processo
    $tokenProvider = new InMemoryTokenProvider($authProvider);
}

$sdk = new MelhorEnvio($config, $httpClient, $tokenProvider);

return $sdk;
