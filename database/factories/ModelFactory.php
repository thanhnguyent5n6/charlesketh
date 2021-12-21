<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'name'           => $faker->name,
        'email'          => $faker->unique()->safeEmail,
        'password'       => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
    ];
});

$factory->define(App\Category::class, function (Faker\Generator $faker) {
    return [
        'parent'   => 0,
        'priority' => 1,
        'status'   => 'publish',
        'type'     => 'san-pham',
    ];
});

$factory->define(App\CategoryLanguage::class, function (Faker\Generator $faker) {
    $name                    = $faker->name;
    $meta_seo['title']       = $name;
    $meta_seo['keywords']    = $name;
    $meta_seo['description'] = $name;
    return [
        'title'       => $name,
        'slug'        => str_slug($name),
        'meta_seo'    => $meta_seo,
        'language'    => 'vi',
    ];
});

$factory->define(App\Product::class, function (Faker\Generator $faker) {
    return [
        'code'           => $faker->unique()->randomNumber,
        'sale_price'     => 200000,
        'regular_price'  => 290000,
        'wholesale_price'=> 100000,
        'original_price' => 50000,
        'weight'         => 500,
        'category_id'    => 2,
        'user_id'        => 1,
        'type'           => 'san-pham',
    ];
});

$factory->define(App\ProductLanguage::class, function (Faker\Generator $faker) {
    $name                    = $faker->name;
    $meta_seo['title']       = $name;
    $meta_seo['keywords']    = $name;
    $meta_seo['description'] = $name;
    return [
        'title'       => $name,
        'slug'        => str_slug($name),
        'description' => $faker->text,
        'contents'    => $faker->paragraph,
        'meta_seo'    => $meta_seo,
        'language'    => 'vi',
    ];
});

$factory->define(App\Post::class, function (Faker\Generator $faker) {
    static $number = 1;
    return [
        'category_id'    => 1,
        'user_id'        =>	1,
        'type'           =>	'tin-tuc',
    ];
});

$factory->define(App\PostLanguage::class, function (Faker\Generator $faker) {
    $name                    = $faker->name;
    $meta_seo['title']       = $name;
    $meta_seo['keywords']    = $name;
    $meta_seo['description'] = $name;
    return [
        'title'       => $name,
        'slug'        => str_slug($name),
        'description' => $faker->text,
        'contents'    => $faker->paragraph,
        'meta_seo'    => $meta_seo,
        'language'    => 'vi',
    ];
});
