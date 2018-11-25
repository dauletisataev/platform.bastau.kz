<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
/*Ерсултан
 *Модель для хранения дополнительной информации о заявке
 */
class ParticipantAdditionals extends Model
{
    //
    protected $table = 'participant_additionals';
    public $fillable =[
    'participant_id',
    'company_name',
    'company_owner',
    'company_ownre_iin',
    'company_address',
    'company_post_index',
    'learning_language',
    'project_id',
    'locale'
];
    public function paricipant()
    {
        return $this->belongsTo('App\Participant','id');
    }
    public function getTranslation($language){
        return App\LeadAdditionals::where(["id"=>$this["id"], "locale"=>$language]);
    }
}
