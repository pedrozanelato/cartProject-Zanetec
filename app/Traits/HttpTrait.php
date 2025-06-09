<?php

namespace App\Traits;

use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

trait HttpTrait
{
    protected function put(string $url, array $payload = [], array $headers = []): array
    {
        $response = Http::withOptions(['verify' => false])
            ->withHeaders($headers)
            ->timeout(240)
            ->put(url: $url, data: $payload);

        return $this->_extractResponse($response);
    }

    private function _extractResponse(PromiseInterface|\Illuminate\Http\Client\Response $response): array
    {
        $data = json_decode($response->body(), true);
        $code = $this->_getValidHttpStatusCode($response->status());
        return [$data, $code, $response];
    }

    private function _getValidHttpStatusCode(int $code): int
    {
        if ($code < Response::HTTP_CONTINUE || $code > Response::HTTP_NETWORK_AUTHENTICATION_REQUIRED) {
            return Response::HTTP_UNPROCESSABLE_ENTITY;
        }

        if ($code === Response::HTTP_INTERNAL_SERVER_ERROR) {
            return Response::HTTP_FAILED_DEPENDENCY;
        }

        if ($code === Response::HTTP_UNAUTHORIZED) {
            return Response::HTTP_FORBIDDEN;
        }

        return $code;
    }

    protected function getRetry(string $url, array $payload = [], array $headers = []): array
    {
        $response = Http::retry(3, 100)
            ->withOptions(['verify' => false])
            ->withHeaders($headers)
            ->get(url: $url, query: $payload);

        return $this->_extractResponse($response);
    }

    protected function get(string $url, array $payload = [], array $headers = []): array
    {
        $response = Http::withOptions(['verify' => false])
            ->withHeaders($headers)
            ->timeout(5000)
            ->get(url: $url, query: $payload);

        return $this->_extractResponse($response);
    }

    protected function delete(string $url, array $payload = [], array $headers = []): array
    {
        $response = Http::withOptions(['verify' => false])
            ->withHeaders($headers)
            ->delete(url: $url, data: $payload);

        return $this->_extractResponse($response);
    }

    protected function postFormData(string $url, array $payload = [], array $headers = []): array
    {
        $response = Http::withOptions(['verify' => false])
            ->withHeaders($headers)
            ->asForm()
            ->post(url: $url, data: $payload);

        return $this->_extractResponse($response);
    }

    protected function post(string $url, array $payload = [], array $headers = [], ?int $timeout = 360): array
    {
        $response = Http::withOptions(['verify' => false])
            ->withHeaders($headers)
            ->timeout(seconds: $timeout)
            ->connectTimeout(seconds: $timeout)
            ->post(url: $url, data: $payload);

        return $this->_extractedResponseTreatMessage($response);
    }

    private function _extractedResponseTreatMessage(PromiseInterface|\Illuminate\Http\Client\Response $response): array
    {
        $body = $response->body();

        if (is_string($body)) {
            $body = Str::replace('Mensagem', 'message', $body);
        }

        $data = json_decode($body, true);
        $code = $this->_getValidHttpStatusCode($response->status());
        return [$data, $code, $response];
    }

    protected function postAttachFile(string $url, array $payload = [], array $headers = [], mixed $file = [], string $finleName = 'file'): array
    {
        $response = Http::withOptions(['verify' => false])
            ->attach(name: $finleName, contents: file_get_contents($file), filename: $file->getClientOriginalName())
            ->withHeaders(headers: $headers)
            ->post(url: $url, data: $payload);

        return $this->_extractResponse($response);
    }

    protected function postRetry(string $url, array $payload = [], array $headers = []): array
    {
        $response = Http::retry(3, 100)->withOptions(['verify' => false])
            ->withHeaders($headers)
            ->post(url: $url, data: $payload);

        return $this->_extractedResponseTreatMessage($response, $payload, $url, 'POST_RETRY');
    }
}
