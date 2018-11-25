<?php

use Illuminate\Database\Seeder;

class ArchiveReasonsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $id = \App\Role::where("name","Participant")->first();
        \App\ArchiveReasons::create([
            'reason' => "Бездействие в течении 5 минут !",
            'locale_id' => 1,
            'role_id' => $id
        ]);
        \App\ArchiveReasons::create([
            'reason' => "Бездействие в течении 10 минут !",
            'locale_id' => 1,
            'role_id' => $id
        ]);
    }
}
