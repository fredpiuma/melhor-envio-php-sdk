<?php

namespace MelhorEnvio\Http;

use MelhorEnvio\Exceptions\NetworkException;
use MelhorEnvio\Exceptions\ProviderException;

class CurlHttpClient implements HttpClientInterface
{
    public function request(
        string $method,
        string $url,
        array $headers = [],
        ?array $body = [],
        int $timeout = 20
    ): HttpResponse {
        $ch = curl_init();

        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Accept: application/json';

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_HEADER, true);

        if ($body !== null && !empty($body)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
        }

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new NetworkException("cURL Error: {$error}");
        }

        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $headerStr = substr($response, 0, $headerSize);
        $bodyStr = substr($response, $headerSize);

        $parsedHeaders = $this->parseHeaders($headerStr);
        $httpResponse = new HttpResponse($statusCode, $parsedHeaders, $bodyStr);

        if (!$httpResponse->isSuccess()) {
            $correlationId = $parsedHeaders['X-Correlation-Id'] ?? $parsedHeaders['x-correlation-id'] ?? null;
            throw new ProviderException(
                "Request failed with status {$statusCode}",
                $statusCode,
                $bodyStr,
                $httpResponse->json(),
                $correlationId
            );
        }

        return $httpResponse;
    }

    private function parseHeaders(string $headerStr): array
    {
        $headers = [];
        foreach (explode("
", $headerStr) as $i => $line) {
            if ($i === 0 || empty($line)) {
                continue;
            }
            if (str_contains($line, ':')) {
                list($key, $value) = explode(': ', $line, 2);
                $headers[$key] = $value;
            }
        }
        return $headers;
    }
}
