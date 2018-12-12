<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Participant;
use App\User;
use App\Role;
use App\ParticipantHistory;
class ParticipantController extends Controller
{
    public function items(Request $request)
    {
        return Participant::filter(Input::all())->with([
            'user.home.district.region'
        ])->orderBy('id', 'desc')->paginate(20);
    }
    public function item($id)
    {
        return Participant::where('id', $id)
            ->with([
                'user.home.district.region',"archive_reason","user.localities","groups.lessons.pages.materials"
            ])->first();
    }
    public function archive($id,Request $request)
    {
        $user = Participant::where("user_id",$id)->first();
        if(!$user->in_archive){
            $this->validate($request, [
                'archive_reason_id' => 'required',
            ]);
            $user->archive_reason_id = $request->get("archive_reason_id");
            User::where("id",$user->user_id)->update([
                "deleted_at"=>\Carbon\Carbon::now()
            ]);
        }else{
            User::where("id",$user->user_id)->update([
                "deleted_at"=>NULL
            ]);
            $user->archive_reason_id = NULL;
        }
        $user->in_archive=$user->in_archive? False:True;
        $user->save();
        User::where("id",$user->user_id)->update([
            "deleted_at"=>\Carbon\Carbon::now()
        ]);
    }
    public function fullDelete($id)
    {
        $user = Participant::find($id);
        $user->user()->delete();
        Participant::destroy($id);
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
            $user->$key=$value;
            $user->save();
        }
        else{
            $user = Participant::find($id);
            $user->$key=$value;
            $user->save();
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
        }
    }
    public function removeDocument($id,request $request)
    {
        $user = Participant::find($id);
        $type=$request->get('type');
        $old = $user->$type;
        if($type){
            $user->$type = NULL;
            $user->save();
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
            'locality_id'=>'integer',
        ]);
        //Создание или обновления пользователя
        $newUser =$request->get("user_id") ? User::find($request->get("user_id")):new User;
        $newUser->first_name = $request->get("new_first_name");
        $newUser->last_name=$request->get("new_last_name");
        $newUser->patronymic= $request->get("new_patronymic");
        $newUser->gender=$request->get("new_gender");
        $newUser->iin=$request->get("new_iin");
        $newUser->phone=$request->get("new_phone");
        $newUser->email=$request->get("new_email");
        // $newUser->locality_id=$request->get('locality_id');
        $newUser->role_id=Role::where("name","Participant")->first()->id;
        $newUser->save();
        $newParticipant = $request->get('id')?Participant::find($request->get("id")):new Participant;
        $newParticipant->disability = $request->get("new_disability");
        $newParticipant->user_id=$newUser->id;
        $newParticipant->save();
        //создание или обновлени

    }
    public function getArchiveReasonsList(){
        $role_id = Role::where("name","Participant")->first();
        $responce = \App\ArchiveReasons::where("role_id",$role_id)->get();
        return response()->json(['status'=>'success','data'=>$responce],200);
    }

}
