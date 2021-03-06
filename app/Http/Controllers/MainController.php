<?php
/**
 * Created by PhpStorm.
 * User: Yurituski
 * Date: 19.11.2018
 * Time: 23:24
 */

namespace App\Http\Controllers;


use App\Locality;
use App\District;
use App\Project;
use App\Role;
use App\Region;
use App\ArchiveReasons;
use App\BusinessTrainer;
use Illuminate\Support\Facades\Request;

class MainController extends Controller
{

    public function data(Request $request)
    {
        $data = [];
        $data['roles'] = Role::all();
        $data['archived_reasons'] = ArchiveReasons::all();
        $data['trainers'] = BusinessTrainer::with('user')->get();
        $data['regions'] = Region::all();
        $data['districts'] = District::all();
        $data['localities']=Locality::all();
        $data['genders'] = ['M', 'F'];
        $data['projects']=Project::all();
        $data['lesson_template_types']=[
            ["name"=>"online","value"=>1],
            ["name"=>"teacher","value"=>2],
            ["name"=>"solo","value"=>3]
        ];
        return $data;
    }
}