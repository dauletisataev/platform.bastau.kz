<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
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
        //This part is required for getting groups of particular participant by user_id
        if(isset($filters['user_id'])){
            $user_id=$filters['user_id'];
            $query->whereHas("participants.user",function ($q) use ($user_id){
                $q->where('id',$user_id);
            });
        }
        if(isset($filters['search_text'])){
            $search_text=$filters['search_text'];
            $query->whereHas('locality', function($q) use($search_text) {
                $q->where(function($query) use($search_text) {
                    $query->where('name', 'like', '%'.$search_text.'%');
                    $query->orWhereHas('district',function($q) use($search_text){
                        $q->where('name', 'like', '%'.$search_text.'%');
                        $q->orWhereHas('region',function($q) use($search_text){
                            $q->where('name', 'like', '%'.$search_text.'%');
                        });
                    });
                });
            });
        }
        if(isset($filters['group_type'])){
            if($filters['group_type']==="current"){
                $query->where('in_archive',0);
            }else if($filters['group_type']==="without_participants"){
                $query->whereDoesntHave('participants');
            }else if($filters['group_type']==="inactive"){
                $query->where('in_archive',1);
            }else{

            }
        }

        return $query;
    }
    public function histories(){
        return $this->hasMany("App\GroupHistory");
    }
    /**
     * BusinessTrainer relation
     */
    public function trainer()
    {
        return $this->belongsTo(BusinessTrainer::class, 'trainer_id');
    }

    public function lessons(){
        return $this->hasMany('App\Lesson');
    }

    public function locality(){
        return $this->belongsTo("App\Locality");
    }
}
