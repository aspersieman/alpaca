<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use Faker\Generator as Faker;
use App\Models\Note;

$factory->define(Note::class, function (Faker $faker) {
    return [
      'name' => $faker->name,
      'content' => $faker->paragraph(5, true),
    ];
});
