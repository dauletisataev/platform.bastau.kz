<?php

namespace App\Http\Controllers;
use App\Participant;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use App\Group;
use App\GroupHistory;
use App\ParticipantHistory;
use Illuminate\Support\Facades\Log;
class GroupController extends Controller
{
    public function item($id)
    {
        return Group::where('id', $id)
            ->with([
                'participants.user',
                'histories',
                'locality.district.region'
            ])->first();
    }
    public function items(){
        return Group::filter(Input::all())->with(['participants.user'])->orderBy('id', 'desc')->paginate(20);
    }
    public function saveGroup(Request $request){
        $this->validate($request, [
            'project_id' => 'required|integer',
            'start_date' => 'required|date',
            'language' => 'required',
            'capacity' => 'required',
            'online'=>'required',
            'locality_id'=>"required|integer"
        ]);
        $group  = $request->get('id')? Group::find($request->get('id')):new Group;
        $group->project_id = $request->get('project_id') ;
        $group->start_date = $request->get('start_date') ;
        $group->language = $request->get('language') ;
        $group->capacity = $request->get('capacity') ;
        $group->online = $request->get('online') ;
        $group->locality_id = $request->get('locality_id');
        $group->save();
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
        $group->participants()->where("participant_id",$request->get('participant_id'))->detach();
    }
    public function archive($id, Request $request){
        $group=Group::find($id);
        $group->in_archive = $group->in_archive? False:True;
        $group->save();
    }
}
