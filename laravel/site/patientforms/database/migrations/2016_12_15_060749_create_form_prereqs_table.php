<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFormPrereqsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('form_prereqs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('parent_form_question_id')->unsigned(); // foreign
            $table->integer('child_form_question_id')->unsigned(); // foreign
            $table->integer('parent_form_answer_id')->unsigned(); //foreign
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('form_prereqs', function (Blueprint $table) {
            $table->foreign('parent_form_question_id')->references('id')->on('form_questions')->onDelete('cascade');
            $table->foreign('child_form_question_id')->references('id')->on('form_questions')->onDelete('cascade');
            $table->foreign('parent_form_answer_id')->references('id')->on('form_answers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('form_prereqs');
    }
}
