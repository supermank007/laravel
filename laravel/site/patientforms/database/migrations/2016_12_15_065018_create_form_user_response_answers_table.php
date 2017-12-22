<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFormUserResponseAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('form_user_response_answers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('form_user_response_id')->unsigned(); // foreign
            $table->integer('form_question_id')->unsigned(); // foreign
            $table->integer('form_answer_id')->unsigned()->nullable(); // foreign
            $table->string('value')->default('');
            $table->boolean('prereq_unsatisfied')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('form_user_response_answers', function (Blueprint $table) {
            $table->foreign('form_user_response_id')->references('id')->on('form_user_responses');
            $table->foreign('form_question_id')->references('id')->on('form_questions');
            $table->foreign('form_answer_id')->references('id')->on('form_answers');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('form_user_response_answers');
    }
}
