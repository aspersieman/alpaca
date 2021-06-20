<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use Faker\Generator as Faker;
use App\Models\Timer;

$factory->define(Timer::class, function (Faker $faker) {
    $start = new \DateTime(date('Y-m-d H:i:s', strtotime('-20 minutes')));
    $stop = new \DateTime('NOW');
    return [
      'name' => $faker->name,
      'started_at' => $start,
      'stopped_at' => $stop,
      'duration' => 1200
    ];
});
