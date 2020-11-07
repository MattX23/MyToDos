<?php

namespace App\Mail;

use App\ToDo;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;

class SendReminder extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    protected ToDo $toDo;
    protected string $timeUntilDue;

    /**
     * Create a new message instance.
     *
     * @param \App\ToDo $toDo
     * @param string    $timeUntilDue
     */
    public function __construct(ToDo $toDo, string $timeUntilDue)
    {
        $this->toDo = $toDo;
        $this->timeUntilDue = $timeUntilDue;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->from(Config::get('mail.from.reminders'))
            ->subject('Your To Do Reminder')
            ->markdown('emails.reminders')
            ->withToDo($this->toDo)
            ->withTimeUntilDue($this->timeUntilDue)
            ->withUrl(Config::get('app.url'));
    }
}
