<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'HomeController@home')->name('index');
Route::get('/home', 'HomeController@home');

Route::get('/login', 'Auth\LoginController@login')->name('login');
Route::get('/logout', 'Auth\LoginController@logout')->name('logout');
Route::post('/login', 'Auth\LoginController@authenticate');

Route::get('/claim', function() {
    return view('registrations.claim');
})->name('claim');

Route::post('/claim', 'UserRegistrationController@claim');

Route::post('/confirm-claim', 'UserRegistrationController@confirmClaim');

Route::get('/access-denied', function() {
    return view('access-denied');
})->name('access_denied');

Route::get('/error', function() {
    return view('error');
})->name('error');

// Password Reset Routes...
Route::get('/password/email', 'Auth\ForgotPasswordController@showLinkRequestForm');
$this->post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail');
$this->get('password/reset/{token?}', 'Auth\ResetPasswordController@showResetForm');
$this->post('password/reset', 'Auth\ResetPasswordController@reset');

Route::group(['middleware' => 'auth:web'], function() {

    // User is logged in

    Route::get('/forms', 'FormController@index')->name('forms.index');

    Route::get('/select_registration', 'UserRegistrationController@showRegistrations')->name('select_registration');
    Route::post('/select_registration', 'UserRegistrationController@selectRegistration')->name('select_registration');

    Route::get('/user/account/{user}', 'UserController@show')->name('users.account');
    Route::post('/user/account/{user}', 'UserController@update');

    // User-only routes
    Route::group(['middleware' => 'role:User'], function() {

        Route::group(['middleware' => 'user_registration'], function() {

            Route::get('/forms/take/{form_id}', 'FormController@take')
                ->name('forms.take');
            Route::get('/forms/instructions/{form_id}', 'FormController@showInstructions')
                ->name('forms.instructions');
            Route::get('/forms/resume/{form_id}', 'FormController@resume')->name('forms.resume');
            Route::get('/forms/take/{form_id}/question/{question_index}', 'FormController@takeQuestion')
                ->name('forms.takeQuestion');
            Route::post('/forms/take/{form_id}/question/{question_index}', 'FormController@submitQuestion');

            Route::post('/forms/submit', 'FormController@submit');
            Route::post('/forms/finish', 'FormController@finish')->name('forms.finish');

            Route::get('/forms/review/{response_id?}', 'FormController@review')
                ->name('forms.review');

        });

    });

    // Admin-only routes
    Route::group(['middleware' => 'role:Admin'], function() {

        Route::get('/forms/assign', 'FormController@assign')
            ->name('forms.assign');
        Route::post('/forms/assign/{form}', 'FormAssignmentController@store');
        Route::post('/forms/unassign/{form}', 'FormAssignmentController@unassign');

        Route::post('/assignments/search', 'FormAssignmentController@searchByRegistrationNumber')
            ->name('assignments.search');

    });

    // Super-admin-only routes
    Route::group(['middleware' => 'role:Super-Admin,Master-Admin'], function() {

        Route::resource('/users', 'UserController');
        Route::resource('/forms', 'FormController', ['except' => ['index', 'show']]);
        Route::put('/forms', 'FormController@update');
        Route::resource('/programs', 'ProgramController', ['except' => ['show']]);

        Route::get('/programs/delete-modal/{program}', 'ProgramController@deleteModal');

        Route::get('/forms/publish/{form}', 'FormController@publish');
        Route::get('/forms/unpublish/{form}', 'FormController@unpublish');
        Route::get('/forms/archive/{form}', 'FormController@archive')->name('forms.archive');
        Route::get('/forms/unarchive/{form}', 'FormController@unarchive')->name('forms.archive');
        Route::get('/forms/duplicate/{form}', 'FormController@duplicate')->name('forms.duplicate');

        Route::get('/users/delete-modal/{user}', 'UserController@deleteModal');
        Route::get('/user/activate/{user}', 'UserController@activate');
        Route::get('/user/deactivate/{user}', 'UserController@deactivate');

    });

    // Master-admin-only routes
    Route::group(['middleware' => 'role:Master-Admin'], function() {

        Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');

    });

});
