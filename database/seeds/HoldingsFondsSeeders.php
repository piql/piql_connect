<?php

use Illuminate\Database\Seeder;

class HoldingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(\App\Holding::class, 10)->create();
    }
}
