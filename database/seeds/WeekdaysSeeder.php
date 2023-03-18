<?php

use App\Weekday;
use Illuminate\Database\Seeder;

class WeekdaysSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $days = [ 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        foreach ($days as $key => $day) {
            Weekday::firstOrCreate(['name' => $day]);
        }
    }
}
