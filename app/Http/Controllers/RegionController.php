<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Region;
use App\District;
use App\Locality;
class RegionController extends Controller
{
    /**
     * @param Request $request
     */
    public function save(Request $request){
        $type=$request->get("type");
        $this->validate($request, [
            'name' => 'required|string',
        ]);
        if($type==="district"){
            $this->validate($request, [
                'region' => 'string|required',
            ]);
            $region = Region::where("name",$request->get('region'))->first();
            District::create([
                "region_id"=>$region->id,
                "name"=>$request->get('name')
            ]);
            return response()->json(['status' => 'success'], 200);
        }
        else if($type==="locality"){
            $this->validate($request, [
                'district' => 'string|required',
                'region'=>'string|required'
            ]);
            $region = Region::where('name',$request->get('region'))->with('districts')->first();
            foreach ($region->districts as $district) {
                if($district->name === $request->get('district')){
                    Locality::create([
                        "district_id"=>$district->id,
                        "name"=>$request->get('name')
                    ]);
                    return response()->json(['status' => 'success'], 200);
                }
            }
        }
        else if($type==="region"){
            Region::create([
                "name"=>$request->get('name')
            ]);
            return response()->json(['status' => 'success'], 200);
        }
    }
    public function delete($id,Request $request){
        $type=$request->get("type");
        if($type==="district"){
            District::where("id",$id)->delete();
            return response()->json(['status' => 'success'], 200);
        }
        else if($type==="locality"){
            Locality::where("id",$id)->delete();
            return response()->json(['status' => 'success'], 200);
        }
        else if($type==="region"){
            Region::where("id",$id)->delete();
            return response()->json(['status' => 'success'], 200);
        }
    }

    public function update(Request $request){
        $type=$request->get('type');
        $item=$request->get('item');
        if($type==='locality'){
            $locality = Locality::find($item['id']);
            $locality->name=$item['name'];
            $locality->save();
        }
        if($type==='region'){
            $region = Region::find($item['id']);
            $region->name=$item['name'];
            $region->save();
        }
        if($type==='district'){
            $district = District::find($item['id']);
            $district->name=$item['name'];
            $district->save();
        }
    }
}
