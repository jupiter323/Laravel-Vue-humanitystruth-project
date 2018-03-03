<?php

use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(App\User::class, function (Faker $faker) {
    return [
        'email' => $faker->unique()->safeEmail,
        'phone' => $faker->phoneNumber,
        'alias' => $faker->name,
        'password' => str_random(32), // secret
        //'hash' => str_random(32),
        'remember_token' => str_random(32),
    ];
});
