<?php

namespace App\Console\Commands;

use App\Mail\SendReminder;
use App\ToDo;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminders:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email reminders for To Dos at the scheduled time.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     */
    public function handle()
    {
        $dateTime = Carbon::now()->format('Y-m-d H:00:00');

        $toDos = ToDo
            ::where('remind_at', '=', $dateTime)
            ->get();

        $toDos->each(function(ToDo $toDo) use ($dateTime) {
            $timeUntilDue = $this->getHumanReadableTimeUntilDue($toDo, $dateTime);

            Mail::to($toDo->user->email)->send(new SendReminder($toDo, $timeUntilDue));

            $toDo->update([
                'remind_at' => null,
            ]);
        });
    }

    /**
     * @param \App\ToDo $toDo
     * @param string    $dateTime
     *
     * @return string
     */
    protected function getHumanReadableTimeUntilDue(ToDo $toDo, string $dateTime): string
    {
        return Carbon
            ::parse($toDo->due_date)
            ->diffForHumans(
                Carbon::parse($dateTime)->startOfDay(),
                true,
                false,
                4
            );
    }
}
