<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

use App\Program;
use App\User;
use App\Form;
use App\FormAssignment;
use App\FormQuestion;
use App\FormAnswer;
use App\FormUserResponse;
use App\FormUserResponseAnswer;
use App\FormPrereq;
use App\FormPrereqAnswer;

class FormDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::where('email','jordan@mediaura.com')->first();
        $registration = $user->registrations()->first();
        $program = Program::all()->first();

        // Create Form
        $form = new Form;
        $form->name = "Pizza Form";
        $form->description = "This is a form to order a pizza.";
        $form->instructions = "Please answer all questions honestly and legally, or else you'll get a gross pizza.";
        $form->show_instructions = true;
        $form->program()->associate($program);
        $form->creator_user()->associate($user);
        $form->editor_user()->associate($user);
        $form->outcome_form = false;
        $form->published = true;
        $form->archived = false;
        $form->save();

        // Assign Form
        $assignment = new FormAssignment;
        $assignment->registration()->associate($registration);
        $assignment->assigner_user()->associate($user);
        $assignment->form()->associate($form);
        $assignment->save();

        // Add Form Questions
        $q1 = new FormQuestion;
        $q1->form()->associate($form);
        $q1->order = 0;
        $q1->label = "Choose your size:";
        $q1->required = true;
        $q1->type = 'radio';
        $q1->save();

        $q2 = new FormQuestion;
        $q2->form()->associate($form);
        $q2->order = 1;
        $q2->label = "Toppings";
        $q2->required = true;
        $q2->type = 'checkbox';
        $q2->save();

        $q3 = new FormQuestion;
        $q3->form()->associate($form);
        $q3->order = 2;
        $q3->label = "You chose to order extra cheese. Please specify how much:";
        $q3->required = true;
        $q3->type = 'radio';
        $q3->save();

        $q4 = new FormQuestion;
        $q4->form()->associate($form);
        $q4->order = 3;
        $q4->label = "Please enter any special delivery directions:";
        $q4->required = true;
        $q4->type = 'text';
        $q4->save();

        // Add Form Answers

        // Q1
        $a1 = new FormAnswer;
        $a1->question()->associate($q1);
        $a1->order = 0;
        $a1->label = '6"';
        $a1->type = 'radio';
        $a1->save();

        $a2 = new FormAnswer;
        $a2->question()->associate($q1);
        $a2->order = 0;
        $a2->label = '12"';
        $a2->type = 'radio';
        $a2->save();

        $a3 = new FormAnswer;
        $a3->question()->associate($q1);
        $a3->order = 0;
        $a3->label = '18"';
        $a3->type = 'radio';
        $a3->save();


        // Q2
        $a6 = new FormAnswer;
        $a6->question()->associate($q2);
        $a6->order = 0;
        $a6->label = 'Extra Cheese';
        $a6->type = 'checkbox';
        $a6->save();

        $a7 = new FormAnswer;
        $a7->question()->associate($q2);
        $a7->order = 0;
        $a7->label = 'Pepperoni';
        $a7->type = 'checkbox';
        $a7->save();

        $a8 = new FormAnswer;
        $a8->question()->associate($q2);
        $a8->order = 0;
        $a8->label = 'Peppers';
        $a8->type = 'checkbox';
        $a8->save();

        $a9 = new FormAnswer;
        $a9->question()->associate($q2);
        $a9->order = 0;
        $a9->label = 'Pineapple';
        $a9->type = 'checkbox';
        $a9->save();

        // Q3
        $a10 = new FormAnswer;
        $a10->question()->associate($q3);
        $a10->order = 0;
        $a10->label = 'A little';
        $a10->type = 'radio';
        $a10->save();

        $a11 = new FormAnswer;
        $a11->question()->associate($q3);
        $a11->order = 0;
        $a11->label = 'A lot!';
        $a11->type = 'radio';
        $a11->save();

        // Q4

        $a8 = new FormAnswer;
        $a8->question()->associate($q4);
        $a8->order = 0;
        $a8->label = '';
        $a8->type = 'text';
        $a8->save();

        // Add Prereqs

        $pr1 = new FormPrereq;
        $pr1->parent_form_question()->associate($q2);
        $pr1->child_form_question()->associate($q3);
        $pr1->parent_form_answer()->associate($a6);
        $pr1->save();

        // // Add User Response
        // $r = new FormUserResponse;
        // $r->form()->associate($form);
        // $r->user()->associate($user);
        // $r->in_progress = false;
        // $r->data_retrieved = false;
        // $r->save();

        // // Add User Response Answers
        // $ra1 = new FormUserResponseAnswer;
        // $ra1->response()->associate($r);
        // $ra1->question()->associate($a2->question);
        // $ra1->answer()->associate($a2);
        // $ra1->save();

        // $ra2 = new FormUserResponseAnswer;
        // $ra2->response()->associate($r);
        // $ra2->question()->associate($a6->question);
        // $ra2->answer()->associate($a6);
        // $ra2->value = "This is my response.";
        // $ra2->save();
    }
}
