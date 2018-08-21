<?php

use Faker\Generator as Faker;
use App\User;

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
        'name' => $faker->name,
        'lastname' => $faker->lastName,
        'email' => $faker->unique()->safeEmail,
        'country' => $faker->country,
        'verified' => $faker->randomElement([User::VERIFICADO, User::NO_VERIFICADO]),
        'date_birthday' => $faker->date($format = 'Y-m-d', $max = 'now'),
        'password' => bcrypt('123456'), // secret
        'remember_token' => str_random(10),
        'verification_token' => str_random(56),
    ];
});


$factory->define(App\Kid::class, function (Faker $faker) {
    return [
        'fullname' => $faker->name,
        'username' => $faker->userName,
        'pin' => bcrypt($faker->randomNumber($nbDigits = 4)),
        'age' => $faker->randomDigit,
        'user_id' => User::inRandomOrder()->first()->id,
    ];
});

$factory->define(App\Video::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'url' => $faker->url,
        'type_local' => $faker->randomElement([true, false]),
        'user_id' => User::inRandomOrder()->first()->id,
    ];
});