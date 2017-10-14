<?php

use Illuminate\Database\Seeder;

class repocommitTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $quantity = mt_rand(1,10);
        factory(App\Repocommit::class, $quantity)->create();
    }
}
