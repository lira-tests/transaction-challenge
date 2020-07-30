<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Wallet;
use Faker\Generator as Faker;
use phpDocumentor\Reflection\Types\Null_;

$factory->define(Wallet::class, function (Faker $faker) {
    return [
        'amount' => $faker->randomFloat(2, 0, 500),
    ];
});
