<?php

use Illuminate\Database\Seeder;

class LessonTemplateItemsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $lessonTemplate  = \App\LessonTemplate::orderBy('id','desc')->first();
        \App\LessonTemplateItem::create([
            'lesson_template_id' => $lessonTemplate->id,
            'title' => str_random(12)
        ]);
    }
}
