<?php

use Faker\Generator as Faker;
use App\User;

$factory->define(App\Investigation::class, function (Faker $faker) {
    return [
        'account_id' => 0,
        'title' => $faker->sentence,
        'objective' => $faker->sentence,
    ];
});
