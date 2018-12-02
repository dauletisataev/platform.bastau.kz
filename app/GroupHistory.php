<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GroupHistory extends Model
{
    protected $table = 'group_histories';
    public $fillable=[
        'action',
        'old_value',
        'new_value',
        'actor_user',
        'group_id',
        'field_name',
        'archive_reason_id'
    ];
    public function loadArchiveReason(){
        return $this->belongsTo('App\ArchiveReasons');
    }

}
