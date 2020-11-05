<?php

namespace App\Traits;

use App\ToDo;

/**
 * @property string $dueDate
 * @property string $remindAt
 * @property string $remindAtTime
 */
trait ToDoRequestTrait
{
    protected function prepareForValidation(): void
    {
        if ($this->remindAtTime) {
            $time = $this->remindAtTime < 10 ? "0$this->remindAtTime" : $this->remindAtTime;
            $this->merge([
                'remindAtTime' => "$time:00",
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
