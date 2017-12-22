<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Form;
use App\UserRegistration;

class HomeController extends Controller
{
    public function home() {

        $currentUser = Auth::user();
 
        if ($currentUser) {

            $forms = [];

            if ($currentUser->hasRoles('Super-Admin', 'Master-Admin')) {

                $forms = Form::orderBy('created_at')
                    ->take(10)
                    ->get();

            } elseif ($currentUser->hasRoles('Admin')) {

                $forms = Form::where('archived', false)
                    ->where('published', true)
                    ->orderBy('created_at')
                    ->take(10)
                    ->get();

            } elseif ($currentUser->hasRoles('User')) {

                if ( session('registration_number') ) {

                    $currentUserRegistration = UserRegistration::where(
                        'registration_number',
                        session('registration_number')
                        )->first();

                    $form_ids = $currentUserRegistration->form_assignments()->pluck('form_id');
                    $forms = [];
                    foreach ($form_ids as $id) {
                        $form = Form::find($id);
                        if ($form->published)
                            $forms[] = Form::find($id);
                    }
                    $forms = collect($forms);

                } else {

                    return redirect()->route('select_registration');

                }


            }

            return view('index', ['forms' => $forms]);

        } else {

            return redirect()->route('login');

        }

    }
}
