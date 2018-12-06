<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Locality extends Model
{
    protected $table = 'localities';

    public $fillable =[
        'name',
        'district_id'
    ];
    public $timestamps = false;
    public function district(){
        return $this->belongsTo('App\District');
    }
    public function scopeFilter($query, $filters)
    {
        if(isset($filters['search_text'])){
            $search_text = $filters['search_text'];
            $query->whereHas('district',function ($q)use ($search_text){
                $q->where('name','like','%'.$search_text.'%');
                $q->orWhereHas('region',function ($q) use($search_text){
                    $q->where('name','like','%'.$search_text.'%');
                });
            });
            $query->orWhere('name','like','%'.$search_text.'$');
        }
        return $query;
    }
}
