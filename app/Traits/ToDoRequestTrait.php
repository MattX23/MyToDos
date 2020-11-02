<?php

namespace App\Traits;

use App\ToDo;
use Carbon\Carbon;

/**
 * @property string $dueDate
 * @property string $remindAt
 */
trait ToDoRequestTrait
{
    protected function prepareForValidation(): void
    {
        if ($this->dueDate && $this->remindAt) {
            $this->merge([
                'remindAt' => Carbon::parse($this->dueDate)
                    ->subDays($this->remindAt)
                    ->format('Y-m-d'),
            ]);
        }
    }

    /**
     * @return bool
     */
    protected function userIsAuthorised(): bool
    {
        $toDoId = $this->route('toDo')->id;
        $userId = $this->route('user')->id;

        return ToDo
            ::where('id', '=', $toDoId)
            ->where('user_id', '=', $userId)
            ->exists();
    }
}
