<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\ParticipantHistory;
use Illuminate\Support\Facades\Log;
/*Ерсултан
 *Модель для хранения основной информации о заявке
 */
class Participant extends Model
{
    //
    protected $table = 'participants';
    public $fillable = [
        'first_name',
        'last_name',
        'patronymic',
        'gender',
        'disability',
        'iin',
        'status',
        'phone',
        'email',
        'post_index',
        'region_id',            //TODO change it to relations,
        'address',
        'identity card',
        'address certificate',
        'deleted_at'
        ];

    public static function rules($id = 0)
    {
        return [
            //Личные данные пользователя

            'disability'=>'required',
            'iin' =>'required',
            'identity_card'=>'nullable|image64:jpeg,jpg,png', //TODO must be required
            'address_certificate'=>'nullable|image64:jpeg,jpg,png', //TODO must be required
            'phone' => 'required',
            'email' => 'nullable|email',
        ];
    }

    public function user(){
        return $this->belongsTo('App\User');
    }
    public function groups(){
        return $this->belongsToMany('App\Group',"group_participant");
    }

    public function archive_reason(){
        return $this->belongsTo('App\ArchiveReasons');
    }
    public function histories(){
        return $this->hasMany("App\ParticipantHistory");
    }

    public function participantAdditionals()
    {
        return $this->hasMany('App\ParticipantAdditionals');
    }

    public function setIdentityCard($value)
    {
        $validator = \Validator::make(['image' => $value], [
            'image' => 'base64image'
        ]);

        if (!$validator->fails()) {

            $imageData = $value;
            $fileName = Carbon::now()->timestamp . '_' . uniqid() . '.' . explode('/', explode(':',
                    substr($imageData, 0, strpos($imageData, ';')))[1])[1];

            Image::make($value)->fit(200)->save(public_path('images/indentity_cards/') . $fileName);

            $this->attributes['identity_card'] = '/images/indentity_cards/' . $fileName;

        }

    }

    public function setAddressCertificate($value)
    {
        $validator = \Validator::make(['image' => $value], [
            'image' => 'base64image'
        ]);

        if (!$validator->fails()) {

            $imageData = $value;
            $fileName = Carbon::now()->timestamp . '_' . uniqid() . '.' . explode('/', explode(':',
                    substr($imageData, 0, strpos($imageData, ';')))[1])[1];

            Image::make($value)->fit(200)->save(public_path('images/address_certificates/') . $fileName);

            $this->attributes['address_certificate'] = '/images/address_certificates/' . $fileName;

        }

    }

    public function scopeFilter($query, $filters)
    {
        if(isset($filters['search_text'])){
            $search_text = $filters['search_text'];
            $query->whereHas('user', function($q) use($search_text) {
                $q->where(function($query) use($search_text) {
                    $query->where('first_name', 'like', '%'.$search_text.'%');
                    $query->orWhere('last_name', 'like', '%'.$search_text.'%');
                    $query->orWhere('patronymic', 'like', '%'.$search_text.'%');
                    $query->orWhere('iin', 'like', '%'.$search_text.'%');
                    $query->orWhereHas('home', function($q) use($search_text) {
                        $q->where (function($query) use($search_text) {
                            $query->where('name', 'like', '%'.$search_text.'%');
                            $query->orWhereHas('district',function($q) use($search_text){
                                $q->where('name', 'like', '%'.$search_text.'%');
                                $q->orWhereHas('region',function($q) use($search_text){
                                    $q->where('name', 'like', '%'.$search_text.'%');
                                });
                            });
                        });
                    });
                });
            });
        }
        /*
                if(isset($filters['locality'])){
                    $query->where('locality_id',$filters['locality']);
                }

                else if(isset($filters['district'])){
                    $query->whereHas('locality', function($q) use($filters) {
                        $q->where (function($query) use($filters) {
                            $query->where('id', $filters['locality']);
                            $query->orWhereHas('district',function($q) use($filters){
                                $q->where('name', $filters['district']);
                            });
                        });
                    });
                }*/

        if (isset($filters['is_archived'])){
            if($filters['is_archived']==="false"){
                $query->where("in_archive",0);
            }else{
                $query->where("in_archive",1);
            }

        }
        return $query;
    }
}
