<?php

namespace App\Http\Controllers;

use App\Lesson;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    public function item($id) {
        $lesson = Lesson::where('id','=',$id)->first();
        return $lesson;
    }

    public function delete($id)
    {
        $lesson = Lesson::find($id);
        Lesson::destroy($id);
    }
}
