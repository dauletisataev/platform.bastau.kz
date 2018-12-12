<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image;

class LessonTemplate extends Model
{
    use \Dimsav\Translatable\Translatable;
    public $timestamps = false;

    public $translatedAttributes = ['name'];
    public $fillable = ['project_id','image','type','has_test','test_duration','datetime'];

    public function items($locale)
    {
        $lesson_template_translation_id=LessonTemplateTranslation::where("lesson_template_id",$this->attributes["id"])->
            where("locale",$locale)->first();
        if($lesson_template_translation_id!==NULL)$lesson_template_translation_id=$lesson_template_translation_id->id;
        $this->items = \App\LessonTemplateItem::where('lesson_template_translation_id',$lesson_template_translation_id)->get();
        return $this;
    }

    public function scopeFilter($query, $filters)
    {
        if($filters["project_id"]!==NULL){
            $query->where("project_id",$filters["project_id"]);
        }
        if($filters["type"]!==NULL){
            $query->where("type",$filters["type"]);
        }

        return $query;
    }

    public function setImageAttribute($value)
    {
        $validator = \Validator::make(['image' => $value], [
            'image' => 'base64image'
        ]);

        if (!$validator->fails()) {
            $imageData = $value;
            $fileName = Carbon::now()->timestamp . '_' . uniqid() . '.' . explode('/', explode(':', substr($imageData, 0, strpos($imageData, ';')))[1])[1];
            Image::make($value)->fit(200)->save(public_path('images/') . $fileName);
            $this->attributes['image'] = '/images/' . $fileName;
        }
    }

}
