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

$factory->define(\App\Entity\Lot::class, function (Faker $faker) {
    return [
        'currency_id' => $faker->numberBetween(1,1000),
        'seller_id' => $faker->numberBetween(1,1000),
        'date_time_open' => Carbon::createFromTimestamp((int) time()),
        'date_time_close' => Carbon::createFromTimestamp((int) time()+3600*60),
        'price' => $faker->randomFloat(2,0,1000),
    ];
});
