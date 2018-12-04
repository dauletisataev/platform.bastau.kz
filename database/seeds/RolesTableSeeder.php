<?php

use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Role::create([
            'name' => "administrator",
            'description' => 'Администратор',
            'instrumental'=> "для администраторов",
        ]);
        \App\Role::create([
            'name' => "Participant",
            'description' => 'участник',
            'instrumental'=>"для участника",
        ]);
        \App\Role::create([
            'name' => 'business-trainer',
            'description' => 'Бизнес-тренер',
            'instrumental' => 'для бизнес-тренера',
        ]);
    }
}
