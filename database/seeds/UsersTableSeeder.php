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
        //Yersultan: creation of  administrator
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
            'locality_id'=>1
        ]);

        //Yersultan: creation of user for trainer
        \App\User::create([
            'first_name' => "Андрей",
            'last_name' => "Странный",
            'patronymic' => "Чел",
            'email' => 'somestrangeemail@gmail.com',
            'phone'=>"8 (777) 708 56 48",
            'password' => bcrypt('123456'),
            'role_id' => 3,
            'gender'=>'M',
            'iin'=>'901030600500',
            'lang_id' => 1,
            'locality_id'=>1
        ]);
        \App\BusinessTrainer::create([
           "id"=>1,
           "user_id"=>\App\User::where("first_name","Андрей")->first()->id,
           "created_at"=>\Carbon\Carbon::now(),
            "updated_at"=>\Carbon\Carbon::now()
        ]);
    }
}
