<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFormAssignmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('form_assignments', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('user_registration_id'); // foreign
            $table->integer('form_id')->unsigned(); // foreign
            $table->uuid('assigner_user_id')->nullable(); // foreign
            $table->boolean('complete')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('form_assignments', function (Blueprint $table) {
            $table->foreign('user_registration_id')->references('id')->on('user_registrations');
            $table->foreign('form_id')->references('id')->on('forms');
            $table->foreign('assigner_user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('form_assignments');
    }
}
