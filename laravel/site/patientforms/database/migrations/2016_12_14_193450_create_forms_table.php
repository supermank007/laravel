<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFormsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('forms', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->default('');
            $table->string('description')->default('');
            $table->integer('program_id')->unsigned(); // foreign
            $table->uuid('creator_user_id'); // foreign
            $table->uuid('editor_user_id'); // foreign
            $table->boolean('outcome_form')->default(false);
            $table->boolean('published')->default(true);
            $table->boolean('archived')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('forms', function (Blueprint $table) {
            $table->foreign('program_id')->references('id')->on('programs');
            $table->foreign('creator_user_id')->references('id')->on('users');
            $table->foreign('editor_user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('forms');
    }
}
