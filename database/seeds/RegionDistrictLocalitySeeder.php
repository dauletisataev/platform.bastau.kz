<?php

use Illuminate\Database\Seeder;

class RegionDistrictLocalitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Region::create([
            "name"=>"Akmola"
        ]);
        $akmola = \App\Region::where("name","Akmola")->first();
        \App\District::create([
            "name"=>"Esil",
            "region_id"=>$akmola->id
        ]);
        $esil = \App\District::where("name","Esil")->first();
        \App\Locality::create([
            "name"=>"Astana",
            "district_id"=>$esil->id
        ]);
    }
}
