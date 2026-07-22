<?php

namespace App\Traits;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Resources\Json\ResourceCollection;

trait ApiResponse
{
    protected function successResponse($data = [], $message = 'The operation was successful.', $statusCode = 200)
    {
        // ✅ اگر خروجی از نوع ResourceCollection است (مثل ListResource::collection($lists))
        if ($data instanceof ResourceCollection) {
            // ResourceCollection خودش pagination و ساختار meta/links را دارد
            $response = $data->response()->getData(true);

            // فیلدهای سفارشی را به آن اضافه می‌کنیم
            $response['success'] = true;
            $response['message'] = $message;

            return response()->json($response, $statusCode);
        }

        // ✅ اگر خروجی از نوع paginator معمولی است
        if ($data instanceof LengthAwarePaginator) {
            return response()->json([
                'data' => $data->items(),
                'links' => [
                    'first' => $data->url(1),
                    'last'  => $data->url($data->lastPage()),
                    'prev'  => $data->previousPageUrl(),
                    'next'  => $data->nextPageUrl(),
                ],
                'meta' => [
                    'current_page' => $data->currentPage(),
                    'from'         => $data->firstItem(),
                    'last_page'    => $data->lastPage(),
                    'path'         => $data->path(),
                    'per_page'     => $data->perPage(),
                    'to'           => $data->lastItem(),
                    'total'        => $data->total(),
                ],
                'success' => true,
                'message' => $message,
            ], $statusCode);
        }

        // ✅ خروجی ساده بدون pagination
        return response()->json([
            'data'     => $data,
            'success'  => true,
            'message'  => $message,
        ], $statusCode);
    }

    protected function errorResponse($message = 'An error has occurred.', $statusCode = 400, $errors = [])
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors'  => $errors,
        ], $statusCode);
    }
}
