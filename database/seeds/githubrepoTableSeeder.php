<?php

use Illuminate\Database\Seeder;

class githubrepoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run()
	{ 
	   $quantity = mt_rand(1,10);
	   factory(App\Githubrepo::class, $quantity)->create()->each(function($u) {
	    $u->repobranches()->save(factory(App\Repobranche::class)->make());
	    $u->repocommits()->save(factory(App\Repocommit::class)->make());
	    $u->repocontributors()->save(factory(App\Repocontributor::class)->make());
	    $u->repolangs()->save(factory(App\Repolang::class)->make());
	   });
	   //factory(App\Githubrepo::class, $quantity)->create();
	}
}
