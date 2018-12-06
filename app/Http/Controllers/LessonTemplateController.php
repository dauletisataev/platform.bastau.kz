<?php

namespace App\Http\Controllers;

use App\LessonQuestion;
use App\LessonQuestionAnswer;
use App\LessonTemplate;
use App\LessonTemplateItem;
use App\Material;
use App\MaterialPage;
use App\Task;
use App\TaskGroup;
use App\TaskQuestion;
use App\UserLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class LessonTemplateController extends Controller
{

    public function items(request $request)
    {
        $templates = LessonTemplate::with([
            'items',
            'role'
        ]);
        return $templates->filter(Input::all())->get();
    }

    public function item($id) {
        $item = LessonTemplate::where('id','=',$id)
            ->with('items','translation','testQuestions.answers')->first();
        return $item;
    }

    public function getItemConnections($id) {
        if ($id == 1) {
            return LessonTemplate::where('language','2')->first();
        } else if ($id == 2) {
            return LessonTemplate::where('language', '1')->first();
        }
        return null;
    }

    public function save(request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'role_id' => 'required'
        ]);

        $template = LessonTemplate::findOrNew($request->get('id'));

        $template->name = $request->get('name');
        $template->role_id = $request->get('role_id');
        $template->image = $request->get('image');
        $template->has_test = $request->get('has_test');
        $template->test_duration = $request->get('test_duration');
        $template->language = $request->get('language');
        if ($request->get('language')==1) {
            $temp = LessonTemplate::find($request->get('connected_template_id'));
            if ( $temp != null) {
                $template->translation()->sync($temp);
                $temp->translation()->sync($template);
            }
        } else if ($request->get('language')==2) {
            $temp = LessonTemplate::find($request->get('connected_template_id'));
            if ( $temp != null) {
                $template->translation()->sync($temp);
                $temp->translation()->sync($template);
            }
        }
        $template->save();
        if(!$request->get('id')) {
            UserLog::insert('lesson_template_create', $template->id, '');
            return $template->id;
        }
    }

    public function  deleteTestQuestion($id) {
        $lessonquestion = LessonQuestion::find($id);
        $lessonquestion::destroy($id);
    }

    public function addTestQuestion($id, request $request) {
        $title = $request->getContent();
        $lesson_question = LessonQuestion::create();
        $lesson_question->lesson_template_id = $id;
        $lesson_question->value = $title;
        $lesson_question->save();
        return $lesson_question->id;
    }

    public function saveTestQuestion(request $request) {
        $test_questions = array();
        $test_questions = $request->all();
        for ($i=0; $i<sizeof($test_questions); $i++){
            $test_question = LessonQuestion::findOrNew($test_questions[$i]['id']);
            $test_question->lesson_template_id = $test_questions[$i]['lesson_template_id'];
            $test_question->value = $test_questions[$i]['value'];
            $test_question->save();
            $test_question_answers = $test_questions[$i]['answers'];
            for ($j=0; $j<sizeof($test_question_answers); $j++) {
                //var_dump($test_question_answers[$j]);
                $test_question_answer = LessonQuestionAnswer::findOrNew($test_question_answers[$j]['id']);
                //var_dump($test_question_answer);
                $test_question_answer->lesson_question_id=$test_question_answers[$j]['lesson_question_id'];
                $test_question_answer->option=$test_question_answers[$j]['option'];
                $test_question_answer->value=$test_question_answers[$j]['value'];
                $test_question_answer->is_correct=$test_question_answers[$j]['is_correct'];
                var_dump($test_question_answer);
                echo 'end';
                $test_question_answer->save();
            }
        }
    }

    public function saveItem($id, request $request) {
        $title = $request->getContent();
        $last = LessonTemplateItem::where('lesson_template_id',$id)
            ->orderBy('order','desc')->first();
        $order = $last ? $last->order + 1 : 0;
        $item = new LessonTemplateItem(['title' => $title, 'lesson_template_id' => $id, 'order' => $order]);
        $item->save();
        return $item->id;
    }

    public function delete($id)
    {
        $lesson_template = LessonTemplate::find($id);
        $lesson_template::destroy($id);
    }

    public function deleteItem($id) {
        $lesson_template_item = LessonTemplateItem::find($id);
        $lesson_template_item::destroy($id);
    }

    public function saveContent(request $request)
    {
        $messages = array(
            'task_question' => 'Неправильно составлен вопрос.',
        );
        $validator = \Validator::make($request->all(), [
            'pages.*.materials.*.content' => 'sometimes|required_unless:pages.*.materials.*.material_type_id,5',
            'pages.*.materials.*.task_group.tasks.*.questions.*.content' => 'sometimes|required|task_question:pages.*.materials.*.task_group.tasks.*.task_type_id',
            'pages.*.materials.*.task_group.tasks.*.description' => 'sometimes|required',
            'pages.*.materials.*.task_group.tasks.*.words' => 'sometimes|required_if:pages.*.materials.*.task_group.tasks.*.task_type_id,6',
            'tests.*.name' => 'sometimes|required',
            'tests.*.task_groups.*.tasks.*.questions.*.content' => 'sometimes|required|task_question:tests.*.task_groups.*.tasks.*.task_type_id',
            'tests.*.task_groups.*.tasks.*.description' => 'sometimes|required',
            'tests.*.task_groups.*.tasks.*.words' => 'sometimes|required_if:tests.*.task_groups.*.tasks.*.task_type_id,6',
            'homework.*.tasks.*.questions.*.content' => 'sometimes|required|task_question:homework.*.tasks.*.task_type_id',
            'homework.*.tasks.*.description' => 'sometimes|required',
            'homework.*.tasks.*.words' => 'sometimes|required_if:homework.*.tasks.*.task_type_id,6',
        ],$messages);
        if ($validator->fails()) return response()->json($validator->errors(), 422);
        $lesson = LessonTemplateItem::find($request->get('id'));
        $lesson_id = $lesson->id;
        $lesson->manual = $request['manual'];
        $pages = $request['pages'];
        $old_page_ids = MaterialPage::where('lesson_template_item_id',$lesson_id)->pluck('id')->toArray();
        $edited_page_ids = [];
        $homework = $request['homework'];
        $tests = $request['tests'];
        if($pages) {
            foreach($pages as $page) {
                if($page['id']) {
                    $old_page = MaterialPage::find($page['id']);
                    $old_page->name = $page['name'];
                    $old_page->sort = $page['sort'];
                    $old_page->save();
                    array_push($edited_page_ids,$old_page->id);
                    $old_material_ids = Material::where('material_page_id',$old_page->id)->pluck('id')->toArray();
                    $edited_material_ids = [];
                    foreach ($page['materials'] as $material) {
                        if($material['id']) {
                            $old_material = Material::find($material['id']);
                            $old_material->material_order = $material['material_order'];
                            $old_material->content = $material['content'];
                            $old_material->additional_content = $material['additional_content'] ? $material['additional_content'] : '';
                            $old_material->save();
                            array_push($edited_material_ids,$old_material->id);
                            if($old_material->material_type_id == 5) {
                                $task_group = TaskGroup::where('material_id','=',$material['id'])->first();
                                $old_task_ids = Task::where('task_group_id','=',$task_group->id)->pluck('id')->toArray();
                                $edited_task_ids = [];
                                foreach ($material['task_group']['tasks'] as $task) {
                                    if($task['id']) {
                                        $old_task = Task::find($task['id']);
                                        $old_task->audio = $task['audio'];
                                        if($task['description']) $old_task->description = $task['description'];
                                        $old_task->image = $task['image'];
                                        if($task['rating'] !== null) $old_task->rating = $task['rating'];
                                        if($task['words'] !== null) $old_task->words = $task['words'];
                                        $old_task->save();
                                        array_push($edited_task_ids,$old_task->id);
                                        $old_question_ids = TaskQuestion::where('task_id','=',$old_task->id)->pluck('id')->toArray();
                                        $edited_question_ids = [];
                                        if($task['questions'] && count($task['questions'])>0) {
                                            foreach ($task['questions'] as $question) {
                                                if($question['id']) {
                                                    $old_question = TaskQuestion::find($question['id']);
                                                    $old_question->content = $question['content'];
                                                    $old_question->is_correct = $question['is_correct'];
                                                    $old_question->save();
                                                    array_push($edited_question_ids,$old_question->id);
                                                } else {
                                                    $new_question = new TaskQuestion();
                                                    $new_question->task_id = $old_task->id;
                                                    $new_question->content = $question['content'];
                                                    $new_question->is_correct = $question['is_correct'];
                                                    $new_question->save();
                                                }
                                            }
                                            $diff_questions = array_diff($old_question_ids,$edited_question_ids);
                                            if($diff_questions) {
                                                foreach($diff_questions as $id) {
                                                    $task = TaskQuestion::find($id);
                                                    $task->delete();
                                                }
                                            }
                                        }
                                    } else {
                                        $new_task = new Task();
                                        $new_task->task_group_id = $task_group->id;
                                        $new_task->task_type_id = $task['task_type_id'];
                                        $new_task->audio = $task['audio'];
                                        $new_task->description = $task['description'];
                                        $new_task->image = $task['image'];
                                        $new_task->rating = $task['rating'];
                                        $new_task->words = $task['words'];
                                        $new_task->save();
                                        if($task['questions'] && count($task['questions'])>0) {
                                            foreach ($task['questions'] as $question) {
                                                $new_question = new TaskQuestion();
                                                $new_question->task_id = $new_task->id;
                                                $new_question->content = $question['content'];
                                                $new_question->is_correct = $question['is_correct'];
                                                $new_question->save();
                                            }
                                        }
                                    }
                                }
                                $diff_tasks = array_diff($old_task_ids,$edited_task_ids);
                                if($diff_tasks) {
                                    foreach($diff_tasks as $id) {
                                        $task = Task::find($id);
                                        $questions = TaskQuestion::where('task_id',$id)->get();
                                        foreach($questions as $question) {
                                            $question->delete();
                                        }
                                        $task->delete();
                                    }
                                }
                            }


                        } else {
                            $new_material = new Material();
                            if(array_key_exists('content',$material)) $new_material->content = $material['content'];
                            if(array_key_exists('additional_content',$material)) $new_material->additional_content = $material['additional_content'];
                            $new_material->material_type_id = $material['material_type_id'];
                            $new_material->material_order = $material['material_order'];
                            $new_material->material_page_id = $old_page->id;
                            $new_material->save();
                            if($material['material_type_id'] == 5) {
                                $new_task_group = new TaskGroup();
                                $new_task_group->lesson_template_item_id = $lesson_id;
                                $new_task_group->material_id = $new_material->id;
                                $new_task_group->is_homework = 0;
                                $new_task_group->is_test = 0;
                                $new_task_group->save();
                                if($material['task_group']['tasks'] && count($material['task_group']['tasks'])>0) {
                                    foreach ($material['task_group']['tasks'] as $task) {
                                        $new_task = new Task();
                                        $new_task->task_group_id = $new_task_group->id;
                                        $new_task->task_type_id = $task['task_type_id'];
                                        $new_task->audio = $task['audio'];
                                        $new_task->description = $task['description'];
                                        $new_task->image = $task['image'];
                                        $new_task->rating = $task['rating'];
                                        $new_task->words = $task['words'];
                                        $new_task->save();
                                        if($task['questions'] && count($task['questions'])>0) {
                                            foreach ($task['questions'] as $question) {
                                                $new_question = new TaskQuestion();
                                                $new_question->task_id = $new_task->id;
                                                $new_question->content = $question['content'];
                                                $new_question->is_correct = $question['is_correct'];
                                                $new_question->save();
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    $diff = array_diff($old_material_ids,$edited_material_ids);
                    if($diff) {
                        foreach($diff as $id) {
                            $material = Material::find($id);
                            $task_group = TaskGroup::where('material_id',$id)->first();
                            if($task_group) {
                                $tasks = Task::where('task_group_id',$task_group->id)->get();
                                if($tasks) {
                                    foreach ($tasks as $task) {
                                        $questions = TaskQuestion::where('task_id',$task->id)->get();
                                        foreach ($questions as $question) {
                                            $question->delete();
                                        }
                                        $task->delete();
                                    }
                                }
                                $task_group->delete();
                            }
                            $material->delete();
                        }
                    }
                } else {
                    $new_page = new MaterialPage();
                    $new_page->lesson_template_item_id = $lesson_id;
                    $new_page->name = $page['name'];
                    $new_page->sort = $page['sort'];
                    $new_page->save();
                    foreach($page['materials'] as $material) {
                        $new_material = new Material();
                        if(array_key_exists('content',$material)) $new_material->content = $material['content'];
                        if(array_key_exists('additional_content',$material)) $new_material->additional_content = $material['additional_content'];
                        $new_material->material_type_id = $material['material_type_id'];
                        $new_material->material_order = $material['material_order'];
                        $new_material->material_page_id = $new_page->id;
                        $new_material->save();
                        if($material['material_type_id'] == 5) {
                            $new_task_group = new TaskGroup();
                            $new_task_group->lesson_template_item_id = $lesson_id;
                            $new_task_group->material_id = $new_material->id;
                            $new_task_group->is_homework = 0;
                            $new_task_group->is_test = 0;
                            $new_task_group->save();
                            if($material['task_group']['tasks'] && count($material['task_group']['tasks'])>0) {
                                foreach ($material['task_group']['tasks'] as $task) {
                                    $new_task = new Task();
                                    $new_task->task_group_id = $new_task_group->id;
                                    $new_task->task_type_id = $task['task_type_id'];
                                    $new_task->audio = $task['audio'];
                                    $new_task->description = $task['description'];
                                    $new_task->image = $task['image'];
                                    $new_task->rating = $task['rating'];
                                    $new_task->words = $task['words'];
                                    $new_task->save();
                                    if($task['questions'] && count($task['questions'])>0) {
                                        foreach ($task['questions'] as $question) {
                                            $new_question = new TaskQuestion();
                                            $new_question->task_id = $new_task->id;
                                            $new_question->content = $question['content'];
                                            $new_question->is_correct = $question['is_correct'];
                                            $new_question->save();
                                        }
                                    }
                                }
                            }
                        }
                    }

                }
            }
            $diff = array_diff($old_page_ids,$edited_page_ids);
            if($diff) {
                foreach($diff as $id) {
                    $page = MaterialPage::find($id);
                    $page->delete();
                }
            }
        }
        $edited_task_group_ids = [];
        $edited_task_ids = [];
        $edited_question_ids = [];

        if($homework) {
            $old_task_group_ids = TaskGroup::where('lesson_template_item_id',$lesson_id)->where('is_homework',1)->pluck('id')->toArray();
            foreach($homework as $task_group) {
                if($task_group['id']) {
                    $old_task_group = TaskGroup::find($task_group['id']);
                    $old_task_group->section_id = $task_group['section_id'];
                    $old_task_group->save();
                    array_push($edited_task_group_ids,$old_task_group->id);
                    if($task_group['tasks']) {
                        $old_task_ids = Task::where('task_group_id','=',$old_task_group->id)->pluck('id')->toArray();
                        foreach ($task_group['tasks'] as $task) {
                            if($task['id']) {
                                $old_task = Task::find($task['id']);
                                $old_task->audio = $task['audio'];
                                $old_task->description = $task['description'];
                                $old_task->image = $task['image'];
                                $old_task->rating = $task['rating'];
                                $old_task->words = $task['words'];
                                $old_task->save();
                                array_push($edited_task_ids,$old_task->id);
                                $old_question_ids = TaskQuestion::where('task_id','=',$old_task->id)->pluck('id')->toArray();
                                if($task['questions'] && count($task['questions'])>0) {
                                    foreach ($task['questions'] as $question) {
                                        if($question['id']) {
                                            $old_question = TaskQuestion::find($question['id']);
                                            $old_question->content = $question['content'];
                                            $old_question->is_correct = $question['is_correct'];
                                            $old_question->save();
                                            array_push($edited_question_ids,$old_question->id);
                                        } else {
                                            $new_question = new TaskQuestion();
                                            $new_question->task_id = $old_task->id;
                                            $new_question->content = $question['content'];
                                            $new_question->is_correct = $question['is_correct'];
                                            $new_question->save();
                                        }
                                    }
                                    $diff_questions = array_diff($old_question_ids,$edited_question_ids);
                                    if($diff_questions) {
                                        foreach($diff_questions as $id) {
                                            $task = TaskQuestion::find($id);
                                            $task->delete();
                                        }
                                    }
                                }
                            } else {
                                $new_task = new Task();
                                $new_task->task_group_id = $old_task_group->id;
                                $new_task->task_type_id = $task['task_type_id'];
                                $new_task->audio = $task['audio'];
                                $new_task->description = $task['description'];
                                $new_task->image = $task['image'];
                                $new_task->rating = $task['rating'];
                                $new_task->words = $task['words'];
                                $new_task->save();
                                if($task['questions'] && count($task['questions'])>0) {
                                    foreach ($task['questions'] as $question) {
                                        $new_question = new TaskQuestion();
                                        $new_question->task_id = $new_task->id;
                                        $new_question->content = $question['content'];
                                        $new_question->is_correct = $question['is_correct'];
                                        $new_question->save();
                                    }
                                }
                            }
                        }
                        if(isset($old_task_ids) && count($old_task_ids)>0) {
                            $diff_tasks = array_diff($old_task_ids,$edited_task_ids);
                            if($diff_tasks) {
                                foreach($diff_tasks as $id) {
                                    $task = Task::find($id);
                                    $questions = TaskQuestion::where('task_id',$id)->get();
                                    foreach($questions as $question) {
                                        $question->delete();
                                    }
                                    $task->delete();
                                }
                            }
                        }
                    }

                } else {
                    $new_task_group = new TaskGroup();
                    $new_task_group->section_id = $task_group['section_id'];
                    $new_task_group->lesson_template_item_id = $lesson_id;
                    $new_task_group->is_homework = 1;
                    $new_task_group->is_test = 0;
                    $new_task_group->save();
                    if($task_group['tasks'] && count($task_group['tasks'])>0) {
                        foreach ($task_group['tasks'] as $task) {
                            $new_task = new Task();
                            $new_task->task_group_id = $new_task_group->id;
                            $new_task->task_type_id = $task['task_type_id'];
                            $new_task->audio = $task['audio'];
                            $new_task->description = $task['description'];
                            $new_task->image = $task['image'];
                            $new_task->rating = $task['rating'];
                            $new_task->words = $task['words'];
                            $new_task->save();
                            if($task['questions'] && count($task['questions'])>0) {
                                foreach ($task['questions'] as $question) {
                                    $new_question = new TaskQuestion();
                                    $new_question->task_id = $new_task->id;
                                    $new_question->content = $question['content'];
                                    $new_question->is_correct = $question['is_correct'];
                                    $new_question->save();
                                }
                            }
                        }
                    }
                }
            }
            $diff_task_groups = array_diff($old_task_group_ids,$edited_task_group_ids);
            if($diff_task_groups) {
                foreach($diff_task_groups as $id) {
                    $task_group = TaskGroup::find($id);
                    $task_group->delete();
                }
            }
        }

        $lesson->save();

    }

    public function move(request $request) {
        $element = $request->get('element');
        $new_index = $request->get('newIndex');
        $old_index = $request->get('oldIndex');
        $item = LessonTemplateItem::find($element['id']);
        if($old_index < $new_index) {
            $lesson_template_items = LessonTemplateItem::where('lesson_template_id',$element['lesson_template_id'])
                ->where('order','>',$old_index)
                ->where('order','<=',$new_index)
                ->get();
            if(count($lesson_template_items)>0) {
                foreach($lesson_template_items as $lesson_template_item) {
                    $lesson_template_item->order = $lesson_template_item->order - 1;
                    $lesson_template_item->save();
                }
            }
        }
        if($old_index > $new_index) {
            $lesson_template_items = LessonTemplateItem::where('lesson_template_id',$element['lesson_template_id'])
                ->where('order','<',$old_index)
                ->where('order','>=',$new_index)
                ->get();
            if(count($lesson_template_items)>0) {
                foreach($lesson_template_items as $lesson_template_item) {
                    $lesson_template_item->order = $lesson_template_item->order + 1;
                    $lesson_template_item->save();
                }
            }
        }
        $item->order = $new_index;
        $item->save();
        return response()->json(['status' => 'success'],200);
    }

    public function content($id) {
        $item = LessonTemplateItem::where('id','=',$id)->with([
            'pages.materials',
            'homework' => function ($q) {
                $q->where('is_homework',1);
                $q->with('tasks.questions');
            }
        ])->first();
        return $item;
    }

}
