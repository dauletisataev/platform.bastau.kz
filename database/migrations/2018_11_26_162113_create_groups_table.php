<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('groups', function (Blueprint $table) {
            $table->increments('id');
            $table->dateTime('next_lesson')->nullable();
            $table->dateTime('start_date')->nullable();
            $table->boolean("in_archive")->default(false);
            $table->boolean('online');
            $table->integer('capacity');
            $table->integer('project_id')->unsigned();
            $table->timestamps();
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->enum('language',['ru','kz']);
            $table->integer('participants_number')->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
