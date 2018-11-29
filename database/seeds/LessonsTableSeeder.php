<?php

use App\LessonTemplateItem;
use Illuminate\Database\Seeder;

class LessonsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $lessonTemplateItem  = LessonTemplateItem::orderBy('id','desc')->first();
        \App\Lesson::create([
            'lesson_template_item_id' => $lessonTemplateItem->id,
            'title' => str_random(12)
        ]);
    }
}
