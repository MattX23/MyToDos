<?php

namespace App\Http\Requests;

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
    public function authorize()
    {
        $toDoId = $this->route('toDo')->id;
        $userId = $this->route('user')->id;

        return ToDo
            ::where('id', '=', $toDoId)
            ->where('user_id', '=', $userId)
            ->exists();
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
