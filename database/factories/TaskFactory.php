<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use Faker\Generator as Faker;
use App\Models\Task;

$factory->define(Task::class, function (Faker $faker) {
    $start = new \DateTime(date('Y-m-d', strtotime('-20 days')));
    $end = new \DateTime('NOW');
    $randomTimestamp = mt_rand($start->getTimestamp(), $end->getTimestamp());
    $randomDate = new \DateTime();
    $randomDate->setTimestamp($randomTimestamp);
    return [
        'name' => $faker->name,
        'status' => Task::STATUSES[mt_rand(0, 2)],
        'due' => $randomDate->format("Y-m-d H:i:s"),
        'project_id' => null,
        'priority' => Task::PRIORITIES[mt_rand(0, 3)]
    ];
});
