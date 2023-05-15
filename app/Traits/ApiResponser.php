<?php

namespace App\Traits;

use Illuminate\Http\Resources\Json\ResourceCollection;

/*
|--------------------------------------------------------------------------
| Api Responser Trait
|--------------------------------------------------------------------------
|
| This trait will be used for any response we sent to clients.
|
*/

trait ApiResponser
{
    /**
     * Return a success JSON response.
     *
     * @param  array|string  $data
     * @param  string  $message
     * @param  int|null  $code
     * @param  array|null  $action
     * @return \Illuminate\Http\JsonResponse
     */
    protected function success($data =null , string $message = null, int $code = 200)
    {
        return response()->json([
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    /**
     * Return an error JSON response.
     *
     * @param  string  $message
     * @param  int  $code
     * @param  array|string|null  $data
     * @param  array|null  $action
     * @return \Illuminate\Http\JsonResponse
     */
    protected function error(string $message = null, int $code = 200, $data = null)
    {
        return response()->json([
            'message' => $message,
            'data' => $data,
        ], $code);
    }

}
