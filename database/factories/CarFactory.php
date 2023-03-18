<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use App\Car;

$factory->define(Car::class, function (Faker $faker) {
    return [
        "name" => $faker->name,
        "service_id" => 1
    ];
});
