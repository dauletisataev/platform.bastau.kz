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
            $table->string('old_value')->nullable();
            $table->string('new_value')->nullable();
            $table->string('field')->nullable();
            $table->string('action_name')->nullable();
            $table->integer('trainer_id')->unsigned()->nullable();
            $table->foreign('trainer_id')->references('id')->on('trainers')->onDelete('cascade');
            $table->integer('actor_id')->unsigned();
            $table->foreign('actor_id')->references('id')->on('users')->onDelete('cascade');
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
