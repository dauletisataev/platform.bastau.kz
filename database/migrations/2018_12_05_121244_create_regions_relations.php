<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRegionsRelations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('locality_user', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('locality_id')->unsigned();
        });
        Schema::create('group_locality', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('group_id')->unsigned();
            $table->integer('locality_id')->unsigned();
        });
        Schema::table('groups',function (Blueprint $table){
            $table->integer('locality_id')->unsigned();
            $table->foreign('locality_id')->references('id')->on('localities')->onDelete('restrict');
        });
        Schema::table('users',function (Blueprint $table){
            $table->integer('locality_id')->unsigned();
            $table->foreign('locality_id')->references('id')->on('localities')->onDelete('restrict');
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
