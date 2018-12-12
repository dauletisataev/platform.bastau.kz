<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
/*done by Yersultan
    For managing projects in future, for now, will be used only for one project
 */
class ProjectSettings extends Model
{
    public $fillable=['days_number','hours_per_day','project_id'];
    public $timestamps=false;

}
