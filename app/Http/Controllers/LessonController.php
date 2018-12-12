<?php

namespace App\Http\Controllers;

use App\Lesson;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LessonController extends Controller
{
    public function item($id) {
        $lesson = Lesson::with('pages.materials',
            'homework','group.participants.user','group.participants')->find($id);
        return $lesson;
    }

    public function delete($id)
    {
        $lesson = Lesson::find($id);
        Lesson::destroy($id);
    }

    public function save(Request $request){
        $this->validate($request, [
            'passed' => 'required|boolean',
            'datetime' => 'required|date_format:Y-m-d H:i:s',
            'without_date' => 'required|boolean',
            'is_full' => 'required|boolean',
            'is_started' => 'required|boolean',
            'order'=>'required|integer',
            'access_homework'=>'required',
            'access_tasks'=>"required",
            'access_tests'=>"required",
            'access_offset'=>"required",
            'title'=>"required",
            'group_id'=>"required|integer",
            'lesson_template_id'=>"required|integer"
        ]);
        if($request->get('id'))$lesson = Lesson::find($request->get("id"));
        else $lesson=new Lesson;
        $is_started=false;
        if($lesson->is_started!==$request->get("is_started") && $request->get("is_started")){
            $is_started=true;
        }
        $lesson->passed=$request->get("passed");
        $lesson->datetime=$request->get("datetime");
        $lesson->without_date=$request->get("without_date");
        $lesson->is_full=$request->get("is_full");
        $lesson->is_started=$request->get("is_started");
        $lesson->order=$request->get("order");
        $lesson->group_id=$request->get("group_id");
        $lesson->access_homework=$request->get('access_homework');
        $lesson->access_tasks=$request->get('access_tasks');
        $lesson->lesson_template_id=$request->get('lesson_template_id');
        $lesson->access_tests=$request->get('access_tests');
        $lesson->access_offset=$request->get('access_offset');
        $lesson->access_offset_value=$request->get('access_offset_value');
        $lesson->title=$request->get('title');
        $lesson->manual=$request->get('manual');
        $lesson->save();
        if($is_started){
            $new_lesson=Lesson::with("group.participants")->find($lesson->id);
            $part_ids=$new_lesson->group->participants->pluck('id');
            foreach ($part_ids as $participant_id){
                DB::table('participant_attendances')->insert([
                    "participant_id"=>$participant_id,
                    "lesson_id"=>$new_lesson->id,
                    "presented"=>true
                ]);
            }
        }
    }


    //for import lessons from lesson template
    public function  getPseudoTime($id){
        $group = \App\Group::find($id);
        $project_settings = \App\ProjectSettings::find($group->project_id);
        $days_in_project=$project_settings->days_number;

        $lessons=[];
        $first_day = new Carbon(date($group->start_date));
        $fist_day_dublicate = new Carbon(date($group->start_date));
        $year=$fist_day_dublicate->year;

        //checking dates and weekend
        for( ;count($lessons)<$days_in_project; ){
            if(\App\Holiday::isHoliday($first_day,$year)===false){
                array_push($lessons,["date"=>$first_day->format('d-m-y'),"available"=>true]);
            }
            $first_day->addDay(1);
        }
        return $lessons;
    }

    //Yersultan
    //Function needed to get the list of attendances by lesson id
    public function getLessonAttendance($id){
        return DB::table('participant_attendances')->where(["lesson_id"=>$id])->get();
    }

    //Yersultan
    //Function that needed for managing attendance
    public function setAttendance(Request $request){
        Log::info("me here");
        Log::info($request);
        $this->validate($request, [
            "lesson_id"=>"required|integer",
            "participant_id"=>"required|integer",
            "presented"=>"required|integer",
        ]);
        if($request->get("id")){
            $attendance = DB::table('participant_attendances')->where("id",$request->get("id"))->update([
                "presented"=>$request->get("presented")
            ]);
        }else{
            $attendance = DB::table('participant_attendances')->create([
                "lesson_id"=>$request->get("lesson_id"),
                "participant_id"=>$request->get("participant_id"),
                "presented"=>$request->get("presented"),
            ]);
        }
        return $attendance;
    }
}
