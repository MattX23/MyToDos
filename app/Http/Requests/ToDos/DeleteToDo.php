<?php

namespace App\Http\Requests\ToDos;

use App\Traits\ToDoRequestTrait;
use Illuminate\Foundation\Http\FormRequest;

class DeleteToDo extends FormRequest
{
    use ToDoRequestTrait;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->isUserAuthorised();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [];
    }
}
