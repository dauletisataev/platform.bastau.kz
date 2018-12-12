<?php

namespace App;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
/*Yersultan, needed for creating schedule by knowing holidays
 * */
class Holiday extends Model
{
    public $fillable=['name','start_day','start_month','days_number'];
    public $timestamps=false;
    public function scopeFilter($query, $filters)
    {
        if(isset($filters['search_text'])){
            $search_text=$filters['search_text'];
            $query->where('name','like','%'.$search_text.'%');
        }
        if($filters['month']){
            $query->where('start_month',$filters['month']);
        }
        return $query;
    }

    //function for checking if the date is a holiday
    public static function isHoliday($check_date,$year){
        $holidays=\App\Holiday::all();

        $weekends = ["Saturday","Sunday"];

        //check for holiday
        foreach ($holidays as $holiday){
            $start_date=Carbon::create($year,$holiday->start_month,$holiday->start_day,0,0,0);
            $end_date= Carbon::create($year,$holiday->start_month,$holiday->start_day,0,0,0)->addDays($holiday->days_number-1);
            $holidays_period = CarbonPeriod::create(
                $year."-".$start_date->month."-".$start_date->day,
                $year."-".$end_date->month."-".$end_date->day
                );
            foreach ($holidays_period as $key => $date) {
                    foreach($weekends as $weekend){
                        if($date->englishDayOfWeek === $weekend){
                            $end_date=$end_date->addDay();
                        }
                    }
            }
            $end_date->addSecond();
            if($check_date->between($start_date,$end_date)){
                return true;
            }
        }
        //check if its weekend
        foreach($weekends as $weekend){
            if($check_date->englishDayOfWeek === $weekend)return true;
        }
        return false;
    }
}
