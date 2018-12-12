<?php

use Illuminate\Database\Seeder;

class ProjectSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $project = \App\Project::find(1);
        \App\ProjectSettings::create([
            "project_id"=>$project->id,
            "hours_per_day"=>4,
            "days_number"=>20
        ]);
    }
}
