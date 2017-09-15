<?php

use Illuminate\Database\Seeder;

class userTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $userQuantity= 10;
        factory(App\User::class, $userQuantity)->create()->each(function($u) {
	      $u->githubusers()->save(factory(App\Githubuser::class)->make());
	  });
	    //factory(App\User::class, $userQuantity)->create();
    }
}
