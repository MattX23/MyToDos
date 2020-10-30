<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\ToDo;
use Carbon\Carbon;
use Faker\Generator as Faker;

$factory->define(ToDo::class, function (Faker $faker) {

    $days = [1, 2, 3, 4, 5, 6, 7, 14, 21];
    $date = $faker->dateTimeBetween('now', '+5 months');

    $dueDate = rand(0, 10) <= 5 ? Carbon::parse($date)->format('Y-m-d') : null;
    $remindAt = $dueDate ? Carbon::parse($date)->subDays(array_rand($days))->format('Y-m-d') : null;

    return [
        'title'      => $faker->sentence(rand(2, 6)),
        'body'       => rand(0, 10) <= 5 ? null : $faker->sentence(rand(5, 15)),
        'due_date'   => $dueDate,
        'remind_at'  => rand(0, 10) <= 5 && $dueDate ? $remindAt : null,
        'complete'   => rand(0, 10) <= 5 ? true : false,
        'image'      => null,
        'attachment' => null,
    ];
});
