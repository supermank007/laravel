<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id');
            $table->integer('program_id')->unsigned();
            $table->string('first_name')->default('');
            $table->string('last_name')->default('');
            $table->string('email');
            $table->string('password')->default('');
            $table->boolean('active')->default(1);
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['email', 'deleted_at']);

            $table->primary('id');
        });

        DB::unprepared('
            CREATE TRIGGER before_insert_users
                BEFORE INSERT ON users
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
        Schema::dropIfExists('users');
        //DB::unprepared('DROP TRIGGER `before_insert_users`');
    }
}
