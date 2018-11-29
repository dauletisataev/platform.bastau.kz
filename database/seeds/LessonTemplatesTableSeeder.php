<?php

use Illuminate\Database\Seeder;

class LessonTemplatesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\LessonTemplate::create([
            'name' => str_random(12)
        ]);
    }
}
