<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\FormUserResponse;
use App\UserRegistration;

use App\Exceptions\APIException;

class FormUserResponseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $responses = FormUserResponse::all();
        return view('admin.response-index', ['responses' => $responses]);
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
    public function store(Request $request)
    {
        //
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

    public function api_fetchResponseData(Request $request, $registration_number = null) {
        $fetch_all = isset($request->fetch_all) ? $request->fetch_all == "true" : false;
        $ret_data = ['user_responses' => []];

        if (!is_null($registration_number)) {

            // Fetch responses from a specific registration

            $registration = UserRegistration::where('registration_number', $registration_number)
                ->first();
            if (is_null($registration)) {
                throw new APIException("Given user registration number not found");
            }
            $assignment_ids = $registration->form_assignments->pluck('id');

            $responses = FormUserResponse::whereIn('form_assignment_id', $assignment_ids);

        } else {

            // Fetch responses from all registrations
            $responses = FormUserResponse::where('in_progress', false);

        }
        
        if (!$fetch_all) $responses = $responses->where('data_retrieved', false);
        $responses = $responses->get();

        foreach ($responses as $response) {
            $response_data = [
                'response_id' => $response->id,
                'registration_number' => $response->assignment->registration->registration_number,
                'form_id' => $response->assignment->form->id,
                'date_submitted' => $response->submitted_at,
                'questions' => []
            ];

            $questions_data = [];
            foreach ($response->answers as $response_answer) {
                if ( isset( $questions_data[$response_answer->question->id] ) ) {
                    $question = $questions_data[ $response_answer->question->id ];
                } else {
                    $question = [
                        'label' => $response_answer->question->label,
                        'answers' => []
                    ];
                    $questions_data[ $response_answer->question->id ] = $question;
                }

                $form_answer        = $response_answer->answer;
                $form_answer_id     = (is_null($form_answer)) ? null : $form_answer->id;
                $form_answer_label  = (is_null($form_answer)) ? null : $form_answer->label;
                $skipped            = $response_answer->prereq_unsatisfied;

                $questions_data[ $response_answer->question->id ]['answers'][] = [
                    'answer_id'     => $form_answer_id,
                    'label'         => $form_answer_label,
                    'value'         => $response_answer->value,
                    'skipped'       => $skipped
                ];
            }

            $questions_data_list = []; // Convert questions data from array to list
            foreach ($questions_data as $id => $data) {
                $data['question_id'] = $id;
                $questions_data_list[] = $data;
            }

            $response_data['questions'] = $questions_data_list;

            $ret_data['user_responses'][] = $response_data;

            $response->data_retrieved = true;
            $response->save();
        }

        return $ret_data;
    }

    public function api_fetchRegistrationResponseData(Request $request, $registration_number) {
        return $this->api_fetchResponseData($request, $registration_number);
    }
}
