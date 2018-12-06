<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class UserLog extends Model
{
    protected $fillable = ['user_id', 'name', 'x_ids', 'y_ids', 'value'];


    public function toArray()
    {
        $array = parent::toArray();
        $array['date'] = $this->date;
        return $array;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }


    public function getDateAttribute($value)
    {
        return $this->created_at->format('d.m.y H:i');
    }

    public static function insert($name, $x, $y = '', $value = '') {

        if(\Auth::user()){
            UserLog::create([
                'name' => $name,
                'user_id' => \Auth::user()->id,
                'x_ids' => $x,
                'y_ids' => $y,
                'value' => $value
            ]);
        }

    }


    public function getXIdsAttribute($value)
    {
        $return = [];
        switch ($this->name){
            case 'user_create':
                $user = User::find($value);
                $return = $user ? ['id' => $user->id, 'name' => $user->first_name] : ['id' => 0, 'name' => '[пользователь удален]'];
                break;
        }
        return $return ? $return : $value;
    }

    public function getYIdsAttribute($value)
    {
        $return = [];
        return $return ? $return : $value;
    }


    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param $filters
     * @return \Illuminate\Database\Eloquent\Builder mixed
     */
    public function scopeFilter($query, $filters)
    {
        if(isset($filters['dateRange'])){

            $dateRange = explode(' — ', $filters['dateRange']);

            if(count($dateRange) == 2){

                $query->where(function($query) use($dateRange){
                    $query->where('created_at', '>=', Carbon::createFromFormat('d.m.y', $dateRange[0])->setTime(0, 0));
                    $query->where('created_at', '<=', Carbon::createFromFormat('d.m.y', $dateRange[1])->setTime(24, 0));

                });
            }

        }
        return $query;
    }

}
