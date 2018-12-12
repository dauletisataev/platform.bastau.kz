<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LessonTemplateItem extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'title',
        'manual',
        'lesson_template_translation_id',
        'lesson_template_id',
        'order',
        'access_tasks',
        'access_homework',
        'access_tests',
        'access_offset',
        'access_offset_value',
        'access_offset_type'
    ];

    public function toArray()
    {
        $array = parent::toArray();
        $array['date'] = '';
        $array['time'] = '';
        $array['active'] = true;
        $array['group_id'] = '';
        $array['office_cabinet_id'] = '';
        $array['is_full'] = $this->is_full;
        return $array;
    }

    public static function boot() {
        parent::boot();
        static::addGlobalScope('order', function (\Illuminate\Database\Eloquent\Builder $builder) {
            $builder->orderBy('order','asc');
        });
    }

    public function materials() {
        return $this->belongsToMany('App\Material');
    }

    public function homework() {
        return $this->hasMany('App\TaskGroup');
    }

    public function pages() {
        return $this->hasMany('App\MaterialPage');
    }

    public function lesson_template() {
        return $this->belongsTo(LessonTemplate::class);
    }
}
