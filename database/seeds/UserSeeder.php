<?php

use App\ToDo;
use App\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(User::class, 5)->create()->each(function(User $user) {
            $user->toDos()->save(factory(ToDo::class)->make());
        });
    }
}
