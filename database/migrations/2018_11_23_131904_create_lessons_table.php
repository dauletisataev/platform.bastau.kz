<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLessonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lessons', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 255);
            $table->integer('passed');
            $table->timestamp('datetime');
            $table->integer('without_date');
            $table->double('duration');
            $table->integer('is_full');
            $table->string('manual');
            $table->integer('is_started');
            $table->integer('lesson_template_item_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lessons');
    }
}
