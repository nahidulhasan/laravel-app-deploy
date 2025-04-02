<?php
namespace App\Traits;

use http\Client;
use Illuminate\Http\Response;

trait ApiResponse
{

    /**
     * @param $data
     * @param int $statusCode
     * @return \Illuminate\Contracts\Routing\ResponseFactory|Response
     */
    public function successResponse($data, $statusCode = Response::HTTP_OK)
    {
        return response($data, $statusCode)->header('Content-Type', 'application/json');
    }


    /**
     * @param $errorMessage
     * @param $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    public function errorResponse($errorMessage, $statusCode)
    {
        return response()->json(['message' => $errorMessage, 'status_code' => $statusCode], $statusCode);
    }


    /**
     * @param $errorMessage
     * @param $statusCode
     * @return \Illuminate\Contracts\Routing\ResponseFactory|Response
     */
    public function errorMessage($errorMessage, $statusCode)
    {
        return response($errorMessage, $statusCode)->header('Content-Type', 'application/json');
    }

}