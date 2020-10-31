<?php

namespace Tests\Traits;

use App\ToDo;
use App\User;
use Carbon\Carbon;

trait TestSetupTrait
{
    protected User $user;

    /**
     * @param bool $isLoggedIn
     *
     * @return \Tests\Traits\TestSetupTrait
     */
    protected function getTestUser($isLoggedIn = true): self
    {
        $this->user = factory(User::class, 1)
            ->create()
            ->first();

        if ($isLoggedIn) $this->logUserIn();

        return $this;
    }

    protected function logUserIn(): void
    {
        $this->actingAs($this->user);
    }

    /**
     * @param int|null  $number
     * @param bool|null $withDueDate
     *
     * @return $this
     */
    protected function addToDos(?int $number = 1, ?bool $withDueDate = false): self
    {
        for ($i = 0; $i <= $number; $i++) {

            $complete = $i % 2 === 0;

            ToDo::create([
                'user_id'   => $this->user->id,
                'title'     => 'Test To Do #'.($i + 1),
                'body'      => 'This is the To Do body for To Do #'.($i + 1),
                'due_date'  => null,
                'remind_at' => null,
                'image'     => null,
                'complete'  => $complete
            ]);
        }

        if ($withDueDate) $this->addDueDate($number);

        return $this;
    }

    /**
     * @param int $number
     *
     * @return $this
     */
    protected function addDueDate(int $number): self
    {
        $dueDates = [];

        for ($i = 0; $i <= $number; $i++) {
            $dueDates[] = Carbon::parse('2020-10-31')->addDays(10 + $i)->format('Y-m-d');
        }

        $index = 0;

        $this->user->toDos->each(function (ToDo $todo) use ($dueDates, &$index) {
            $todo->update([
                'due_date' => $dueDates[$index],
            ]);

            $index++;
        });

        return $this;
    }
}