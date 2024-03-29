<?php

use Illuminate\Database\Seeder;

class CuisineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $cuisines = config('constants.cuisines');
        foreach ($cuisines as $key => $cuisine) {
            \App\Cuisine::firstOrCreate(['name' => $cuisine]);
        }
    }
}
