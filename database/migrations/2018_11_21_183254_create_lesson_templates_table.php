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
            $table->integer('project_id');
            $table->boolean("has_test")->default(false);
            $table->time('test_duration')->nullable();
            $table->string("type",191)->default("online");
            $table->string('image', 191);
        });

        //localization of lesson templates
        Schema::create("lesson_template_translations",function (Blueprint $table){
            $table->increments("id");
            $table->string("locale")->index();
            $table->integer("lesson_template_id")->unsigned();
            $table->unique(['lesson_template_id','locale']);
            $table->foreign('lesson_template_id')->references('id')->on('lesson_templates')->onDelete('cascade');

            $table->string("name",255);
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
