<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BusinessTrainer extends Model
{	
    use SoftDeletes;

	protected $table = 'trainers';
    protected $dates = ['deleted_at'];
    /**
    * Foreign Key Relationship with User model
    */
    public function user()
    {
    	return $this->belongsTo('App\User');
    }

    /**
    * Foreign Key Relationship with History model
    */
    public function history()
    {
        return $this->belongsTo('App\BTrainerHistory');
    }

    /**
    * Foreign Key Relationship with Log model
    */
    public function log() {
        return $this->belongsTo('App\BTrainerLog');
    }

    /**
    * Move to History
    */
    public function archive()
    {
        $this->delete();
        return response()->json([
            "status" => "204",
            "message" => "Deleted successfully"
        ]);
    }

    public function get_archive_date()
    {
        return $this->deleted_at;
    }

    /**
    * Full delete from BusinessTrainer and History tables.
    */

    public function full_delete()
    {
        $this->forceDelete();
        $this->history()->forceDelete();
    }

    public function restrore_self()
    {
        $this->restore();
    }
    /*
	* public function groups()
    * {
    *	return $this->hasMany('App\Group');
    * }	
    */
}
