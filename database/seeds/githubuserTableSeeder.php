<?php

use Illuminate\Database\Seeder;

class githubuserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $quantity = mt_rand(1,10);
	  factory(App\Githubuser::class, $quantity)->create()->each(function($u) 
	  {
		$u->githubrepos()->factory(App\Githubrepo::class, $quantity)->create()->each(function($v)
	    		{
	    			$v->repobranches()->save(factory(App\Repobranches::class)->make());
	    			$v->repocommits()->save(factory(App\Repocommit::class)->make());
	    			$v->repocontributors()->save(factory(App\Repocontributor::class)->make());
	    			$v->repolangs()->save(factory(App\Repolang::class)->make());

	    		});  	
   		});
      //factory(App\Githubuser::class, 10)->create();
	}

}
