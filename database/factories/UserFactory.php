<?php

use Faker\Generator as Faker;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

$factory->define(App\Models\User::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => '12345678',
        'remember_token' => Str::random(10),
        'permissions' => [],
        'last_login' => $faker->dateTime,
        'active' => null,
        'activation_key' => \Ramsey\Uuid\Uuid::uuid4()->toString(),
    ];
});
