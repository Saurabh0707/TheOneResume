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
use Faker\Provider\DateTime;

$faker = Faker\Factory::create();

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
    ];
});

$factory->define(App\Githubuser::class, function (Faker\Generator $faker) {
    return [
        'username' => $faker->name,
        'html_url' => $faker->url,
        'name' => $faker->name,
        'company' => $faker->name,
        'location' => $faker->address,
        'user_created_at' => $faker->dateTime(),
        'user_created_at' => $faker->dateTime(),
        'user_updated_at' => $faker->dateTime(),
        'public_repos' => $faker->randomDigit,
        'public_gists' => $faker->randomDigit,
    ];
});
$factory->define(App\Githubrepo::class, function (Faker\Generator $faker) {
    return [
        'githubuser_id' => function() { return App\User::find('1')->id ?: factory(App\User::class)->create()->id;},
        'owner' => $faker->name,
        'name' => $faker->name,
        'html_url' => $faker->url,
        'clone_url' => $faker->url,
        'repo_created_at' => $faker->dateTime(),
        'repo_updated_at' => $faker->dateTime(),
        'repo_pushed_at' => $faker->dateTime(),
        'public_repos' => $faker->randomDigit,
        'no_of_commits' => $faker->randomDigit,
        'no_of_branches' => $faker->randomDigit,
        'no_of_pullrequests' => $faker->randomDigit,
        'no_of_contributors' => $faker->randomDigit,
    ];
});

$factory->define(App\Repobranche::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
       ];
});
$factory->define(App\Repocommit::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'sha'   => sha1(str_shuffle('ghjklkjhgfghjklkjhkjhgjkjhg')),
        'author' => $faker->name,
        'committer' => $faker->name,
        'message' =>$faker->sentence(5),
        'commit_created_at' => $faker->dateTime(),
        'commit_updated_at' => $faker->dateTime(),
    ];
});
$factory->define(App\Repocontributor::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'contributions' => $faker->randomDigit,
        ];
});
$factory->define(App\Repolang::class, function (Faker\Generator $faker) {
    return [
        'name' => 'PHP',
        'lines'=> $faker->randomDigit,
    ];
});
