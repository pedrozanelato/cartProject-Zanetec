<?php

namespace App\Exceptions;

use Exception;
use GuzzleHttp\Exception\ConnectException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Nette\NotImplementedException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Throwable;
use TypeError;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Render an exception into an HTTP response.
     *
     * @param Request $request
     * @param Exception|Throwable $e
     * @return Response
     *
     * @throws Throwable
     */
    public function render($request, Exception|Throwable $e): Response
    {
        if ($request->is('api/*')) {
            if ($e instanceof QueryException) {
                $dbCode = $e->getCode();
                $errorMessage = match ($dbCode ?? 0) {
                    23000 => 'Erro ao tentar salvar os dados, verifique se os dados fornecidos são válidos.',
                    default => 'Houve um erro ao tentar salvar os dados, verifique se os dados fornecidos são válidos.',
                };
                return $this->errorResponse(
                    message: $errorMessage,
                    code: 470,
                    data: config('app.debug') ? ($e->getMessage() ?? null) : null,
                );
            }

            if ($e instanceof TransportException) {
                return $this->errorResponse(
                    message: 'Houve um erro ao tentar enviar o e-mail, verifique se o e-mail existe realmente e se o mesmo é válido.',
                    code: 409,
                    data: config('app.debug') ? ($e->getMessage() ?? null) : null,
                );
            }

            if ($e instanceof ValidationException) {
                return $this->errorResponse(
                    message: 'Os dados fornecidos são inválidos.',
                    code: 422,
                    erros: Arr::flatten($e->validator->getMessageBag()->toArray()),
                );
            }
            if ($e instanceof ConnectException || $e instanceof ConnectionException) {
                return $this->errorResponse(
                    message: 'Não foi possível conectar ao servidor remoto, por favor tente novamente mais tarde.',
                    code: 408,
                    data: config('app.debug') ? ($e->getMessage() ?? null) : null,
                );
            }
            if ($e instanceof ExceptionWithData) {
                return $this->errorResponse(
                    message: $e->getMessage() ?? 'Os dados fornecidos são inválidos.',
                    code: $e->getCode() ?? 422,
                    data: $e->getData(),
                    erros: $e->getErrors(),
                );
            }
            if ($e instanceof TypeError) {
                return $this->errorResponse($e->getMessage(), 500);
            }
            if ($e instanceof RouteNotFoundException) {
                return $this->errorResponse(message: $e->getMessage(), code: 404);
            }
            if ($e instanceof ModelNotFoundException) {
                $modelName = strtolower(class_basename($e->getModel()));
                return $this->errorResponse("Não existe nenhum {$modelName} com o identificador especificado.", 404);
            }
            if ($e instanceof AuthenticationException) {
                return response()->json(['error' => 'Usuário não autenticado.'], 401);
            }
            if ($e instanceof AuthorizationException) {
                return $this->errorResponse($e->getMessage(), 403);
            }
            if ($e instanceof MethodNotAllowedHttpException) {
                return $this->errorResponse('Você tentou acessar um método inválido ', 405);
            }
            if ($e instanceof NotFoundHttpException) {
                return $this->errorResponse('Essa URL não existe. Você digitou corretamente?', 404);
            }
            if ($e instanceof HttpException) {
                return $this->errorResponse($e->getMessage(), $e->getStatusCode());
            }
            if ($e instanceof NotImplementedException) {
                return $this->errorResponse($e->getMessage() !== '' ? $e->getMessage() : 'Método não implementado.', 500);
            }
            if ($e instanceof BindingResolutionException) {
                return $this->errorResponse('Interface não implementada em tempo de execução.', 500, data: $e->getMessage());
            }
            if ($e instanceof Exception) {
                $code = $e->getCode();
                return $this->errorResponse($e->getMessage(), $code !== 0 ? $code : 422);
            }
            if (config('app.debug')) {
                return parent::render($request, $e);
            }
            return $this->errorResponse('Tivemos um erro inesperado. Por favor tente novamente mais tarde.', 500);
        }
        
        return response()->view('errors.404', [], 404);
    }

    protected function errorResponse($message, $code, $data = null, mixed $erros = null): JsonResponse
    {
        !is_array($data) && $data = ($data instanceof Model || $data instanceof Collection) ? $data->toArray() : (array)$data;

        $result = collect([
            'success' => false,
            'message' => $message,
            'code' => $code,
            'data' => $data,
            'errors' => $erros,
        ])->filter(fn($value) => $value !== null);

        return response()->json($result, $code);
    }
}
