<?php

namespace App;

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
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Foreign Key Relationship with BTrainerHistory model
     */
    public function histories()
    {
        return $this->hasMany(BTrainerHistory::class);
    }

    /**
     * Move to History
     */
    public function archive()
    {

    }

    /**
     * Completely delete from trainers and history
     */
    public function delete()
    {

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


    /*
	* public function groups()
    * {
    *	return $this->hasMany('App\Group');
    * }	
    */
}
