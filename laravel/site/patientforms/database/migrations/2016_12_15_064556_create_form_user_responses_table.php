<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFormUserResponsesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('form_user_responses', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('form_assignment_id')->unsigned(); // foreign
            $table->boolean('in_progress')->default(true);
            $table->boolean('data_retrieved')->default(false);
            $table->timestamps();
            $table->dateTime('submitted_at')->nullable()->default(null);
            $table->softDeletes();
        });

        Schema::table('form_user_responses', function (Blueprint $table) {
            $table->foreign('form_assignment_id')->references('id')->on('form_assignments');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('form_user_responses');
    }
}
