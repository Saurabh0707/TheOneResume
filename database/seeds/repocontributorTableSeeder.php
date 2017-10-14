<?php

use Illuminate\Database\Seeder;

class repocontributorTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $quantity = mt_rand(1,10);
        factory(App\Repocontributor::class, $quantity)->create();
    }
}
