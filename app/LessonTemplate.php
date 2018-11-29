<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Intervention\Image\Facades\Image;

class LessonTemplate extends Model
{
    public $timestamps = false;

    public $fillable = [
        'name',
        'program_id',
        'level_id',
        'image',
        'cost',
        'type',
        'role_id'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items()
    {
        return $this->hasMany(LessonTemplateItem::class);
    }

    // LMS
    public function users() {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    public function role() {
        return $this->belongsTo(Role::class);
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

    public function scopeFilter($query, $filters)
    {

    }

    public function translation(){
        return $this->belongsToMany('App\LessonTemplate', 'lesson_template_translation', 'lesson_template_ru_id', 'lesson_template_kz_id');
    }
}
