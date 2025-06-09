<?php

use App\Exceptions\ExceptionWithData;
use Illuminate\Http\Response;

/**
 * Lança uma exceção customizada (ou aborta) quando uma condição é verdadeira.
 *
 * @param  bool        $condition  Se verdadeiro, dispara a exceção ou aborta.
 * @param  string      $message    Mensagem de erro.
 * @param  int         $code       Código HTTP (default 422).
 * @param  mixed|null  $data       Payload adicional opcional.
 * @param  array|null  $errors     Erros de validação opcionais.
 * 
 * @return void
 *
 * @throws ExceptionWithData
 */
function exception(
    bool $condition,
    string $message,
    int $code = Response::HTTP_UNPROCESSABLE_ENTITY,
    mixed $data = null,
    ?array $errors = null
): void {
    
    if (! $condition) {
        return;
    }

    if ($data === null && $errors === null) {
        abort($code, $message);
    }

    $ex = new ExceptionWithData($message, $code);

    if ($errors !== null) {
        $ex->setErrors($errors);
    }

    if ($data !== null) {
        $ex->setData($data);
    }

    throw $ex;
}