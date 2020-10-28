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
        $todos = $this->user->toDos->groupby('complete');

        return $this->apiResponse(
            [
                'incomplete' => $todos[0],
                'complete'   => $todos[1],
            ]
        );
    }
}
