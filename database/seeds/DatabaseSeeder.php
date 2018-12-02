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
        $this->call(ArchiveReasonsTableSeeder::class);
        $this->call(CreateProjectsSeeder::class);
    }
}
