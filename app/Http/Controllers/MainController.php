<?php
/**
 * Created by PhpStorm.
 * User: Yurituski
 * Date: 19.11.2018
 * Time: 23:24
 */

namespace App\Http\Controllers;


use App\Role;
use App\Region;
use App\ArchiveReasons;
use Illuminate\Support\Facades\Request;

class MainController extends Controller
{

    public function data(Request $request)
    {
        $data = [];
        $data['roles'] = Role::all();
        $data['archived_reasons'] = ArchiveReasons::all();
        $data['regions'] = Region::with(['districts.localities'])->get();
        return $data;
    }
}