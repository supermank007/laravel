<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Clear existing data
        DB::table('form_outcome_intervals')->delete();
        DB::table('form_prereqs')->delete();
        DB::table('form_user_response_answers')->delete();
        DB::table('form_user_responses')->delete();
        DB::table('form_answers')->delete();
        DB::table('form_questions')->delete();
        DB::table('form_assignments')->delete();
        DB::table('forms')->delete();
        DB::table('user_registrations')->delete();
        DB::table('users')->delete();
        DB::table('programs')->delete();
        DB::table('roles')->delete();

        // Seed User Data
        $this->call(UserDataSeeder::class);
        // Seed Form Data
        $this->call(FormDataSeeder::class);
    }
}
