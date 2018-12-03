<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArchiveReasonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {


        Schema::create('archive_reasons', function (Blueprint $table) {
            $table->increments('id');                      //айди
            $table->string('locale_id')->index();             //локализация
            $table->string('reason');
            $table->unique(['id','locale_id']);
            $table->integer("role_id")->unsigned();
        });


        Schema::table('participants', function (Blueprint $table) {
            $table->foreign('archive_reason_id')->references('id')->on('archive_reasons')->onDelete('cascade');
        });
        Schema::table('participant_histories', function (Blueprint $table) {
            $table->integer("archive_reason_id")->unsigned()->nullable();
            $table->foreign('archive_reason_id')->references('id')->on('archive_reasons')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('archive_reasons');
    }
}
