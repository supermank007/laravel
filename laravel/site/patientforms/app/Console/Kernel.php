<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

use App\FormOutcomeInterval;
use App\UserRegistration;
use App\FormAssignment;

use Mail;
use Carbon\Carbon;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            // Auto-assign Outcome forms and send emails
            $outcome_intervals = \App\FormOutcomeInterval::all();

            foreach ($outcome_intervals as $outcome_interval) {
                $form = $outcome_interval->form;

                if (! $form->published) continue;

                // Get all users who have discharge dates <interval> days before now
                $now = Carbon::now();
                $discharge_date = $now->subDays($outcome_interval->interval);
                $user_registrations = UserRegistration::whereDate('discharged_at', '=', $discharge_date->toDateString())->get();
                foreach ($user_registrations as $user_registration) {
                    $user = $user_registration->user;
                    // Check if user is already assigned/completed this form
                    $assignment = $user_registration->form_assignments()->where('form_id', $form->id)->first();

                    if (is_null($assignment)) {
                        // Auto-assign form and send email
                        $assignment = new FormAssignment;
                        $assignment->registration()->associate($user_registration);
                        $assignment->form()->associate($form);
                        $assignment->save();
                        Mail::send(
                            'emails.outcome_reminder',
                            [
                                'user' => $user,
                                'registration' => $user_registration,
                                'form' => $form
                            ],
                            function ($m) use ($user) {
                                $m->to($user->email, $user->fullName())->subject('Patient Forms: You have a new Outcome Form available!');
                            }
                        );

                    } else {

                        // Send reminder email if user hasn't completed form
                        if (is_null($assignment->response) || $assignment->response->in_progress) {
                            Mail::send(
                                'emails.outcome_reminder',
                                [
                                    'user' => $user,
                                    'registration' => $user_registration,
                                    'form' => $form
                                ],
                                function ($m) use ($user) {
                                    $m->to($user->email, $user->fullName())->subject('Patient Forms: You have a new Outcome Form available!');
                                }
                            );
                        }
                    }
                }
            }

        })->dailyAt(env('OUTCOME_DAILY_EMAIL_TIME', '9:00'));
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
