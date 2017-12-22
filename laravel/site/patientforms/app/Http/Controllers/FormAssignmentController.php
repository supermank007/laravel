<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Form;
use App\FormAssignment;
use App\UserRegistration;
use App\FormUserResponse;

class FormAssignmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Form $form)
    {
        if ($form->published && !$form->archived) {
            $assignment = new FormAssignment;
            $registration = UserRegistration::where('registration_number', $request->registration_number)->first();
            $assignment->registration()->associate($registration);
            $assignment->form()->associate($form);
            $user = Auth::user();
            $assignment->assigner_user()->associate($user);
            $assignment->save();

            return redirect()->route('forms.index');
        } else {
            abort(403, 'This form either does not exist or is not available to assign.');
        }
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
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function unassign(Request $request, Form $form) {
        if ($form->published && !$form->archived) {
            $registration = UserRegistration::where('registration_number', $request->registration_number)
                ->first();
            $assignment = FormAssignment::where('user_registration_id', $registration->id)
                ->where('form_id', $form->id)
                ->first();

            $is_deletable = $assignment->complete === 0 &&
                ( $assignment->response === null ||
                  $assignment->response->in_progress === 0 );

            if ($is_deletable) {
                $assignment->delete();
            } else {
                abort(403, 'This form either does not exist or is not available to unassign.');
            }

            return redirect()->route('forms.assign');
        } else {
            abort(403, 'This form either does not exist or is not available to unassign.');
        }
    }

    public function searchByRegistrationNumber(Request $request) {

        $ret_data = [
            'assignments' => [],
            'registration_found' => false,
            'program_id' => null,
        ];

        $registration_number = $request->registration_number;

        $registration = UserRegistration::where('registration_number', $registration_number)
            ->first();

        if (is_null($registration)) return $ret_data;

        $user = $registration->user;
        $ret_data['registration_found'] = true;
        $ret_data['program_id'] = $registration->program_id;


        foreach ($registration->form_assignments as $assignment) {
            $form_name = $assignment->form->name;
            $form_id = $assignment->form->id;

            $response = $assignment->response;

            if (is_null($response)) {
                $status = 'Assigned';
            } elseif ($response->in_progress) {
                $status = 'In Progress';
            } elseif (!$response->in_progress) {
                $status = 'Completed';
            }

            $assignment_data = [
                'form_name' => $form_name,
                'form_id' => $form_id,
                'status' => $status
            ];

            $ret_data['assignments'][] = $assignment_data;
        }

        return $ret_data;

    }
}
