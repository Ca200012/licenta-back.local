<?php

namespace App\Providers;

use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;

class ResponseMacroServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        Response::macro('success', function ($data, $extraParams = []) {
            $response = [
                'errors' => false,
                'data' => $data,
                'status_code' => 200,
            ];
            if (count($extraParams) > 0) {
                $response = array_merge_recursive($response, $extraParams);
            }

            return Response::json($response);
        });

        Response::macro('error', function ($message, $status = 400, $extraParams = []) {
            $response = [
                'message' => $status . ' error',
                'errors' => [
                    'message' => [$message],
                ],
                'status_code' => $status,
            ];

            if (count($extraParams) > 0) {
                $response = array_merge_recursive($response, $extraParams);
            }

            return Response::json($response, $status);
        });

        Response::macro('errorjson', function ($message, $data = [], $status = 400) {
            return Response::json([
                'message' => $status . ' error',
                'errors' => [
                    'message' => [$message],
                ],
                'status_code' => $status,
                'params' => $data
            ], $status);
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
