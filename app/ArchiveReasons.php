<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ArchiveReasons extends Model
{
    protected $table = 'archive_reasons';
    public $fillable =[
        'reason',
        'locale_id'
    ];
    public function getTranslation($language){
        return App\LeadAdditionals::where(["id"=>$this["id"], "locale"=>$language]);
    }
    public $timestamps = false;
}
