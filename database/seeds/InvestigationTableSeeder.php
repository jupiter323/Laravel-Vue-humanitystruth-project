<?php

use Illuminate\Database\Seeder;

class InvestigationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Investigation::class, 20)->create();
    }
}
