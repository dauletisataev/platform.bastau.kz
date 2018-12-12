<?php

use Illuminate\Database\Seeder;

class HolidaysSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Holiday::create([
            'start_day'=>1,
            'start_month'=>1,
            'days_number'=>2,
            'name'=>'new_year',
        ]);

    }
}
