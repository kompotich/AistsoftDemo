<?php

declare(strict_types=1);

namespace AistsoftDemo\Client;

use InvalidArgumentException;
use JsonException;
use RuntimeException;
use stdClass;

class CheckoClient
{
    private string $baseUrl;
    private string $apiKey;
    private array $requests;

    public function __construct(array $config)
    {
        $this->baseUrl = rtrim($config['base_url'] ?? '', '/') . '/';
        $this->apiKey = $config['key'] ?? '';
        $this->requests = $config['requests'] ?? [];

        if ($this->baseUrl === '/') {
            throw new InvalidArgumentException('Не найдена конфигурация для Checko API');
        }

        if ($this->requests === []) {
            throw new InvalidArgumentException('Не найден список ресурсов для Checko API');
        }
    }

    public function get(string $resource, array $parameters = []): stdClass
    {
        if (!isset($this->requests[$resource])) {
            throw new InvalidArgumentException('Неизвестный ресурс Checko API');
        }

        if ($this->apiKey === '') {

            throw new RuntimeException('Не указан ключ API для Checko API');
        }

        $url = $this->buildUrl($this->requests[$resource], $parameters);

        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => "Accept: application/json\r\n",
                'timeout' => 20,
                'ignore_errors' => true,
            ],
        ]);
        
        $body = @file_get_contents($url, false, $context);

        if ($body === false) {
            $lastError = error_get_last();
            $details = $lastError['message'] ?? 'Неизвестная ошибка';
            $details = str_replace(
                [$url, $this->apiKey],
                ['[Checko API URL]', '[REDACTED]'],
                $details
            );

            throw new RuntimeException('Не удалось получить ответ от Checko API: ' . $details);
        }

        foreach ($http_response_header ?? [] as $header) {
            if (preg_match('/^HTTP\/\S+\s+(\d{3})/', $header, $matches) === 1) {
                $statusCode = (int) $matches[1];
            }
        }

        if (!isset($statusCode) || $statusCode < 200 || $statusCode >= 300) {
            throw new RuntimeException(
                isset($statusCode)
                    ? sprintf('Checko API вернул статус %d', $statusCode)
                    : 'Checko API вернул неизвестный статус'
            );
        }

        try {
            $response = json_decode($body, false, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new RuntimeException('Checko API вернул некорректный JSON', 0, $exception);
        }

        if (!$response instanceof stdClass) {
            throw new RuntimeException('Checko API вернул неожидаемый формат ответа');
        }

        return $response;
    }

    public function supports(string $resource): bool
    {
        return isset($this->requests[$resource]);
    }

    private function buildUrl(string $template, array $parameters): string
    {
        [$path, $query] = array_pad(explode('?', $template, 2), 2, '');
        $queryParameters = [];

        foreach (explode('&', $query) as $pair) {
            if ($pair === '') {
                continue;
            }

            [$name, $placeholder] = array_pad(explode('=', $pair, 2), 2, '');
            $parameter = trim($placeholder, '{}');
            $value = $parameter === 'key'
                ? $this->apiKey
                : ($parameters[$parameter] ?? '');

            if ($value !== '') {
                $queryParameters[$name] = $value;
            }
        }

        if ($queryParameters === []) {
            throw new InvalidArgumentException('Не указаны параметры запроса для запроса к Checko API');
        }

        return $this->baseUrl . ltrim($path, '/') . '?'
            . http_build_query($queryParameters, '', '&', PHP_QUERY_RFC3986);
    }
}
