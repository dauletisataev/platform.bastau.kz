<?php

namespace App\Observers;

use App\User;
use Carbon\Carbon;
use App\Participant;
use App\ParticipantHistory;
class UserObserver
{
    /**
     * Listen to the User created event.
     *
     * @param  User  $user
     * @return void
     */
    public function saving(User $user)
    {
        if (!$user->password){
            $password = bin2hex(openssl_random_pseudo_bytes(4));
           // $password = 123456;
            $user->password = bcrypt($password);

        }
    }
		public function updating(User $user)
    {
        $participant = Participant::where('user_id',$user->id);
        $oldUser = User::find($user->id);

        if ($oldUser) {
            //IIN check
            if ($user->isDirty(['iin'])) {
                ParticipantHistory::create([
                    'action' => 'change_personal_data',
                    'old_value'=>$user->iin,
                    'new_value' => $user->iin,
                    'filed_name' =>"iin",
                    'participant_id' => $participant->id,
                    "actor_user" =>request()->user()->id
                ]);
            }
            //First_name check
            if ($user->isDirty(['first_name'])) {
                ParticipantHistory::create([
                    'action' => 'change_personal_data',
                    'old_value'=>$user->first_name,
                    'new_value' => $user->first_name,
                    'filed_name' =>"first_name",
                    'participant_id' => $participant->id,
                    "actor_user" =>request()->user()->id
                ]);
            }
            //last_name check
            if ($user->isDirty(['last_name'])) {
                ParticipantHistory::create([
                    'action' => 'change_personal_data',
                    'old_value'=>$user->last_name,
                    'new_value' => $user->last_name,
                    'filed_name' =>"last_name",
                    'participant_id' => $participant->id,
                    "actor_user" =>request()->user()->id
                ]);
            }
            //Patronymic check
            if ($user->isDirty(['patronymic'])) {
                ParticipantHistory::create([
                    'action' => 'change_personal_data',
                    'old_value'=>$user->patronymic ,
                    'new_value' => $user->patronymic ,
                    'filed_name' =>"patronymic",
                    'participant_id' => $participant->id,
                    "actor_user" =>request()->user()->id
                ]);
            }
            //Gender check
            if ($user->isDirty(['gender'])) {
                ParticipantHistory::create([
                    'action' => 'change_personal_data',
                    'old_value'=>$user->gender ,
                    'new_value' => $user->gender ,
                    'filed_name' =>"gender",
                    'participant_id' => $participant->id,
                    "actor_user" =>request()->user()->id
                ]);
            }
            //phone check
            if ($user->isDirty(['phone'])) {
                ParticipantHistory::create([
                    'action' => 'change_personal_data',
                    'old_value'=>$user->phone ,
                    'new_value' => $user->phone ,
                    'filed_name' =>"phone",
                    'participant_id' => $participant->id,
                    "actor_user" =>request()->user()->id
                ]);
            }
            //email check
            if ($user->isDirty(['email'])) {
                ParticipantHistory::create([
                    'action' => 'change_personal_data',
                    'old_value'=>$user->email ,
                    'new_value' => $user->email ,
                    'filed_name' =>"email",
                    'participant_id' => $participant->id,
                    "actor_user" =>request()->user()->id
                ]);
            } 
        }
    }

    public function created(User $user) {
        
    }


    public function deleting(User $user)
    {


    }
}
