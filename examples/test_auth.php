<?php

$sdk = require_once __DIR__ . '/bootstrap.php';

use MelhorEnvio\Auth\OAuthTokenProvider;
use MelhorEnvio\DTO\CalcularFreteDTO;
use MelhorEnvio\Exceptions\AuthException;
use MelhorEnvio\Exceptions\ProviderException;

try {
    echo "1. Tentando obter token de acesso via client_credentials...\n";
    
    // Pegando as dependências do bootstrap para testar o provider isolado
    $config = new \MelhorEnvio\Config\MelhorEnvioConfig(
        clientId: getenv('ME_CLIENT_ID'),
        clientSecret: getenv('ME_CLIENT_SECRET'),
        baseUrl: getenv('ME_BASE_URL')
    );
    $httpClient = new \MelhorEnvio\Http\CurlHttpClient();
    
    $authProvider = new OAuthTokenProvider($config, $httpClient);
    $token = $authProvider->getAccessToken('test-auth');
    
    echo "✅ Token obtido com sucesso!\n";
    echo "Token Type: " . $token->getTokenType() . "\n";
    
    echo "\n2. Testando chamada na API (/shipment/calculate)...\n";
    $dto = new CalcularFreteDTO(
        fromCep: '29050560',
        toCep: '01001000',
        packages: [['weightKg' => 0.1, 'heightCm' => 1, 'widthCm' => 1, 'lengthCm' => 1]]
    );
    
    $sdk->calcularFrete($dto);
    echo "✅ API respondeu com sucesso!\n";
    
} catch (AuthException $e) {
    echo "❌ Erro ao obter Token: " . $e->getMessage() . "\n";
    if ($e->getPrevious() instanceof ProviderException) {
        echo "Resposta do Provider: " . $e->getPrevious()->getBody() . "\n";
    }
} catch (ProviderException $e) {
    echo "❌ Erro na Chamada da API: " . $e->getMessage() . "\n";
    echo "Status: " . $e->getHttpStatus() . "\n";
    echo "Corpo: " . $e->getBody() . "\n";
} catch (\Exception $e) {
    echo "❌ Erro inesperado: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
