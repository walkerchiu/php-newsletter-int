<?php

/** @var \Illuminate\Database\Eloquent\Factory  $factory */

use Faker\Generator as Faker;
use WalkerChiu\Newsletter\Models\Entities\Setting;
use WalkerChiu\Newsletter\Models\Entities\SettingLang;

$factory->define(Setting::class, function (Faker $faker) {
    return [
        'serial' => $faker->isbn10
    ];
});

$factory->define(SettingLang::class, function (Faker $faker) {
    return [
        'code'  => $faker->locale,
        'key'   => $faker->randomElement(['name', 'description']),
        'value' => $faker->sentence
    ];
});
