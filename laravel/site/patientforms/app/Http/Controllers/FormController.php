<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Illuminate\Database\Eloquent\ModelNotFoundException;

use Validator;

use App\Program;
use App\Form;
use App\FormQuestion;
use App\FormAnswer;
use App\User;
use App\FormUserResponse;
use App\FormUserResponseAnswer;
use App\FormAssignment;
use App\FormPrereq;
use App\UserRegistration;
use App\FormOutcomeInterval;

use Carbon\Carbon;

class FormController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();


        if ($user->hasRoles('Super-Admin') || $user->hasRoles('Master-Admin')) {
            $forms = Form::all();
        } elseif ($user->hasRoles('Admin')) {
            $forms = Form::where('published', true)->where('archived', false)->get();
        } else {
            $user_registration = User::getUserRegistration();
            if (is_null($user_registration)) {
                return redirect()->route('select_registration');
            }
            $form_ids = $user_registration->form_assignments()
                ->where('complete', false)
                ->pluck('form_id');
            $forms = [];
            foreach ($form_ids as $id) {
                $form = Form::find($id);
                if ($form->published)
                    $forms[] = Form::find($id);
            }
        }
        return view('admin.forms-index', ['forms' => $forms]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $programs = Program::all();
        return view('forms.create', ['form_method' => 'POST', 'programs' => $programs]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        // Validate data

        $validator = Validator::make($request->form, [
            'name' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                $data = [
                    'success' => false,
                    'data' => $validator->errors()
                ];
                return $data;
            } else {
                return redirect('forms/create')
                    ->withErrors($validator)
                    ->withInput();
            }
        }

        // Make sure there is at least one question
        

        $form = new Form;
        $form->name = $request->form['name'];
        $form->description = $request->form['description'];
        $form->instructions = $request->form['instructions'];
        $form->show_instructions = $request->form['show_instructions'];
        $form->outcome_form = $request->form['outcome_form'];
        $form->published = $request->form['published'];

        $program = Program::find($request->form['program_id']);
        $user = Auth::user();

        $form->program()->associate($program);
        $form->creator_user()->associate($user);
        $form->editor_user()->associate($user);

        $form->save();

        $questions = [];
        $answers = [];

        // Create form questions
        foreach ($request->form['questions'] as $question) {

            $form_question = new FormQuestion;
            $form_question->label = $question['label'];
            $form_question->order = $question['order'];
            $form_question->required = $question['required'];
            $form_question->type = $question['type'];
            $form_question->form()->associate($form);

            $form_question->save();

            $questions[ $question['id'] ] = $form_question->id;

            $answers[ $question['id'] ] = [];

            // Create form answers
            if ($question['type'] == 'text') {

                $form_answer = new FormAnswer;
                $form_answer->label = '';
                $form_answer->order = 1;
                $form_answer->type = 'text';
                $form_answer->question()->associate($form_question);

                $form_answer->save();

            } else {

                foreach ($question['answers'] as $answer) {

                    $form_answer = new FormAnswer;
                    $form_answer->label = $answer['label'];
                    $form_answer->order = $answer['order'];
                    $form_answer->type = $answer['type'];
                    $form_answer->question()->associate($form_question);

                    $form_answer->save();

                    $answers[ $question['id'] ][ $answer['id'] ] = $form_answer->id;

                }

            }


        }

        // Create form prereqs

        foreach ($request->form['prereqs'] as $prereq) {

            $parent_id = $prereq['parent_question_id'];
            $child_id = $prereq['child_question_id'];
            $answer_id = $prereq['answer'];

            // create new FormPrereq
            $parent_question = $questions[ $parent_id ];
            $child_question = $questions[ $child_id ];
            $answer = $answers[ $parent_id ][ $answer_id ];
            
            $prereq = new FormPrereq;

            $prereq->parent_form_question()->associate($parent_question);
            $prereq->child_form_question()->associate($child_question);
            $prereq->parent_form_answer()->associate($answer);
            $prereq->save();

        }

        if ($form->outcome_form) {
            // Create outcome intervals
            foreach ($request->form['outcome_intervals'] as $interval) {
                $outcome_interval = new FormOutcomeInterval;
                $outcome_interval->form()->associate($form);
                $outcome_interval->interval = $interval['value'];
                $outcome_interval->save();
            }
        }

        if ($request->ajax()) {
            $data = [
                'success' => true,
                'data' => ['form_id' => $form->id]
            ];

            return json_encode($data);
        } else {
            return redirect()->route('forms.index');
        }

    }

    public function duplicate(Form $form) {

        $new_form = new Form;
        $new_form->name = $form->name;
        $new_form->description = $form->description;
        $new_form->instructions = $form->instructions;
        $new_form->show_instructions = $form->show_instructions;
        $new_form->outcome_form = $form->outcome_form;
        $new_form->program_id = $form->program_id;
        $new_form->creator_user_id = Auth::user()->id;
        $new_form->editor_user_id = Auth::user()->id;
        $new_form->published = $form->published;
        $new_form->archived = $form->archived;

        $new_form->save();

        $questions = [];
        $answers = [];
        $prereqs = [];

        foreach ($form->questions as $question) {

            $new_question = new FormQuestion;
            $new_question->form_id  = $new_form->id;
            $new_question->label    = $question->label;
            $new_question->order    = $question->order;
            $new_question->required = $question->required;
            $new_question->type     = $question->type;

            $new_question->save();

            $questions[$question->id] = $new_question->id;

            foreach ($question->answers as $answer) {

                $new_answer = new FormAnswer;
                $new_answer->form_question_id = $new_question->id;
                $new_answer->label = $answer->label;
                $new_answer->order = $answer->order;
                $new_answer->type  = $answer->type;

                $new_answer->save();

                $answers[$answer->id] = $new_answer->id;

            }

            foreach ($question->parent_prereqs as $prereq) {

                $prereqs[] = $prereq;

            }

        }

        foreach ($prereqs as $prereq) {

            $new_prereq = new FormPrereq;
            $new_prereq->parent_form_question_id = $questions[$prereq->parent_form_question_id];
            $new_prereq->child_form_question_id  = $questions[$prereq->child_form_question_id];
            $new_prereq->parent_form_answer_id   = $answers[$prereq->parent_form_answer_id];

            $new_prereq->save();

        }

        foreach ($form->outcome_intervals as $outcome_interval) {

            $new_outcome_interval = new FormOutcomeInterval;
            $new_outcome_interval->form_id = $form->id;
            $new_outcome_interval->interval = $outcome_interval->interval;

            $new_outcome_interval->save();

        }

        return ['success' => true];

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Form $form)
    {
        if ($form->published) {
            return redirect()->route('access_denied');
        }
        $programs = Program::all();
        return view('forms.create', ['form' => $form, 'form_method' => 'PUT', 'programs' => $programs]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Form $form)
    {

        $form->name = $request['form']['name'];
        $form->description = $request['form']['description'];
        $form->instructions = $request['form']['instructions'];
        $form->show_instructions = $request['form']['show_instructions'];
        $form->program_id = $request['form']['program_id'];
        $form->outcome_form = $request['form']['outcome_form'];
        $form->editor_user()->associate(Auth::user());
        $form->published = $request['form']['published'];

        $new_question_ids = [];
        $delete_question_ids = [];

        $question_ids = $form->questions()->pluck('id');
        foreach ($question_ids as $id) {
            $delete_question_ids[$id] = true;
        }

        foreach ($request['form']['questions'] as $question_data) {

            $question_is_new = $question_data['new'];

            if ($question_is_new) {
                // Create new question
                $form_question = new FormQuestion;
            } else {
                $form_question = FormQuestion::find($question_data['id']);
                $delete_question_ids[$form_question->id] = false;
            }

            $form_question->form()->associate($form);
            $form_question->order = $question_data['order'];
            $form_question->label = $question_data['label'];
            $form_question->required = $question_data['required'];
            $form_question->type = $question_data['type'];

            $form_question->save();

            if ($question_is_new) {
                $new_question_ids[$question_data['id']] = $form_question->id;
                
            }

            $new_answer_ids = [];
            $delete_answer_ids = [];

            $answer_ids = $form_question->answers()->pluck('id');
            foreach ($answer_ids as $id) {
                $delete_answer_ids[$id] = true;
            }

            foreach ($question_data['answers'] as $answer_data) {

                $answer_is_new = $answer_data['new'];

                if ($answer_is_new) {
                    // Create new answer
                    $form_answer = new FormAnswer;
                } else {
                    $form_answer = FormAnswer::find($answer_data['id']);
                    $delete_answer_ids[$form_answer->id] = false;
                }

                $form_answer->form_question_id = $form_question->id;
                $form_answer->order = $answer_data['order'];
                $form_answer->label = $answer_data['label'];
                $form_answer->type = $answer_data['type'];

                $form_answer->save();

                if ($answer_is_new) {
                    $new_answer_ids[$answer_data['id']] = $form_answer->id;
                }

            }

            foreach ($delete_answer_ids as $id => $delete) {
                if ($delete) {
                    FormUserResponseAnswer::where('form_answer_id', $id)->delete();
                    FormAnswer::find($id)->delete();
                }
            }

            if ($question_is_new && $form_question->type == 'text') {
                $form_answer = new FormAnswer;
                $form_answer->form_question_id = $form_question->id;
                $form_answer->order = 1;
                $form_answer->label = '';
                $form_answer->type = 'text';

                $form_answer->save();
            }

        }

        $delete_prereq_ids = [];

        $prereq_ids = FormPrereq::whereIn('parent_form_question_id', $question_ids)
            ->whereIn('child_form_question_id', $question_ids, 'or')
            ->pluck('id');
        foreach ($prereq_ids as $id) {
            $delete_prereq_ids[$id] = true;
        }

        foreach ($request['form']['prereqs'] as $prereq_data) {

            $prereq_is_new = $prereq_data['new'];

            if ($prereq_is_new) {
                $form_prereq = new FormPrereq;
            } else {
                $form_prereq = FormPrereq::find($prereq_data['id']);
                $delete_prereq_ids[$form_prereq->id] = false;
            }

            if ( isset( $new_question_ids[ $prereq_data['parent_question_id'] ] ) ) {
                $parent_question_id = $new_question_ids[ $prereq_data['parent_question_id'] ];
            } else {
                $parent_question_id = $prereq_data['parent_question_id'];
            }

            if ( isset( $new_question_ids[ $prereq_data['child_question_id'] ] ) ) {
                $child_question_id = $new_question_ids[ $prereq_data['child_question_id'] ];
            } else {
                $child_question_id = $prereq_data['child_question_id'];
            }

            if ( isset( $new_answer_ids[ $prereq_data['answer'] ] ) ) {
                $parent_answer_id = $new_answer_ids[ $prereq_data['answer'] ];
            } else {
                $parent_answer_id = $prereq_data['answer'];
            }

            $parent_question = FormQuestion::find($parent_question_id);
            $form_prereq->parent_form_question()->associate($parent_question);

            $child_question = FormQuestion::find($child_question_id);
            $form_prereq->child_form_question()->associate($child_question);

            $parent_answer = FormAnswer::find($parent_answer_id);
            $form_prereq->parent_form_answer()->associate($parent_answer);

            $form_prereq->save();

        }

        foreach ($delete_question_ids as $id => $delete) {
            if ($delete) {
                FormUserResponseAnswer::where('form_question_id', $id)->delete();
                FormQuestion::find($id)->delete();
            }
        }

        foreach ($delete_prereq_ids as $id => $delete) {
            if ($delete) {
                FormPrereq::find($id)->delete();
            }
        }

        // Create outcome intervals
        $form->outcome_intervals()->delete();
        if ($form->outcome_form) {
            foreach ($request->form['outcome_intervals'] as $interval) {
                $outcome_interval = new FormOutcomeInterval;
                $outcome_interval->form()->associate($form);
                $outcome_interval->interval = $interval['value'];
                $outcome_interval->save();
            }
        }

        $form->save();
        return ['success' => 1];

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Form $form)
    {
        $assignments = $form->assignments()->delete();
        $form->delete();
        return "form deleted";
    }

    public function assign(Request $request) {
        $assignable_forms = Form::where('published', true)
            ->where('archived', false)
            ->get();

        $selected_form = null;
        if (isset($request->f)) {
            $selected_form_id = $request->f;
            $selected_form = Form::findOrFail($selected_form_id);
        }
        return view('admin.forms-assign', ['selected_form' => $selected_form, 'forms' => $assignable_forms]);
    }

    public function take($form_id = null, $question_index = null) {
        $user_registration = User::getUserRegistration();

        $form = Form::find($form_id);

        // Check that form exists
        if ( is_null($form) ) {
            abort(401, "This form either does not exist or you are not allowed to view it.");
        }

        // Check that form is assigned to user
        $assignment = $form->assignments()
            ->where('user_registration_id', $user_registration->id)
            ->first();
        if ( is_null($assignment) ) {
            abort(401, "This form either does not exist or you are not allowed to view it.");
        }

        // Check that form is published
        if ( ! $form->published ) {
            abort(401, "This form either does not exist or you are not allowed to view it.");
        }

        // Check that user hasn't already completed form
        $user_response = $assignment->response;
        if (!is_null($user_response) && $user_response->in_progress == false) {
            return view('error', ['error' => "You have already completed this form."]);
        }

        return view('user.forms-take', ['form' => $form]);
    }

    public function showInstructions(Request $request, $form_id) {
        $user_registration = User::getUserRegistration();

        $form = Form::find($form_id);

        // Check that form exists
        if ( is_null($form) ) {
            abort(401, "This form either does not exist or you are not allowed to view it.");
        }

        // Check that form is assigned to user
        $assignment = $form->assignments()
            ->where('user_registration_id', $user_registration->id)
            ->first();
        if ( is_null($assignment) ) {
            abort(401, "This form either does not exist or you are not allowed to view it.");
        }

        // Check that form is published
        if ( ! $form->published ) {
            abort(401, "This form either does not exist or you are not allowed to view it.");
        }

        // Check that user hasn't already completed form
        $user_response = $assignment->response;
        if (!is_null($user_response) && $user_response->in_progress == false) {
            return view('error', ['error' => "You have already completed this form."]);
        }

        $question_index = 1;
        if (isset($request->question_index))
            $question_index = $request->question_index;

        return view('user.forms-instructions', ['form' => $form, 'question_index' => $question_index]);
    }

    public function resume(Request $request, $form_id) {

        $user_registration = User::getUserRegistration();

        $form = Form::findOrFail($form_id);

        // Check that form is assigned to user
        $assignment = $form->assignments()
            ->where('user_registration_id', $user_registration->id)
            ->first();
        if ( is_null($assignment) ) {
            abort(401, "This form either does not exist or you are not allowed to view it.");
        }

        // Check that form is published
        if ( ! $form->published ) {
            abort(401, "This form either does not exist or you are not allowed to view it.");
        }

        // Check that user has started form
        $user_response = $assignment->response;

        if (is_null($user_response)) {
            return redirect()->route('forms.take', ['form_id' => $form->id]);
        }

        // Check that user hasn't already completed form
        if (!is_null($user_response) && $user_response->in_progress == false) {
            return view('error', ['error' => "You have already completed this form."]);
        }

        // Get first unanswered question
        $questions = $form->questionsSorted();
        foreach ($questions as $i => $question) {
            $answers = $user_response->answers()
                ->where('form_question_id', $question->id)
                ->get();
            if (count($answers) == 0) {

                if ($form->show_instructions) {
                    $route = 'forms.instructions';
                } else {
                    $route = 'forms.takeQuestion';
                }
                return redirect()->route($route,
                    [
                        'form_id' => $form_id,
                        'question_index' => $i + 1
                    ]
                );
            }
        }

        // if all questions answered, go to review page
        return redirect()->route(
            'forms.review',
            ['response_id' => $user_response->id]
        );

    }


    public function takeQuestion(Request $request, $form_id, $question_index) {

        $question_index = +$question_index;

        $user_registration = User::getUserRegistration();

        $form = Form::findOrFail($form_id);

        // Check that form is assigned to user
        $assignment = $form->assignments()
            ->where('user_registration_id', $user_registration->id)
            ->first();
        if ( is_null($assignment) ) {
            abort(401, "This form either does not exist or you are not allowed to view it.");
        }

        // Check that form is published
        if ( ! $form->published ) {
            abort(401, "This form either does not exist or you are not allowed to view it.");
        }

        // Check that user hasn't already completed form
        $user_response = $assignment->response;
        if (!is_null($user_response) && $user_response->in_progress == false) {
            return view('error', ['error' => "You have already completed this form."]);
        }
        if (is_null($user_response)) {
            $user_response = new FormUserResponse;
            $user_response->assignment()->associate($assignment);
            $user_response->in_progress = true;
            $user_response->data_retrieved = false;
            $user_response->save();            
        }

        $questions = $form->questionsSorted();

        // Make sure any previous required questions have been answered
        for ( $i = 0; $i < count($questions); $i++) {
            $question = $questions[$i];
            if ($i == $question_index - 1) break;

            $is_answered = $user_response->answers()
                ->where('form_question_id', $question->id)
                ->count() > 0;
            if ($question->required && ! $is_answered) {
                return redirect()->route('forms.takeQuestion',
                    [
                        'form_id' => $form_id,
                        'question_index' => $i+1,
                        'error' => 'required'
                    ]
                );
            }
        }

        // Check that question exists
        if (!isset($questions[$question_index - 1])) {
            abort(404, "This question does not exist.");
        }

        $question = $questions[$question_index - 1];

        // include current response answer if available
        $current_response_answer_ids = [];
        $current_response_answer_value = null;
        if ($user_response) {
            $current_response_answers = $user_response->answers()->where('form_question_id', $question->id)->get();
            $current_response_answer_ids = map(
                function($answer) {
                    return $answer->form_answer_id;
                },
                $current_response_answers
            );
            if (count($current_response_answers) && ($question->type == 'text' || $question->type == 'radio')) {
                $current_response_answer_value = $current_response_answers[0]->value;
            }
        }

        // Prerequisite question logic
        $prereqs = $question->child_prereqs;
        if (count($prereqs) > 0) {
            foreach ($prereqs as $prereq) {

                if ($prereq->isAnswered($user_response)) {
                    if ($prereq->isSatisfied($user_response)) {
                        return view('user.forms-take-question',
                            [
                                'form' => $form,
                                'question' => $question,
                                'question_index' => $question_index,
                                'current_response_answer_ids' => $current_response_answer_ids,
                                'current_response_answer_value' => $current_response_answer_value,
                                'user_response' => $user_response
                            ]
                        );
                    } else {
                        // Add indication that this question's prereqs were unsatisfied
                        FormUserResponseAnswer::where('form_user_response_id', $user_response->id)
                            ->where('form_question_id', $question->id)
                            ->delete();
                        
                        $answer = $this->createUserResponseAnswer($user_response, $question, null, '', true);

                        // Before skipping question, check to see if we're
                        // coming from the user clicking the "Back" button (not
                        // the browser one)
                        // If so, redirect to the question before this one
                        if (isset($_GET['back'])) {
                            return redirect()->route(
                                'forms.takeQuestion',
                                ['form_id' => $form_id, 'question_index' => $question_index - 1, 'back' => 1]
                            );
                        }

                        // if last question, show review page
                        if ( ($question_index) == count($questions) ) {
                            return redirect()->route(
                                'forms.review',
                                ['response_id' => $user_response->id]
                            );
                        }

                        // Skip question
                        return redirect()->route(
                            'forms.takeQuestion',
                            ['form_id' => $form_id, 'question_index' => $question_index + 1]
                        );
                    }
                } else {
                    $prereq_question = $prereq->child_form_question;
                    $prereq_question_index = $form->questionSortedIndex($prereq_question);

                    // Show unanswered question with message
                    return redirect()->route(
                        'forms.takeQuestion',
                        [
                            'form_id' => $form_id,
                            'question_index' => $prereq_question_index,
                            'error' => 'required'
                        ]
                    );
                }
            }

            return view('user.forms-take-question',
                [
                    'form' => $form,
                    'question' => $question,
                    'question_index' => $question_index,
                    'current_response_answer_ids' => $current_response_answer_ids,
                    'current_response_answer_value' => $current_response_answer_value,
                    'user_response' => $user_response
                ]
            );
        } else {

            return view('user.forms-take-question',
                [
                    'form' => $form,
                    'question' => $question,
                    'question_index' => $question_index,
                    'current_response_answer_ids' => $current_response_answer_ids,
                    'current_response_answer_value' => $current_response_answer_value,
                    'user_response' => $user_response
                ]
            );
        }

    }

    public function submitQuestion(Request $request, $form_id, $question_index) {

        $user_registration = User::getUserRegistration();
        $request_data = $request->all();

        // check form exists and is active
        // check that form is assigned to user
        // check that user hasn't already completed form
        // check that question exists
        // check if user response exists; if not, create

        $form = Form::find($form_id);

        // Check that form is assigned to user
        $assignment = $form->assignments()
            ->where('user_registration_id', $user_registration->id)
            ->first();
        if ( is_null($assignment) ) {
            abort(401, "This form either does not exist or you are not allowed to view it.");
        }

        $questions = $form->questionsSorted();
        $question = $questions[$question_index - 1];

        $user_registration = User::getUserRegistration();
        $user = Auth::user();

        $user_response = $assignment->response;

        if (!is_null($user_response) && $user_response->in_progress == false) {
            return view('error', ['error' => "You have already completed this form."]);
        }

        // Re-show the question if it is required and no answer given
        if ($question->required) {
            $this->validate($request, [
                'answer' => 'required'
            ]);
        }

        if (is_null($user_response)) {
            $user_response = new FormUserResponse;
            $user_response->assignment()->associate($assignment);
            $user_response->in_progress = true;
            $user_response->data_retrieved = false;
            $user_response->save();            
        }

        if ($question->type == 'radio') {

            $user_response_answer = FormUserResponseAnswer::where('form_user_response_id', $user_response->id)
                ->where('form_question_id', $question->id)
                ->first();

            if (is_null($user_response_answer)) {
                $user_response_answer = new FormUserResponseAnswer;
                $user_response_answer->response()->associate($user_response);
                $user_response_answer->question()->associate($question);
            }


            $answer = FormAnswer::find($request_data['answer']);

            if ($answer === null) abort(400, 'This answer is invalid.');

            $user_response_answer->answer()->associate($answer);
            if (isset($request_data['answer_text'])) {
                $user_response_answer->value = $request_data['answer_text'];
            }
            $user_response_answer->prereq_unsatisfied = false;

            $user_response_answer->save();

        } elseif ($question->type == 'checkbox') {
            $response_answers = FormUserResponseAnswer::where('form_user_response_id', $user_response->id)
                ->where('form_question_id', $question->id)
                ->delete();

            if (isset($request_data['answer'])) {
                foreach ($request_data['answer'] as $request_answer) {
                    $answer = FormAnswer::find($request_answer);
                    $answer_value = $request_answer;

                    $response_answer = $this->createUserResponseAnswer($user_response, $question, $answer, $answer_value);
                }
            } else {
                $response_answer = $this->createUserResponseAnswer($user_response, $question, null, '');
            }

        } elseif ($question->type == 'text') {
            if ($question->required) {
                $this->validate($request, [
                    'answer_text' => 'filled'
                ]);
            }

            $user_response_answer = FormUserResponseAnswer::where('form_user_response_id', $user_response->id)
                ->where('form_question_id', $question->id)
                ->first();

            if (is_null($user_response_answer)) {
                $user_response_answer = new FormUserResponseAnswer;
                $user_response_answer->response()->associate($user_response);
                $user_response_answer->question()->associate($question);
            }


            $answer = FormAnswer::find($request_data['answer']);
            $user_response_answer->answer()->associate($answer);
            $user_response_answer->value = $request_data['answer_text'];
            $user_response_answer->prereq_unsatisfied = false;

            $user_response_answer->save();
        }

        // If "Review" btn clicked, go to review page if subsequent questions are satisfied
        if ( isset($request->go_to_review) ) {

            for ($i = $question_index; $i < count($questions); $i++) {

                $question = $questions[$i];

                // Check if question prereqs' status have changed. Display
                // this question if it is now prereq-satisfied and hasn't been
                // already answered
                $prereqs = $question->child_prereqs;
                if (count($prereqs) > 0) {
                    foreach ($prereqs as $prereq) {

                        if ($prereq->isAnswered($user_response)) {
                            if ($prereq->isSatisfied($user_response)) {

                                // Check if this question was already answered
                                $answers = $question->user_response_answers($user_response->id)
                                    ->where('prereq_unsatisfied', false);
                                if ($answers->count()) {
                                    continue;
                                } else {
                                    // show unanswered question
                                    return redirect()->route(
                                        'forms.takeQuestion',
                                        [
                                            'form_id' => $form_id,
                                            'question_index' => $i + 1,
                                            'error' => 'required'
                                        ]
                                    );
                                }

                            } else {

                                // Remove existing answers and replace with "prereq_unsatisfied" answer
                                $user_response->answers()
                                    ->where('form_question_id', $question->id)
                                    ->delete();
                                $this->createUserResponseAnswer($user_response, $question, null, '', true);

                            }

                        } else {
                            // show unanswered question
                            return redirect()->route(
                                'forms.takeQuestion',
                                [
                                    'form_id' => $form_id,
                                    'question_index' => $i + 1,
                                    'error' => 'required'
                                ]
                            );

                        }
                    }
                }

                // Show question if it is required and hasn't been answered
                if ($question->required && $question->user_response_answers($user_response->id)->count() == 0) {
                    // show unanswered question
                    return redirect()->route(
                        'forms.takeQuestion',
                        [
                            'form_id' => $form_id,
                            'question_index' => $i + 1,
                            'error' => 'required'
                        ]
                    );
                }

            }

            return redirect()->route(
                'forms.review',
                ['response_id' => $user_response->id]
            );
        }

        // if last question, show review page
        if ( ($question_index) == count($questions)) {
            return redirect()->route(
                'forms.review',
                ['response_id' => $user_response->id]
            );
        } else {
            return redirect()->route(
                'forms.takeQuestion',
                ['form_id' => $form_id, 'question_index' => $question_index + 1]
            );
        }

    }

    public function review(Request $request, $response_id) {
        $response = FormUserResponse::find($response_id);

        $form = $response->assignment->form;

        $questions = $form->questionsSorted();

        // Make sure any previous required questions have been answered
        foreach ($questions as $i => $question) {

            $is_answered = $response->answers()
                ->where('form_question_id', $question->id)
                ->count() > 0;
            if (! $is_answered) {
                return redirect()->route('forms.takeQuestion',
                    [
                        'form_id' => $form->id,
                        'question_index' => $i+1,
                        'error' => 'required'
                    ]
                );
            }
        }

        $completed_forms = $request->session()->get('completed_forms', []);
        array_push($completed_forms, $form->id);
        $request->session()->put('completed_forms', $completed_forms);

        return view('user.forms-take-review', ['response' => $response]);
    }

    public function createUserResponseAnswer($response, $question, $answer, $value, $prereq_unsatisfied = false) {

        $response_answer = new FormUserResponseAnswer;
        $response_answer->response()->associate($response);
        $response_answer->question()->associate($question);
        $response_answer->answer()->associate($answer);

        $response_answer->value = $value;
        $response_answer->prereq_unsatisfied = $prereq_unsatisfied;

        $response_answer->save();

        return $response_answer;

    }

    public function finish(Request $request) {

        $user_response_id = $request->response;

        $user_response = FormUserResponse::find($user_response_id);
        $user_registration = User::getUserRegistration();
        $user = Auth::user();

        if (is_null($user_response)) {
            abort(403, 'This user response does not exist or you are not allowed to submit it.');
        }

        // Check that response is user's
        if ($user_response->assignment->registration != $user_registration) {
            abort(403, 'This user response does not exist or you are not allowed to submit it.');
        }

        if ($user_response->in_progress == false) {
            return view('error', ['error' => 'This user response has already been submitted.']);
        }

        $user_response->in_progress = false;
        $user_response->submitted_at = Carbon::now();
        $user_response->save();

        $user_response->assignment->complete = true;
        $user_response->assignment->save();

        return view('user.forms-take-complete', ['form' => $user_response->assignment->form]);

    }

    public function publish(Form $form) {
        $form->published = true;
        $form->save();
    }

    public function unpublish(Form $form) {
        $form->published = false;
        $form->save();
    }

    public function archive(Form $form) {
        $form->archived = true;
        $form->save();
    }

    public function unarchive(Form $form) {
        $form->archived = false;
        $form->save();
    }
}
