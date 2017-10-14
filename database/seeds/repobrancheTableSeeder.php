<?php

use Illuminate\Database\Seeder;

class repobrancheTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $quantity = mt_rand(1,10);
	  	factory(App\Repobranche::class, $quantity)->create();
    }
}
