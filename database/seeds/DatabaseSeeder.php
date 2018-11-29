<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UserTableSeeder::class);
        $this->call(LocalesTableSeeder::class);
        $this->call(RolesTableSeeder::class);
        $this->call(LessonTemplatesTableSeeder::class);
        $this->call(LessonTemplateItemsTableSeeder::class);
        $this->call(LessonsTableSeeder::class);
    }
}
