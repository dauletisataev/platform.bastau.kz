<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroupHistory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('group_histories', function (Blueprint $table) {
            $table->increments('id');                        //айди
            $table->string('action');                        //название изменения
            $table->string("old_value")->nullable();                     //старое значение
            $table->string("new_value")->nullable();
            $table->string("field_name")->nullable();//новое значение
            $table->integer('actor_user')->unsigned();       //кем изменено
            $table->timestamp('created_at');                 //время созданного изменения
            $table->timestamp('updated_at');                 //время изменения таблицы, в принципе не будем пользоваться этим
            $table->integer("group_id")->unsigned();
            $table->foreign('group_id')->references('id')->on('groups')->onDelete('cascade');
            $table->foreign('actor_user')->references('id')->on('users');
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
