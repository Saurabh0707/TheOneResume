<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //App\User::truncate();
        
        
         $this->call(userTableSeeder::class);
         //$this->call(githubuserTableSeeder::class);
          $this->call(githubrepoTableSeeder::class);
         // $this->call(repobrancheTableSeeder::class);
         // $this->call(repocommitTableSeeder::class);
         // $this->call(repocontributorTableSeeder::class);
         // $this->call(repolangTableSeeder::class);
	}
}
