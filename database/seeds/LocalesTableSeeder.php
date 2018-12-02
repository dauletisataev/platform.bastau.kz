<?php

use Illuminate\Database\Seeder;

class LocalesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Locale::create([
            'name' => "ru",
        ]);
        \App\Locale::create([
            'name' => "kz",
        ]);
    }
}
