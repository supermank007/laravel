<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFormPrereqAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('form_prereq_answers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('form_prereq_id')->unsigned(); // foreign
            $table->integer('form_answer_id')->unsigned(); // foreign
            $table->timestamps();
        });

        Schema::table('form_prereq_answers', function (Blueprint $table) {
            $table->foreign('form_prereq_id')->references('id')->on('form_prereqs');
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
        Schema::dropIfExists('form_prereq_answers');
    }
}
