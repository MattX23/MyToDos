@component('mail::message')
# Reminder!

Hey {{ $toDo->user->name }},

Just a reminder, your task: "{{ $toDo->title }}" is due in {{ $timeUntilDue }}.

To view, delete or edit your To Dos, simply log in using the button below.

@component('mail::button', ['url' => $url])
Login
@endcomponent

See ya! <br><br>
Regards,
{{ config('app.name') }}
@endcomponent
