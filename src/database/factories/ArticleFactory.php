<?php

/** @var \Illuminate\Database\Eloquent\Factory  $factory */

use Faker\Generator as Faker;
use WalkerChiu\Newsletter\Models\Entities\Article;

$factory->define(Article::class, function (Faker $faker) {
    return [
        'serial'  => $faker->isbn10,
        'subject' => $faker->title,
        'content' => $faker->text
    ];
});
