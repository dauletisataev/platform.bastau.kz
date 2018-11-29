<?php
/**
 * Created by PhpStorm.
 * User: Yurituski
 * Date: 23.04.2018
 * Time: 14:39
 */

namespace App;


use App\Scopes\OrderScope;
use Illuminate\Database\Eloquent\Model;

class MaterialPage extends Model
{
    public $fillable = [
        'lesson_template_item_id',
        'lesson_id',
        'name',
        'sort'
    ];

    public $timestamps = false;

    public static function boot()
    {
        parent::boot();
        static::addGlobalScope(new OrderScope('sort'));
    }

    public function lesson() {
        return $this->belongsTo('App\Lesson');
    }

    public function lesson_template_item() {
        return $this->belongsTo('App\LessonTemplateItem');
    }

    public function materials() {
        return $this->hasMany('App\Material');
    }
}