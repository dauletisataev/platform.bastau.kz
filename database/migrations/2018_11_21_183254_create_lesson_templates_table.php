<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLessonTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lesson_templates', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',255);
            $table->integer('program_id');
            $table->integer('level_id');
            $table->string('image', 191);
            $table->integer('type');
            $table->integer('cost');
            $table->integer('role_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lesson_templates');
    }
}
