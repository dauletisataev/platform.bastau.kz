<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Participant;
use App\User;
use App\ParticipantHistory;
use App\Role;
use Illuminate\Support\Facades\Log;
class ParticipantController extends Controller
{
    public function items(Request $request)
    {
        return Participant::filter(Input::all())->with([
            'user'
        ])->orderBy('id', 'desc')->paginate(20);
    }

    public function item($id)
    {
        return Participant::where('id', $id)
            ->with([
                'user',"archive_reason"
            ])->first();
    }

    public function delete($id,Request $request)
    {
        $user = Participant::where("user_id",$id)->first();
        $user->in_archive= true;
        $user->archive_reason_id = $request->get("archive_reason_id");
        $user->save();
        User::where("id",$user->user_id)->update([
            "deleted_at"=>\Carbon\Carbon::now()
        ]);
        ParticipantHistory::create([
            "action" => "Участник архивирован",
            "new_value" =>$user->archive_reason_id,
            "actor_user" =>$request->user()->id,
            "participant_id" => $user->id,
            "archive_reason_id"=>$request->get("archive_reason_id")
        ]);
    }
    public function fullDelete($id)
    {
        $user = Participant::find($id);
        ParticipantHistory::where("participant_id",$id)->delete();
        $user->user()->delete();
        Participant::destroy($id);
    }

    public function activate($id,Request $request)
    {
        $user = Participant::where("user_id",$id)->first();
        $user->in_archive= false;
        $user->archive_reason_id = NULL;
        $user->save();
        User::where("id",$user->user_id)->update([
            "deleted_at"=>NULL
        ]);
        ParticipantHistory::create([
            "action" => "Участник активирован",
            "field_name" =>"archive_reason_id",
            "old_value" => true,
            "new_value" =>false,
            "actor_user" =>$request->user()->id,
            "participant_id" => $user->id
        ]);
    }

    public function getHistories($id){
        $responce = ParticipantHistory::where("participant_id",$id)->get();
        return response()->json(['status'=>'success','data'=>$responce],200);
    }

    public function saveField($id,Request $request){
        $user_id=$request->get("user_id");
        $key = $request->get("key");
        $value = $request->get("value");
        if($user_id){
            $user = User::find($user_id);
            $old = $user->$key;
            $new = $value;
            $user->$key=$value;
            $user->save();
            ParticipantHistory::create([
                "action"=>"Изменена персональная информация",
                "field_name" =>$key,
                "old_value" => $old,
                "new_value" =>$new,
                "actor_user" =>$request->user()->id,
                "participant_id" => $id
            ]);
        }
        else{
            $user = Participant::find($id);
            $old = $user->$key;
            $new = $value;
            $user->$key=$value;
            $user->save();
            ParticipantHistory::create([
                "action" => "Изменена персональная информация",
                "field_name" =>$key,
                "old_value" => $old,
                "new_value" =>$new,
                "actor_user" =>$request->user()->id,
                "participant_id" => $id
            ]);
        }

    }

    public function saveDocument($id,request $request)
    {
        $this->validate($request, [
            'file' => 'image64:jpeg,jpg,png',
        ]);
        $user = Participant::find($id);
        $type=$request->get('type');
        $file=$request->file('file');
        $old = $user->$type;
        if($file && $type){
            $user->$type = $file;
            $user->save();
            ParticipantHistory::create([
                "action" => "Добавлен документ",
                "field_name" =>$type,
                "old_value" => $old,
                "new_value" =>$file,
                "actor_user" =>$request->user()->id,
                "participant_id" => $user->id
            ]);
        }
    }
    public function removeDocument($id,request $request)
    {
        $user = Participant::find($id);
        $type=$request->get('type');
        Log::info($type);
        $old = $user->$type;
        if($type){
            $user->$type = NULL;
            $user->save();
            ParticipantHistory::create([
                "action" => "Документ удален",
                "field_name" =>$type,
                "old_value" => $old,
                "new_value" =>"удален",
                "actor_user" =>$request->user()->id,
                "participant_id" => $user->id
            ]);
        }
    }

    public function save(Request $request)
    {
        $this->validate($request, [
            'new_first_name' => 'required|string|max:255',
            'new_last_name' => 'required|string|max:255',
            'new_patronymic' => 'required|string|max:255',
            'new_gender' => 'required',
            'new_disability' => 'required|integer',
            'new_phone' => 'required|string',
            'new_iin' =>'required|integer',
            'new_photo' => 'string',
            'new_email' => 'required|string',
        ]);
        if($request->get('user_id')){
            $oldUser = User::where("id",$request->get('user_id'))->first();
            $usr = User::where("id",$request->get('user_id'))->first()->update([
                "first_name"=>$request->get("new_first_name"),
                "last_name"=>$request->get("new_last_name"),
                "patronymic"=> $request->get("new_patronymic"),
                "gender"=>$request->get("new_gender"),
                "iin"=>$request->get("new_iin"),
                "phone"=>$request->get("new_phone"),
                "email"=>$request->get("new_email"),
                "role_id"=>Role::where("name","Participant")->first()->id
            ]);
            $checklist = ["first_name","last_name","patronymic","gender","iin","phone","email","role_id"];
            foreach ($checklist as $key){
                if($oldUser->$key !== $usr->$key){
                    ParticipantHistory::create([
                        "action" => "Изменена персональная информация",
                        "field_name" =>$key,
                        "old_value" => $oldUser->$key,
                        "new_value" =>$usr->$key,
                        "actor_user" =>$request->user()->id,
                        "participant_id" => $usr->id
                    ]);
                }
            }
        }else{
            $usr = User::create([
                "first_name" => $request->get("new_first_name"),
                "last_name" => $request->get("new_last_name"),
                "patronymic" => $request->get("new_patronymic"),
                "gender" => $request->get("new_gender"),
                'iin' => $request->get("new_iin"),
                "phone" => $request->get("new_phone"),
                "email" => $request->get("new_email"),
                "role_id" => Role::where("name","Participant")->first()->id
            ]);
        }
        if($request->get("id")){
            $participant = Participant::find($request->get("id"));
            if($participant->disability !== $request->get("new_disability")){
                $old = $participant->disability;
                $participant->disability = $request->get("new_disability");
                $participant->save();
                ParticipantHistory::create([
                    "action" => "Изменена персональная информация",
                    "field_name" =>"disability",
                    "old_value" => $old,
                    "new_value" =>$request->get("new_disability"),
                    "actor_user" =>$request->user()->id,
                    "participant_id" => $participant->id
                ]);
            }
            if($participant->user_id !== $usr->id){
                $old = $participant->user_id;
                $participant->user_id = $usr->id;
                $participant->save();
                ParticipantHistory::create([
                    "action" => "Изменена персональная информация",
                    "field_name" =>"user_id",
                    "old_value" => $old,
                    "new_value" =>$usr->id,
                    "actor_user" =>$request->user()->id,
                    "participant_id" => $participant->id
                ]);

            }
        }else{
            $participant= new Participant();
            $participant->disability = $request->get("new_disability");
            $participant->user_id=$usr->id;
            $participant->save();
            ParticipantHistory::create([
                "action" => "Создан новый участник",
                "field_name" =>"user_id",
                "old_value" => NULL,
                "new_value" =>$participant,
                "actor_user" =>$request->user()->id,
                "participant_id" => $participant->id
            ]);

        }
    }

    public function getArchiveReasonsList(){

        $role_id = Role::where("name","Participant")->first();
        $responce = \App\ArchiveReasons::where("role_id",$role_id)->get();
        return response()->json(['status'=>'success','data'=>$responce],200);
    }

}
