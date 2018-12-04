<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBTrainerHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bthistories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('trainers');                         // tracks this table
            $table->string('old_value');
            $table->string('new_value');
            $table->string('field');                            // which field was altered
            $table->string('actor');                            // who made changes
            // relation with trainer
            $table->integer('trainer_id')->unsigned();
            $table->foreign('trainer_id')->references('id')->on('trainers')->onDelete('cascade');
            // relation with actor
            $table->integer('actor_id')->unsigned();
            $table->foreign('actor_id')->references('id')->on('users')->onDelete('cascade');
            $table->text('body');                               // description of action
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('b_trainer_histories');
    }
}
