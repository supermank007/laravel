<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFormQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('form_questions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('form_id')->unsigned(); // foreign
            $table->integer('order')->unsigned()->default(0);
            $table->string('label')->default('');
            $table->boolean('required')->default(true);
            $table->enum('type', ['radio', 'text', 'checkbox']);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('form_questions', function (Blueprint $table) {
            $table->foreign('form_id')->references('id')->on('forms');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('form_questions');
    }
}
