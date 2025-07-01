<?php

namespace App\Trait;

use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;

trait ResponseJson
{
    public function codeReturn(int $code, $data = NULL, string $message = ''): JsonResponse
    {
        if ( !$message && !empty(config('message.RETURN_CODES')[$code])) {
            $message = config('message.RETURN_CODES')[$code];
        }

        $ret = ['code' => $code, 'message' => $message ?: ''];
        if ( !is_null($data)) {
            if (is_array($data)) {
                $data = array_filter($data, static function ($item) {
                    return $item !== NULL;
                });
            }
            $ret['data'] = $data;
        }

        return response()->json($ret);
    }

    public function success($data = NULL, string $info = 'success'): JsonResponse
    {
        return $this->codeReturn(200, $data, $info);
    }

    public function fail(int $code, string $info = ''): JsonResponse
    {
        return $this->codeReturn($code, NULL, $info);
    }

    public function successPaginated($paginator): JsonResponse
    {
        return $this->success($this->paginate($paginator));
    }

    protected function paginate($paginator): array
    {
        if ($paginator instanceof LengthAwarePaginator) {
            return [
                'total'        => $paginator->total(),
                'current_page' => $paginator->total() === 0 ? 0 : $paginator->currentPage(),
                'limit'        => $paginator->perPage(),
                'last_page'    => $paginator->total() === 0 ? 0 : $paginator->lastPage(),
                'data'         => $paginator->items(),
            ];
        }

        return [];
    }
}
