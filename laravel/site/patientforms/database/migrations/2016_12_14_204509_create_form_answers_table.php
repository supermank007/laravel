<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFormAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('form_answers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('form_question_id')->unsigned(); // foreign
            $table->integer('order')->unsigned()->default(0);
            $table->string('label')->default('');
            $table->enum('type', ['radio', 'radio_text', 'text', 'checkbox']);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('form_answers', function (Blueprint $table) {
            $table->foreign('form_question_id')->references('id')->on('form_questions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('form_answers');
    }
}
