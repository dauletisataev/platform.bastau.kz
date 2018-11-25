<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('patronymic');
            $table->enum('gender',['M','F']);
            $table->string('iin')->trim()->unique();
            $table->string('phone')->unique();
            $table->string('email')->nullable();
            $table->integer('role_id')->unsigned();
            $table->string('photo')->nullable();
            $table->string('password')->nullable();
            $table->date('deleted_at')->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
