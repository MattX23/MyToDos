<?php

namespace App\Traits;

use Carbon\Carbon;

/**
 * @property string $dueDate
 * @property string $remindAt
 */
trait ToDoRequestTrait
{
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
