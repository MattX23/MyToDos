<?php

namespace App\Http\Requests\ToDos;

use App\ToDo;
use App\ToDoRequest;
use App\Traits\ToDoRequestTrait;

class StoreToDo extends ToDoRequest
{
    use ToDoRequestTrait;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return ToDo::RULES;
    }
}
