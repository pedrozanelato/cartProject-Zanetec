<?php

namespace App\Exceptions;

use App\Models\DefaultReturnType;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ExceptionWithData extends Exception
{
    private mixed  $data;
    private int    $success;
    private ?array $appendData;
    private ?array $errors;

    public function __construct(
        $message = '',
        Throwable $previous = null,
        $data = null,
        $code = Response::HTTP_UNPROCESSABLE_ENTITY,
        $success = false,
        $errors = null,
    )
    {
        parent::__construct($message, self::_getValidHttpStatusCode($code), $previous);

        $this->data = $data;
        $this->errors = $errors;
        $this->success = $success;
    }

    public static function instance(): ExceptionWithData
    {
        return new self();
    }

    public static function create(Throwable|ExceptionWithData $exception): ExceptionWithData
    {
        try {
            $code = $exception->getCode() > 0
                ? $exception->getCode()
                : (property_exists((object)$exception, 'getStatusCode')
                    ? $exception->getStatusCode()
                    : Response::HTTP_UNPROCESSABLE_ENTITY);

            return new self(
                message: $exception->getMessage() ? $exception?->getMessage() : 'Ocorreu um erro e não foi possível atender sua solicitação tente novamente mais tarde.',
                previous: $exception->getPrevious() ?? null,
                data: ($exception instanceof ExceptionWithData) ? $exception->data : null,
                code: (int)($code >= Response::HTTP_CONTINUE ? $code : Response::HTTP_UNPROCESSABLE_ENTITY),
                success: false,
                errors: $exception->errors ?? null,
            );
        } catch (Exception) {
            return new self(
                message: 'Ocorreu um erro e não foi possível atender sua solicitação tente novamente mais tarde. [35]',
                code: Response::HTTP_UNPROCESSABLE_ENTITY,
            );
        }
    }

    public function getData(): mixed
    {
        return $this->data ?? null;
    }

    public function setAppendData(?array $data): ExceptionWithData
    {
        $this->appendData = $data;
        return $this;
    }

    public function setMessage(string $message): ExceptionWithData
    {
        $this->message = $message;
        return $this;
    }

    public function setErrors(?array $errors): ExceptionWithData
    {
        $this->errors = $errors;
        return $this;
    }

    public function getErrors(): ?array
    {
        return $this->errors ?? null;
    }

    public function setStatusCode(int $code): ExceptionWithData
    {
        $this->code = $code;
        return $this;
    }

    public function setSuccess(int $success): ExceptionWithData
    {
        $this->success = $success;
        return $this;
    }

    public function getSuccess(): bool
    {
        return $this->success;
    }

    public function setData(mixed $data): ExceptionWithData
    {
        $this->data = $data;
        return $this;
    }

    public function toJsonResponse(): jsonResponse
    {
        return DefaultReturnType::create(
            success: $this->success ?? false,
            code: $this->code,
            data: $this->data,
            message: $this->message,
            errors: $this->errors,
        )->setAppendData($this->appendData ?? [])->toJsonResponse();
    }

    public function writeLog(): ExceptionWithData
    {
        Log::error($this);

        return $this;
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

    public function setException(Exception|ExceptionWithData $e): ExceptionWithData
    {
        $e->message && $this->setMessage($e->message);
        $e->code && $this->setStatusCode($e->code);
        $e->success && $this->setSuccess($e->success);
        ($e instanceof ExceptionWithData) && $e->data && $this->setData($e->data);

        return $this;
    }
}
