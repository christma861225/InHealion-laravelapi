<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Laravel\Lumen\Routing\Controller as BaseController;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Controller extends BaseController
{
    /**
     * @param $status
     * @param $message
     * @throws HttpException
     */
    protected function raiseError($status, $message)
    {
        abort($status, $message);
    }

    protected function respondWithMessage($message = null): JsonResponse
    {
        return response()->json([
            'status' => 'OK',
            'message' => $message
        ]);
    }

    protected function respondWithResource(JsonResource $resource): JsonResponse
    {
        return response()->json($resource);
    }
}
