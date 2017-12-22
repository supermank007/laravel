<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserRegistrationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_registrations', function (Blueprint $table) {
            $table->uuid('id');
            $table->string('registration_number')->unique();
            $table->integer('program_id')->unsigned();
            $table->uuid('user_id')->nullable();
            $table->timestamps();
            $table->dateTime('discharged_at')->nullable()->default(null);
            $table->softDeletes();
            $table->boolean('active')->default(true);

            $table->primary('id');
        });

        Schema::table('user_registrations', function (Blueprint $table) {
            $table->foreign('program_id')->references('id')->on('programs');
            $table->foreign('user_id')->references('id')->on('users');
        });

        DB::unprepared('
            CREATE TRIGGER before_insert_user_registrations
                BEFORE INSERT ON user_registrations
                FOR EACH ROW
                SET new.id = IF (new.id != "", new.id, uuid());
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_registrations');
    }
}
