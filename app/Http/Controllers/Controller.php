<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    protected ?User $user;

    public function __construct()
    {
        $this->user = auth('api')->user();
    }

    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @param array $response
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function apiResponse(array $response): JsonResponse
    {
        return response()->json($response);
    }
}
