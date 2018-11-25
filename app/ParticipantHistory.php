<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class ParticipantHistory extends Model
{
    protected $table = 'participant_histories';
    public $fillable=[
        'action',
        'old_value',
        'new_value',
        'actor_user',
        'participant_id',
        'field_name',
        'archive_reason_id'
    ];
    public function loadArchiveReason(){
        return $this->belongsTo('App\ArchiveReasons');
    }
}