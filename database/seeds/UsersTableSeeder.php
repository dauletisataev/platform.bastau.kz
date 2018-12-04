<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\User::create([
            'first_name' => "Developer",
            'last_name' => "Developer",
            'patronymic' => "Developer",
            'email' => 'developer@gmail.com',
            'phone'=>"8 (777) 777 77 77",
            'password' => bcrypt('123456'),
            'role_id' => 1,
            'gender'=>'M',
            'iin'=>'901030500500',
            'lang_id' => 1,
        ]);

        \App\User::create([
            'first_name' => "Business",
            'last_name' => "Trainer",
            'patronymic' => "Trainerovich",
            'email' => 'trainer@business.com',
            'phone'=>"8 (800) 555 35 35",
            'password' => bcrypt('123456'),
            'role_id' => 2,
            'gender'=>'M',
            'iin'=>'901201450902',
            'lang_id' => 1,
        ]);
    }
}
