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
            'name' => "Developer",
            'email' => 'developer@gmail.com',
            'password' => bcrypt('123456'),
            'role_id' => 1,
        ]);
    }
}