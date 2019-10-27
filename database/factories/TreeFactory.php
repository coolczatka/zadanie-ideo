<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Tree;
use Faker\Generator as Faker;

$factory->define(Tree::class, function (Faker $faker) {
    return [
        'name' =>$faker->sentence,
        'user_id'=>rand(1,40)
    ];
});
