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
            $table->integer('passed');
            $table->timestamp('datetime');
            $table->integer('without_date');
            $table->double('duration');
            $table->integer('is_full');
            $table->integer('is_started');
            $table->integer('order');
            $table->integer('access_tasks')->default(0);
            $table->integer('access_homework')->default(0);
            $table->integer('access_tests')->default(0);
            $table->integer('access_offset')->default(0);
            $table->integer('access_offset_value');
            $table->string('title',255)->nullable();
            $table->string('access_offset_type', 191)->nullable();
            $table->string('manual')->nullable();
            $table->integer('lesson_template_id')->unsigned();
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
