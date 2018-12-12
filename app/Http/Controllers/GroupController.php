<?php

namespace App\Http\Controllers;
use App\Participant;
use App\ProjectSettings;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use App\Group;
use App\GroupHistory;
use App\ParticipantHistory;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
/** Created by Yersultan
 * Needed for managing groups
*/
class GroupController extends Controller
{
    public function item($id)
    {
        return Group::where('id', $id)
            ->with([
                'participants.user',
                'histories',
                'locality.district.region',
                'lessons.pages.materials'
            ])->first();
    }
    public function items(request $request){
        $groups = Group::filter(Input::all())->with(['participants.user','locality.district.region','lessons.pages.materials'])->orderBy('id', 'desc');
        /**Daulet
         * координатор получает только сущности с его локалити.
         * 
         */
        // if($request->user()->hasRole('coordinator')){
        //     $user_region_id = $request->user()->home->district->region->id;
        //     $groups->whereHas('locality', function($query) use ($user_region_id) {
        //         $query->whereHas('district', function($q) use ($user_region_id) {
        //             $q->where('region_id',$user_region_id);
        //         });
        //     });
        // }
        // if($request->user()->hasRole('business-trainer')){
        //     $trainer_user_id = $request->user()->id;
        //     $groups->whereHas('trainer', function($query) use ($trainer_user_id) { 
        //             $query->where('user_id',$trainer_user_id); 
        //     });
        // }
        return $groups->paginate(20);
    }
    public function saveGroup(Request $request){
        $this->validate($request, [
            'project_id' => 'required|integer',
            'start_date' => 'required|date',
            'language' => 'required',
            'capacity' => 'required',
            'trainer' => 'required|integer',
            'locality_id'=>"required|integer"
        ]);
        $group  = $request->get('id')? Group::find($request->get('id')):new Group;
        $group->project_id = $request->get('project_id') ;
        $group->start_date = $request->get('start_date') ;
        $group->language = $request->get('language') ;
        $group->capacity = $request->get('capacity') ;
        $group->online = $request->get('online')?true:false ;
        $group->trainer()->associate($request->get('trainer')); //Yersultan: this line isn't mine
        $group->locality_id = $request->get('locality_id');
        $group->save();
    }

    public function importLessonsFromTemplate($id, Request $request){
        $lessons= $request->get("data");
        $project_settings=ProjectSettings::where("project_id",Group::find($id)->project_id)->first();
        foreach($lessons as $lesson){
            $time=Carbon::createFromFormat('d-m-y H:i',$lesson['date'].' '.$lesson['time']);
            $new_lesson=\App\Lesson::create([
                "passed"=>false,
                "datetime"=>$time,
                "without_date"=>false,
                "duration"=>$project_settings->hours_per_day,
                "is_full"=>false,
                "is_started"=>false,
                "group_id"=>$id,
                "order"=>$lesson["order"],
                "access_homework"=>$lesson["access_homework"],
                "access_tasks"=>$lesson["access_tasks"],
                "access_tests"=>$lesson["access_tests"],
                "access_offset"=>$lesson["access_offset"],
                "access_offset_value"=>$lesson["access_offset_value"],
                "title"=>$lesson["title"],
                "access_offset_type"=>$lesson["access_offset_type"],
                "manual"=>$lesson["manual"],
                "lesson_template_id"=>$lesson["lesson_template_id"]
            ]);
            $lesson_template_item = \App\LessonTemplateItem::with(["pages.materials"])->find($lesson["id"]);
            foreach ($lesson_template_item->pages as $page) {
                $new_page = \App\MaterialPage::create([
                    "lesson_id"=>$new_lesson->id,
                    "name"=>$page->name,
                    "sort"=>$page->sort
                ]);
                foreach ($page->materials as $material){
                        \App\Material::create([
                        "content"=>$material->content,
                        "material_order"=>$material->material_order,
                        "material_type_id"=>$material->material_type_id,
                        "additional_content"=>$material->additional_content,
                        "material_page_id"=>$new_page->id
                    ]);
                }
            }
        }
    }

    public function getNotMyParticipants($id){
        $group=Group::where('id', $id)
            ->with([
                'participants',
            ])->first();
        $members= $group->participants->pluck('id');
        $users =   Participant::with("user")->whereNotIn("id",$members)->get();
        return response()->json(['status' => 'success', 'data' => $users], 200);
    }


    public function addParticipants($id,Request $request){
        $group = Group::with("participants")->find($id);
        $this->validate($request, [
            'participants' => 'required',
        ]);

        //check if the number exceeds
        $remaining_slots=$group->capacity - count($group->participants()->get());


        if(count($request->get('participants')) > $remaining_slots){
            return response()->json(['status' => 'failed', 'errors' => ["participants"=>"too_many_participants"]], 400);
        }
        foreach($request->get('participants') as $participant){
            $oldparticipant=Participant::find($participant);
            $contains=$group->participants()->where("participant_id",$oldparticipant->id)->count();
            if($contains===0){
                $group->participants()->save($oldparticipant);
                $group->save();
                ParticipantHistory::create([
                    'action' => 'added_to_group',
                    'new_value' => $group->id ,
                    'filed_name' =>"group_id",
                    'participant_id' => $oldparticipant->id,
                    "actor_user" =>request()->user()->id
                ]);
                GroupHistory::create([
                    'action' => 'new_participant',
                    'new_value' => $oldparticipant->id ,
                    'filed_name' =>"participant_id",
                    'group_id' => $group->id,
                    "actor_user" =>request()->user()->id
                ]);
            }
        }
    }


    public function removeParticipant($id,Request $request){
        $group = Group::with("participants")->find($id);
        $this->validate($request, [
            'participant_id' => 'required|integer',
        ]);
        $oldparticipant = $group->participants()->where("participant_id",$request->get('participant_id'))->first();
        ParticipantHistory::create([
            'action' => 'removed_from_group',
            'new_value' => $group->id ,
            'filed_name' =>"group_id",
            'participant_id' => $oldparticipant->id,
            "actor_user" =>request()->user()->id
        ]);
        GroupHistory::create([
            'action' => 'removed_participant',
            'new_value' => $oldparticipant->id ,
            'filed_name' =>"participant_id",
            'group_id' => $group->id,
            "actor_user" =>request()->user()->id
        ]);
        $group->participants()->detach($oldparticipant->id);
    }


    public function archive($id, Request $request){
        $group=Group::find($id);
        $group->in_archive = $group->in_archive? False:True;
        $group->save();
    }
}
