<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\BTrainerHistory
 *
 * @property int $id
 * @property int $student_id
 * @property string $value
 * @property string $type
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\BTrainerHistory whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\BTrainerHistory whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\BTrainerHistory whereStudentId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\BTrainerHistory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\BTrainerHistory whereValue($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Query\Builder|\App\BTrainerHistory whereType($value)
 */

class BTrainerHistory extends Model
{
    protected $fillable = ['trainer_id', 'value', 'type', 'description'];
}
