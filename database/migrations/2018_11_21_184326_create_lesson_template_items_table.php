<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLessonTemplateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lesson_template_items', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('lesson_template_translation_id')->unsigned();
            $table->integer('lesson_template_id')->unsigned();
            $table->integer('order');
            $table->integer('access_tasks')->default(0);
            $table->integer('access_homework')->default(0);
            $table->integer('access_tests')->default(0);
            $table->integer('access_offset')->default(0);
            $table->integer('access_offset_value');
            $table->string('title',255);
            $table->string('access_offset_type', 191)->nullable();
            $table->string('manual');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lesson_template_items');
    }
}
