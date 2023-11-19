<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    /**
     * @param mixed|null $data
     * @param array $responseConstantArray
     * @return JsonResponse
     */
    protected function successResponse(
        mixed $data = null,
        array $responseConstantArray = ['code' => 200, 'message' => '']
    ): JsonResponse{
        return response()
            ->json([
                'status'    => 'ok',
                'data'      => $data,
                'message'   => $responseConstantArray['message'],
                'code'      => $responseConstantArray['code']
            ]);
    }

    /**
     * @param array $responseConstantArray
     * @param mixed|null $data
     * @return JsonResponse
     */
    protected function errorResponse(
        array $responseConstantArray = ['code' => 400, 'message' => ''],
        mixed $data = null
    ): JsonResponse{
        return response()
            ->json([
                'status'    => 'error',
                'data'      => $data,
                'message'   => $responseConstantArray['message'],
                'code'      => $responseConstantArray['code']
            ], 400);
    }
}
