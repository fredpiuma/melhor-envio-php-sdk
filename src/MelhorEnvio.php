<?php

namespace MelhorEnvio;

use MelhorEnvio\Auth\TokenProviderInterface;
use MelhorEnvio\Config\MelhorEnvioConfig;
use MelhorEnvio\Http\HttpClientInterface;
use MelhorEnvio\Support\CorrelationId;

use MelhorEnvio\DTO\CalcularFreteDTO;
use MelhorEnvio\Models\ResultadoCalculoFrete;
use MelhorEnvio\Models\CotacaoFrete;
use MelhorEnvio\Models\Money;
use MelhorEnvio\Models\PrazoEntrega;
use MelhorEnvio\Models\WarningIntegracao;
use MelhorEnvio\Models\ErroCalculoFrete;

use MelhorEnvio\DTO\CriarEnvioDTO;
use MelhorEnvio\Models\CriarEnvioResultado;
use MelhorEnvio\Models\CheckoutResultado;
use MelhorEnvio\Models\GerarEtiquetaResultado;
use MelhorEnvio\Models\ImprimirEtiquetaResultado;

use MelhorEnvio\DTO\EmitirEtiquetaDTO;
use MelhorEnvio\Models\CriarEtiquetaResultado;

use MelhorEnvio\Models\HistoricoRastreamento;
use MelhorEnvio\Models\EventoRastreamento;

class MelhorEnvio
{
    public function __construct(
        private MelhorEnvioConfig $config,
        private HttpClientInterface $httpClient,
        private TokenProviderInterface $tokenProvider
    ) {
    }

    public function obterHistoricoRastreamento(string $codigoDeRastreio, ?string $correlationId = null): HistoricoRastreamento
    {
        $correlationId ??= CorrelationId::generate();

        $response = $this->request(
            'GET',
            "/api/v2/me/shipment/tracking/{$codigoDeRastreio}",
            [],
            null,
            [],
            $correlationId
        );

        // O response geralmente é um array com os dados do rastreio
        // Dependendo da transportadora, o formato muda, mas tentaremos normalizar
        $eventos = [];
        $rawEventos = $response['tracking'] ?? [];

        foreach ($rawEventos as $ev) {
            $eventos[] = new EventoRastreamento(
                (string)($ev['status'] ?? ''),
                (string)($ev['message'] ?? $ev['description'] ?? ''),
                (string)($ev['location'] ?? ''),
                isset($ev['created_at']) ? new \DateTimeImmutable($ev['created_at']) : null,
                $ev
            );
        }

        return new HistoricoRastreamento(
            $codigoDeRastreio,
            (string)($response['status'] ?? 'unknown'),
            isset($response['updated_at']) ? new \DateTimeImmutable($response['updated_at']) : null,
            $eventos
        );
    }

    public function emitirEtiqueta(EmitirEtiquetaDTO $dto): CriarEtiquetaResultado
    {
        $correlationId = $dto->correlationId ?? CorrelationId::generate();

        // 1. Criar Envio
        $criarResultado = $this->criarEnvio($dto);

        // 2. Checkout
        $checkoutResultado = $this->checkout($criarResultado->orderId, $dto->idempotencyKey, $correlationId);

        // 3. Gerar
        $this->gerarEtiqueta($criarResultado->orderId, $correlationId);

        // 4. Obter URL (Aguardar se necessário)
        // O manual sugere sleep(5) nos exemplos
        if ($dto->autoRetry) {
            $retries = 0;
            while ($retries <= $dto->retryMax) {
                try {
                    sleep(2); // Wait a bit
                    $imprimirResultado = $this->obterUrlEtiqueta($criarResultado->orderId, $correlationId);
                    if ($imprimirResultado->urlEtiqueta) {
                        return new CriarEtiquetaResultado(
                            $criarResultado->orderId,
                            $checkoutResultado->protocol,
                            null, // codigoRastreio? (pode ser obtido via status)
                            null, // valor?
                            $checkoutResultado->status,
                            $imprimirResultado->urlEtiqueta,
                            $imprimirResultado->rawProvider
                        );
                    }
                } catch (\Exception $e) {
                    // Ignore and retry
                }
                $retries++;
            }
        }

        $imprimirResultado = $this->obterUrlEtiqueta($criarResultado->orderId, $correlationId);

        return new CriarEtiquetaResultado(
            $criarResultado->orderId,
            $checkoutResultado->protocol,
            null,
            null,
            $checkoutResultado->status,
            $imprimirResultado->urlEtiqueta,
            $imprimirResultado->rawProvider
        );
    }

    public function criarEnvio(CriarEnvioDTO $dto): CriarEnvioResultado
    {
        $dto->validate();
        $correlationId = $dto->correlationId ?? CorrelationId::generate();

        $response = $this->request(
            'POST',
            '/api/v2/me/cart',
            [],
            $dto->toArray(),
            [],
            $correlationId,
            $dto->idempotencyKey
        );

        return new CriarEnvioResultado(
            (string)$response['id'],
            (string)($response['status'] ?? 'pending'),
            $this->config->isDebugRawProvider() ? $response : null
        );
    }

    public function checkout(int|string $orderId, ?string $idempotencyKey = null, ?string $correlationId = null): CheckoutResultado
    {
        $correlationId ??= CorrelationId::generate();

        $response = $this->request(
            'POST',
            '/api/v2/me/shipment/checkout',
            [],
            ['orders' => [$orderId]],
            [],
            $correlationId,
            $idempotencyKey
        );

        return new CheckoutResultado(
            (string)($response['protocol'] ?? ''),
            (string)($response['status'] ?? 'pending'),
            $this->config->isDebugRawProvider() ? $response : null
        );
    }

    public function gerarEtiqueta(int|string $orderId, ?string $correlationId = null): GerarEtiquetaResultado
    {
        $correlationId ??= CorrelationId::generate();

        $response = $this->request(
            'POST',
            '/api/v2/me/shipment/generate',
            [],
            ['orders' => [$orderId]],
            [],
            $correlationId
        );

        return new GerarEtiquetaResultado(
            (string)($response['status'] ?? 'pending'),
            $this->config->isDebugRawProvider() ? $response : null
        );
    }

    public function obterUrlEtiqueta(int|string $orderId, ?string $correlationId = null): ImprimirEtiquetaResultado
    {
        $correlationId ??= CorrelationId::generate();

        $response = $this->request(
            'POST',
            '/api/v2/me/shipment/print',
            [],
            ['orders' => [$orderId]],
            [],
            $correlationId
        );

        return new ImprimirEtiquetaResultado(
            (string)($response['url'] ?? $response['data']['url'] ?? ''),
            $this->config->isDebugRawProvider() ? $response : null
        );
    }

    public function calcularFrete(CalcularFreteDTO $dto): ResultadoCalculoFrete
    {
        $dto->validate();
        $correlationId = $dto->correlationId ?? CorrelationId::generate();

        $response = $this->request(
            'POST',
            '/api/v2/me/shipment/calculate',
            [],
            $dto->toArray(),
            [],
            $correlationId,
            $dto->idempotencyKey
        );

        $cotacoes = [];
        $warnings = [];
        $erros = [];

        foreach ($response as $item) {
            if (isset($item['error'])) {
                $erros[] = new ErroCalculoFrete(
                    (string)($item['id'] ?? 'unknown'),
                    $item['error'],
                    $item['message'] ?? null
                );
                continue;
            }

            if (!isset($item['price'])) {
                continue;
            }

            $cotacao = new CotacaoFrete(
                $item['id'],
                $item['company']['name'] ?? 'Unknown',
                $item['name'] ?? 'Unknown',
                Money::fromFloat((float)$item['price']),
                new PrazoEntrega(
                    (int)($item['delivery_range']['min'] ?? $item['delivery_time'] ?? 0),
                    (int)($item['delivery_range']['max'] ?? $item['delivery_time'] ?? 0)
                ),
                Money::fromFloat((float)($item['insurance_value'] ?? 0)),
                !($item['error'] ?? false),
                [], // observacoes
                $item
            );

            $cotacoes[] = $cotacao;
        }

        // Encontrar melhor preço e melhor prazo
        $melhorPreco = null;
        $melhorPrazo = null;

        foreach ($cotacoes as $c) {
            if ($melhorPreco === null || $c->price->getCents() < $melhorPreco->price->getCents()) {
                $melhorPreco = $c;
            }
            if ($melhorPrazo === null || $c->deliveryTime->getMaxDays() < $melhorPrazo->deliveryTime->getMaxDays()) {
                $melhorPrazo = $c;
            }
        }

        return new ResultadoCalculoFrete(
            $dto->fromCep,
            $dto->toCep,
            $cotacoes,
            $melhorPreco,
            $melhorPrazo,
            $warnings,
            $erros,
            new \DateTimeImmutable(),
            $correlationId,
            $this->config->isDebugRawProvider() ? $response : null
        );
    }

    protected function request(
        string $method,
        string $path,
        array $query = [],
        ?array $body = null,
        array $headers = [],
        ?string $correlationId = null,
        ?string $idempotencyKey = null
    ): array {
        $correlationId ??= CorrelationId::generate();
        $token = $this->tokenProvider->getAccessToken($correlationId);

        $url = rtrim($this->config->getBaseUrl(), '/') . '/' . ltrim($path, '/');
        if (!empty($query)) {
            $url .= '?' . http_build_query($query);
        }

        $defaultHeaders = [
            'Authorization: Bearer ' . $token->getAccessToken(),
            'X-Correlation-Id: ' . $correlationId,
            'User-Agent: ' . $this->config->getUserAgent(),
        ];

        if ($idempotencyKey) {
            $defaultHeaders[] = 'Idempotency-Key: ' . $idempotencyKey;
        }

        $allHeaders = array_merge($defaultHeaders, $headers);

        if ($this->config->getLogger()) {
            $this->config->getLogger()->info("MelhorEnvio Request: {$method} {$url}", [
                'correlationId' => $correlationId,
                'headers' => $allHeaders,
                'body' => $body
            ]);
        }

        $response = $this->httpClient->request(
            $method,
            $url,
            $allHeaders,
            $body,
            $this->config->getTimeoutSeconds()
        );

        $data = $response->json();

        if ($this->config->getLogger()) {
            $this->config->getLogger()->info("MelhorEnvio Response: {$response->getStatusCode()}", [
                'correlationId' => $correlationId,
                'body' => $this->config->isDebugRawProvider() ? $response->getBody() : '...'
            ]);
        }

        return $data;
    }
}
