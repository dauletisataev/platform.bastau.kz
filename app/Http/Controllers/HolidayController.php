<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Holiday;
use DateTime;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Input;

/** Yersultan
 * Holidays controller
*/
class HolidayController extends Controller
{
    //is not used in front-end, but if there is such requirements,then we can request it
    public function item($id)
    {
        return Holiday::where('id', $id)->first();
    }
    public function items(){
        return Holiday::filter(Input::all())->orderBy('start_month', 'asc')->orderBy('start_day','asc')->paginate(20);
    }
    public function delete($id){
        Holiday::destroy($id);
        return response()->json(['status' => 'success'], 200);
    }
    public function save(Request $request){
        $this->validate($request, [
           "name"=>"required|string",
           "days_number"=>"required|integer",
           "start_day"=>"required|integer",
           "start_month"=>"required|integer"
        ]);
        $start_day= $request->get('start_day');
        $start_month = $request->get("start_month");
        // if day or month is less than 10, add 0 at the beginning
        if($start_day<10){
            $start_day='0'.$start_day;
        }
        if($start_month<10){
            $start_month='0'.$start_month;
        }
        $date=$start_day.'-'.$start_month;
        $d= DateTime::createFromFormat('d-m',$date);
        if(!$d ||$d->format('d-m')!==$date){
            return response()->json(['status' => 'failed'], 405);
        }
        //ended parsing part

        //saving or updating holiday starts here
        $holiday = $request->get('id')? Holiday::find($request->get('id')):new Holiday();
        $holiday->days_number = $request->get('days_number');
        $holiday->name=$request->get('name');
        $holiday->start_day=$start_day;
        $holiday->start_month=$start_month;
        $holiday->save();
    }
}
