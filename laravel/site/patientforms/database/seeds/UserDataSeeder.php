<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

use App\Role;
use App\Program;
use App\User;
use App\UserRegistration;

class UserDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        // Create roles
        $role1 = new Role;
        $role1->role = 'User';
        $role1->save();

        $role2 = new Role;
        $role2->role = 'Admin';
        $role2->save();

        $role3 = new Role;
        $role3->role = 'Super-Admin';
        $role3->save();

        $role4 = new Role;
        $role4->role = 'Master-Admin';
        $role4->save();

        // Create programs
        $program1 = new Program;
        $program1->name = 'Remuda Ranch';
        $program1->description = 'Remuda Ranch is an eating disorder treatment center that provides teen eating disorder and residential eating disorder treatment - including bulimia and anorexia disorder treatment. It provides individualized help for eating disorders for women.';
        $program1->save();

        $program2 = new Program;
        $program2->name = 'The Meadows';
        $program2->description = 'The Meadows is the most trusted name in Addiction Treatment Program. For the last 35 years, we have been the best inpatient Drug rehab center in Arizona and all over the US.';
        $program2->save();

        // Create users
        $user1 = new User;
        $user1->id = \Webpatser\Uuid\Uuid::generate(4);
        $user1->email = 'jordan@mediaura.com';
        $user1->first_name = 'Jordan';
        $user1->last_name = 'Williamson';
        $user1->password = bcrypt("test12");
        $user1->program()->associate($program1);
        $user1->save();
        $user1->roles()->attach($role4);
        $user1->roles()->attach($role3);
        $user1->roles()->attach($role2);
        $user1->roles()->attach($role1);

        $user2 = new User;
        $user2->id = \Webpatser\Uuid\Uuid::generate(4);
        $user2->email = 'user@test.com';
        $user2->first_name = 'Test';
        $user2->last_name = 'User';
        $user2->password = bcrypt("test12");
        $user2->program()->associate($program2);
        $user2->save();
        $user2->roles()->attach($role1);

        $user3 = new User;
        $user3->id = \Webpatser\Uuid\Uuid::generate(4);
        $user3->email = 'andrew@mediaura.com';
        $user3->first_name = 'Andrew';
        $user3->last_name = 'Aebersold';
        $user3->password = bcrypt("test12");
        $user3->program()->associate($program1);
        $user3->save();
        $user3->roles()->attach($role3);

        $user4 = new User;
        $user4->id = \Webpatser\Uuid\Uuid::generate(4);
        $user4->email = 'admin@test.com';
        $user4->first_name = 'Test';
        $user4->last_name = 'Admin';
        $user4->password = bcrypt("test12");
        $user4->program()->associate($program1);
        $user4->save();
        $user4->roles()->attach($role2);

        // Create user registrations
        $registration1 = new UserRegistration;
        $registration1->id = \Webpatser\Uuid\Uuid::generate(4);
        $registration1->registration_number = '12345';
        $registration1->user()->associate($user1);
        $registration1->program()->associate($program1);
        $registration1->save();

        $registration1 = new UserRegistration;
        $registration1->id = \Webpatser\Uuid\Uuid::generate(4);
        $registration1->registration_number = '67890';
        $registration1->user()->associate($user2);
        $registration1->program()->associate($program1);
        $registration1->save();

    }
}
