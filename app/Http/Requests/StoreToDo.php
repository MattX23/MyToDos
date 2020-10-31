<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class StoreToDo extends FormRequest
{
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
        return [
            'title'      => 'required|string|min:2',
            'body'       => 'nullable|string',
            'dueDate'    => 'nullable|date|required_with:remindAt|after:today',
            'remindAt'   => 'nullable|exclude_if:dueDate,null|date|after:tomorrow',
            'image'      => 'nullable|image|max:4096',
            'attachment' => 'nullable|file|max:4096',
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->dueDate && $this->remindAt) {
            $this->merge([
                'remindAt' => Carbon::parse($this->dueDate)
                    ->subDays($this->remindAt)
                    ->format('Y-m-d'),
            ]);
        }
    }
}
