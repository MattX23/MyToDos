<?php

namespace App\Http\Requests\ToDos;

use App\ToDo;
use App\Traits\ToDoRequestTrait;
use Illuminate\Foundation\Http\FormRequest;

class UpdateToDo extends FormRequest
{
    use ToDoRequestTrait;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->userIsAuthorised();
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
