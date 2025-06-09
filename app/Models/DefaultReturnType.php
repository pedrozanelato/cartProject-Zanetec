<?php

namespace App\Models;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * @OA\Schema(
 *   schema="DefaultReturnType",
 *   title="DefaultReturnType",
 *  	@OA\Property(
 *         property="success",
 *         type="boolean"
 *      ),
 *  	@OA\Property(
 *         property="data",
 *         type="object"
 *      ),
 *  	@OA\Property(
 *         property="message",
 *         type="string"
 *      ),
 *  	@OA\Property(
 *         property="code",
 *         type="int"
 *      ),
 *  	@OA\Property(
 *         property="Errors",
 *         type="object"
 *      ),
 *   ),
 * )
 *
 * @property int $success
 * @property mixed $data
 * @property string $message
 * @property int $code
 * */
class DefaultReturnType
{
    public bool $success;
    public mixed $data;
    public ?string $message;
    public int $code;
    public mixed $pages;
    private ?array $appendData;
    private ?array $errors;

    public function __construct(
        bool   $success = true,
        int    $code = Response::HTTP_OK,
        mixed  $data = null,
        string $message = null,
        mixed  $pages = null,
        array  $errors = null
    )
    {
        $this->code = $code;
        $this->success = $success;
        $this->data = $data;
        $this->message = $message;
        $this->pages = $pages;
        $this->errors = $errors;

        if (!$this->message)
            unset($this->message);

        if (!$this->pages)
            unset($this->pages);

        if (!$this->errors)
            unset($this->errors);
    }

    public static function create(
        bool   $success = true,
        int    $code = Response::HTTP_OK,
        mixed  $data = null,
        string $message = null,
        mixed  $pages = null,
        array  $errors = null
    ): DefaultReturnType
    {
        return new DefaultReturnType($success, $code, $data, $message, $pages, $errors);
    }

    public function toArray(): array
    {
        return (array)$this;
    }

    public static function jsonResponseReverse(DefaultReturnType $result): JsonResponse
    {
        return response()->json($result->toArrayReverse(), $result->code);
    }

    public function toArrayReverse(): array
    {
        return array_reverse((array)$this ?? []);
    }

    public function setData(mixed $data): DefaultReturnType
    {
        $this->data = $data;
        return $this;
    }

    public function setPages(LengthAwarePaginator $lengthAwarePaginator): DefaultReturnType
    {
        $position_last = ($lengthAwarePaginator->perPage() * $lengthAwarePaginator->currentPage());

        $this->pages = AppPaginate::create(
            first: 1,
            previous: $lengthAwarePaginator->currentPage() - 1,
            current: $lengthAwarePaginator->currentPage(),
            next: $lengthAwarePaginator->hasMorePages() ? $lengthAwarePaginator->currentPage() + 1 : $lengthAwarePaginator->currentPage(),
            last: $lengthAwarePaginator->lastPage(),
            total: $lengthAwarePaginator->total(),
            perPage: $lengthAwarePaginator->perPage(),
            position: [
                'first' => (($lengthAwarePaginator->perPage() * $lengthAwarePaginator->currentPage()) - $lengthAwarePaginator->perPage()) + 1,
                'last'  => $position_last < $lengthAwarePaginator->total() ? $position_last : $lengthAwarePaginator->total()
            ],
        );

        return $this;
    }

    public function toJsonResponse(): JsonResponse
    {
        $data = array_merge([
            'success' => $this->success,
            'code'    => $this->code,
            'message' => $this->message ?? null,
            'data'    => $this->data,
            'pages'   => $this->pages ?? null,
            'errors'  => $this->errors ?? null,
        ], $this->appendData ?? []);

        return response()->json(array_filter($data, fn($x) => !is_null($x) && $x !== ''), $this->code);
    }

    public function toJsonResponseReverse(): JsonResponse
    {
        return response()->json($this->toArrayReverse(), $this->code);
    }

    public function error(string $message = null, mixed $data = null, int $code = Response::HTTP_NOT_ACCEPTABLE, array $errors = null): DefaultReturnType
    {
        $this->success = false;
        $this->message = $message;
        $this->code = $code;
        $this->data = $data;
        $this->errors = $errors;

        return $this;
    }

    public function setMessage(string $message): DefaultReturnType
    {
        $this->message = $message;
        return $this;
    }

    public function setAppendData(?array $data): DefaultReturnType
    {
        $this->appendData = $data;
        return $this;
    }

    public function setCode(int $code): DefaultReturnType
    {
        $this->code = $code;
        return $this;
    }

    public function setSuccess(bool $success): DefaultReturnType
    {
        $this->success = $success;
        return $this;
    }

    public function success(int $code = Response::HTTP_OK): DefaultReturnType
    {
        $this->code = $code;
        $this->success = true;
        return $this;
    }
}
