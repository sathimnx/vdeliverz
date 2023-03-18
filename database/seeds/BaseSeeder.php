<?php

use Illuminate\Database\Seeder;

class BaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create Types
        $types = config('constants.types');
        foreach ($types as $key => $type) {
            \App\Type::firstOrCreate(['name' => $type]);
        }

        // Create Categories
        $categories = config('constants.categories');
        foreach ($categories as $key => $category) {
            \App\Category::firstOrCreate(['name' => $category]);
        }

    }
}
