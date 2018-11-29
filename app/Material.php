<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    public $timestamps = false;

    public $fillable = [
        'material_type_id',
        'content',
        'additional_content',
        'material_order',
        'material_page_id'
    ];

    public function materialType() {
        return $this->belongsTo('App\MaterialType');
    }

    public function lessons() {
        return $this->belongsToMany('App\Lesson');
    }

    public function templates() {
        return $this->belongsToMany('App\LessonTemplateItem');
    }

    public function page() {
        return $this->belongsTo('App\MaterialPage','material_page_id');
    }

    public function taskGroup() {
        return $this->hasOne('App\TaskGroup');
    }

}
