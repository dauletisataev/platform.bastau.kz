<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $table = 'groups';
    public $fillable=[
        'online',
        'next_lesson',
        'in_archive',
        'capacity',
    ];
    public function participants(){
        return $this->belongsToMany('App\Participant');
    }
    public function scopeFilter($query, $filters)
    {
        if($filters['group_type']==="current"){
            $query->where('in_archive',0);
        }else if($filters['group_type']==="without_participants"){
           $query->whereDoesntHave('participants');
        }else if($filters['group_type']==="inactive"){
            $query->where('in_archive',1);
        }else{

        }
        return $query;
    }
    public function histories(){
        return $this->hasMany("App\GroupHistory");
    }
}
