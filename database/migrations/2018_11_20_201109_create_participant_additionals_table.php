<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateParticipantAdditionalsTable extends Migration
{
    /**
     * Ерсултан
     *  Дополнительные параметры для заявок, которые сохраняют все нужные данные для любой заявки
     *  так же имеют локализацию
     *
     */
    public function up()
    {
        Schema::create('participant_additionals', function (Blueprint $table) {
            $table->increments('id');                      //айди
            $table->integer('participant_id')->unsigned();        //айди родительской модели
            $table->string('locale_id')->index();             //локализация
            $table->string('company_name');                //название компании
            $table->string('company_address');             //адрес компании
            $table->string('company_post_index');          //пост индекс компании
            $table->string('learning_language');           //язык предпочитаемого обучения
            $table->string('project_id');                  //айди проекта
            $table->string('phone');                       //телефон компании
            $table->string('email');                       //электронная почта компании
            $table->unique(['participant_id','locale_id']);              // убедиться что нет дубляции переводов
            //убедиться что при удалении заявки, так же удаляются дополнительные данные с этой таблицы
            $table->foreign('participant_id')->references('id')->on('participants')->onDelete('cascade');
            $table->timestamp('created_at');
            $table->timestamp('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('participant_additionals');
    }
}