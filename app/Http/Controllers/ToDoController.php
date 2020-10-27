<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class ToDoController extends Controller
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function get(): JsonResponse
    {
        return $this->apiResponse($this->user->toDos->toArray());
    }
}
