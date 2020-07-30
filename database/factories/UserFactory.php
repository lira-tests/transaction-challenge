<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\User;
use Faker\Generator as Faker;
use Faker\Provider\pt_BR\Company;
use Faker\Provider\pt_BR\Person;
use Illuminate\Support\Str;

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

$factory->define(User::class, function (Faker $faker) {
    $faker->addProvider(new Person($faker));
    $faker->addProvider(new Company($faker));

    $type = $faker->randomElement([User::TYPE_COMPANY, User::TYPE_PERSON]);

    if ($type === User::TYPE_PERSON) {
        return [
            'full_name' => $faker->name,
            'email' => $faker->unique()->safeEmail,
            'document' => $faker->unique()->cpf(false),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
            'type' => $type
        ];
    }

    return [
        'full_name' => $faker->company,
        'email' => $faker->unique()->companyEmail,
        'document' => $faker->unique()->cnpj(false),
        'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
        'type' => $type
    ];
});
