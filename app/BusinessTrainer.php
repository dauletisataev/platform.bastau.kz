<?php

namespace App;

use App\Observers\BTrainerObserver;
use Illuminate\Database\Eloquent\Model;

class BusinessTrainer extends Model
{
    protected $table = 'trainers';
    protected $dates = ['deleted_at'];
    /**
     * Foreign Key Relationship with User model
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Foreign key to Groups
     */
    public function groups()
    {
        return $this->hasMany(Group::class, 'trainer_id');
    }

    /**
     * Foreign Key Relationship with BTrainerHistory model
     */
    public function histories()
    {
        return $this->hasMany(BTrainerHistory::class);
    }

    /**
     * Filter from frontend
     */
    public function scopeFilter($query, $filters)
    {

        if (isset($filters['search_text'])) {
            $search_text = $filters['search_text'];
            $query->whereHas('user', function ($q) use ($search_text) {
                $q->where(function ($query) use ($search_text) {
                    $query->where('name', 'like', '%' . $search_text . '%');
                    $query->orWhere('email', 'like', '%' . $search_text . '%');
                    $query->orWhere('phone', 'like', '%' . $search_text . '%');
                });
            });
        }
    }
    public static function boot()
    {
        parent::boot();
        BusinessTrainer::observe(BTrainerObserver::class);
    }
    public function locality(){
        return $this->belongsTo("App\Locality");
    }

    /*
	* public function groups()
    * {
    *	return $this->hasMany('App\Group');
    * }	
    */
}
