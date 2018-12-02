<?php

use Illuminate\Database\Seeder;

class CreateProjectsSeeder extends Seeder
{
    /**
     * Yersultan
     * Created for adding projects to the database
     */
    public function run()
    {
        \App\Project::create([
            "name"=>"Бизнес-Советник"
        ]);
    }
}
