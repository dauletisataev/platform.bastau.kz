<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLessonQuestionAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
*/

    public function up()
    {
        Schema::create('lesson_question_answers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('lesson_question_id');
            $table->integer('option');
            $table->string('value');
            $table->boolean('is_correct');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lesson_question_answers');
    }
}
