<?php

use Faker\Generator as Faker;
use Carbon\Carbon;

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

$factory->define(\App\Entity\Money::class, function (Faker $faker) {
    return [
        'currency_id' => function () {
            return factory(\App\Entity\Currency::class)->create()->id;
        },
        'amount' => $faker->randomFloat(2,1,10000),
        'wallet_id' => function () {
            return factory(\App\Entity\Wallet::class)->create()->id;
        },
    ];
});
