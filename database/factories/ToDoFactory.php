<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\ToDo;
use Faker\Generator as Faker;

$factory->define(ToDo::class, function (Faker $faker) {
    $dueDate = rand(0, 10) <= 5 ? $faker->dateTimeBetween('now', '+5 months') : null;

    return [
        'title'     => $faker->sentence(rand(2, 6)),
        'body'      => $faker->sentence(rand(5, 15)),
        'due_date'  => $dueDate,
        'remind_at' => rand(0, 10) <= 5 ? $faker->dateTimeBetween('now', $dueDate) : null,
        'complete'  => rand(0, 10) <= 5 ? true : false,
        'image'     => null,
    ];
});
