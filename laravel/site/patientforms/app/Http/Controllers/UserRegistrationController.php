<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Mail;

use App\User;
use App\Role;
use App\Program;
use App\UserRegistration;
use Validator;
use View;

class UserRegistrationController extends Controller
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
    public function store(Request $request)
    {
        $program = Program::find($request->program_id);

        $user = User::where('email', $request->email)->first();
        if (is_null($user)) {
            $user = new User;
            $user->id = \Webpatser\Uuid\Uuid::generate(4);
            $user->email = $request->email;
            $user->program()->associate($program);
            $user->save();
            $role_user = Role::where('role', 'User')->first();
            $user->roles()->attach($role_user);
        }

        $registration = new UserRegistration();

        $registration->id = \Webpatser\Uuid\Uuid::generate(4);
        $registration->registration_number = $request->registration_number;
        $registration->program()->associate($program);
        $registration->user()->associate($user);

        $registration->save();

        Mail::send(
            'emails.registration_confirmation',
            [
                'email' => $user->email,
                'registration_number' => $registration->registration_number
            ],
            function ($m) use ($user) {

                $from_address   = env('MAIL_FROM_ADDRESS');
                $from_name      = env('MAIL_FROM_NAME');
                $subject_line   = 'Patient Forms: You have been registered';

                $m->to($user->email)
                    ->from($from_address, $from_name)
                    ->subject($subject_line);
            }
        );

        return ["success" => true, "user_registration_id" => $registration->id->string];
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

    public function showRegistrations() {

        $user = Auth::user();

        $registrations = $user->registrations;

        foreach ($registrations as $registration) {
            $assignments = $registration->form_assignments;
            $num_unfinished_forms = 0;
            foreach ($assignments as $assignment) {
                $response = $assignment->response;
                if ( is_null($response) || $response->in_progress ) {
                    $num_unfinished_forms += 1;
                }
            }
            $registration->num_unfinished_forms = $num_unfinished_forms;
        }

        return view('user.select-registration', ['registrations' => $registrations]);
    }

    public function selectRegistration(Request $request) {

        $registration = UserRegistration::where('registration_number', $request->registration_number)
            ->first();
        if ($registration === null) {
            abort(404, "Registration with given registration number not found.");
        }

        session(['registration_number' => $request->registration_number]);
        return redirect()->intended(route('index'));

    }

    protected function _checkClaim($registration_number, $email) {
        $user = User::where('email', $email)->first();
        if (is_null($user)) {
            return false;
        }

        $registration = UserRegistration::where('registration_number', $registration_number)->first();
        if (is_null($registration)) {
            return false;
        }

        return true;
    }

    public function claim(Request $request) {

        $validator = Validator::make($request->all(), [
            'registration_number' => 'required',
            'email' => 'required',
        ]);

        $validator->after(function ($validator) use($request) {

            if (! $this->_checkClaim($request->registration_number, $request->email)) {
                $validator->errors()->add('email', 'Email or Registration Number not found.');
            }

        });

        if ($validator->fails()) {
            return View::make('registrations.claim')->withErrors($validator);
        } else {
            return view('user.registration-set-password',
                [
                    'registration_number' => $request->registration_number,
                    'email' => $request->email
                ]
            );
        }

    }

    public function confirmClaim(Request $request) {

        $validator = Validator::make($request->all(), [
            'password' => 'required|same:password_confirm|max:255'
        ]);

         $validator->after(function ($validator) use($request) {

            if (! $this->_checkClaim($request->registration_number, $request->email)) {
                $validator->errors()->add('email', 'Email or Registration Number not found.');
            }

        });

        if ($validator->fails()) {
            return View::make('user.registration-set-password',
                [
                    'registration_number' => $request->registration_number,
                    'email' => $request->email
                ])->withErrors($validator);
        }

        $user = User::where('email', $request->email)->first();
        $user->password = bcrypt($request->password);
        $user->save();

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password, 'active' => 1])) {
            // Authentication passed...
            session(['registration_number' => $request->registration_number]);
            return redirect()->route('index');
        } else {
            return view('auth/login', ['errors' => [ 'Login failed' ]]);
        }

    }

    public function api_dischargeDate(Request $request, $registration_number) {

        $registration = UserRegistration::where('registration_number', $registration_number)
            ->first();

        if (empty($registration)) {
            return response()->json(
                [
                    "success" => false,
                    "message" => "No registration found with the given registration number"
                ],
                404
            );
        }

        $registration->discharged_at = $request->discharge_date;
        $registration->save();

        return [
            "success" => true,
            "message" => "Discharge date successfully set"
        ];

    }
}
