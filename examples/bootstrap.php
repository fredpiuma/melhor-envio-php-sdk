<?php

require_once __DIR__ . '/../vendor/autoload.php';

use MelhorEnvio\MelhorEnvio;
use MelhorEnvio\Config\MelhorEnvioConfig;
use MelhorEnvio\Http\CurlHttpClient;
use MelhorEnvio\Auth\OAuthTokenProvider;
use MelhorEnvio\Auth\InMemoryTokenProvider;

// Configurações (Substitua pelos seus dados do Sandbox)
$config = new MelhorEnvioConfig(
    clientId: 'SEU_CLIENT_ID',
    clientSecret: 'SEU_CLIENT_SECRET',
    baseUrl: MelhorEnvioConfig::BASE_URL_SANDBOX,
    debugRawProvider: true
);

$httpClient = new CurlHttpClient();
$authProvider = new OAuthTokenProvider($config, $httpClient);

// Opcional: Usar cache in-memory para não pedir token toda hora no mesmo processo
$tokenProvider = new InMemoryTokenProvider($authProvider);

$sdk = new MelhorEnvio($config, $httpClient, $tokenProvider);

return $sdk;
