<?php

use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
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
            'phone' => "8 (777) 777 77 77",
            'role_id' => 1,
<<<<<<< HEAD
            'gender'=>'M',
            'iin'=>'901030500500',
=======
>>>>>>> remotes/origin/master
            'lang_id' => 1,
        ]);
    }
}
