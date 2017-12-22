<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFormOutcomeIntervalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('form_outcome_intervals', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('form_id')->unsigned(); // foreign
            $table->integer('interval')->unsigned();
            $table->timestamps();
        });

        Schema::table('form_outcome_intervals', function (Blueprint $table) {
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
        Schema::dropIfExists('form_outcome_intervals');
    }
}
