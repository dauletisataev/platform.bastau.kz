<?php

namespace App\Observers;

use App\Participant;
use App\ParticipantHistory;
use Illuminate\Support\Facades\Log;
class ParticipantObserver
{
    public function updating(Participant $participant)
    {
        $oldParticipant = Participant::find($participant->id);
        if ($oldParticipant && !$oldParticipant->in_archive && $participant->in_archive) {
            ParticipantHistory::create([
                'action' => 'archived',
                'new_value' => $participant->archive_reason_id,
                'filed_name' =>"archive_reason_id",
                'participant_id' => $participant->id,
                "actor_user" =>request()->user()->id
            ]);
        }
        if ($oldParticipant && $oldParticipant->in_archive && !$participant->in_archive) {
            ParticipantHistory::create([
                'action' => 'unarchived',
                'new_value' => $participant->archive_reason_id,
                'filed_name' =>"archive_reason_id",
                'participant_id' => $participant->id,
                "actor_user" =>request()->user()->id
            ]);
        }
        if ($oldParticipant) {
            if ($participant->isDirty(['disability'])) {
                ParticipantHistory::create([
                    'action' => 'change_personal_data',
                    'old_value'=>$oldParticipant->disability,
                    'new_value' => $participant->disability,
                    'filed_name' =>"disability",
                    'participant_id' => $participant->id,
                    "actor_user" =>request()->user()->id
                ]);
            }
        }
    }
    public function created(Participant $participant)
    {
        ParticipantHistory::create([
            'action' => 'created',
            'new_value' => $participant->id,
            'filed_name' =>"id",
            'participant_id' => $participant->id,
            "actor_user" =>request()->user()->id
        ]);
    }
}