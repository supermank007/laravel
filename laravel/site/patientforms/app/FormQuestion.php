<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormQuestion extends Model
{
    use SoftDeletes;

    public function form() {
        return $this->belongsTo('App\Form');
    }

    public function answers() {
        return $this->hasMany('App\FormAnswer');
    }

    public function response_answers() {
        return $this->hasMany('App\FormUserResponseAnswer');
    }

    public function child_prereqs() {
        return $this->hasMany('App\FormPrereq', 'child_form_question_id');
    }

    public function parent_prereqs() {
        return $this->hasMany('App\FormPrereq', 'parent_form_question_id');
    }

     public function user_response_answers($response_id) {
         $response_answers = FormUserResponse::find($response_id)
             ->answers()
             ->where('form_question_id', $this->id)
             ->get();
         return $response_answers;
     }

    public function response_answer_values($response_id) {
        $response_answers = $this->user_response_answers($response_id);

        $response_answer_values = [];

        foreach ($response_answers as $response_answer) {

            $form_answer = $response_answer->answer;

            if (!is_null($form_answer)) {
                
                if ($form_answer->type == 'radio'
                    || $form_answer->type == 'checkbox') {

                    $response_answer_values[] = $form_answer->label;

                } elseif ($form_answer->type == 'radio_text') {

                    $response_answer_values[] = "{$form_answer->label}: \"{$response_answer->value}\"";

                } elseif ($form_answer->type == 'text') {

                    $response_answer_values[] = $response_answer->value;

                } else {

                    $response_answer_values[] = null;

                }
                
            }


        }

        return $response_answer_values;

    }

    public function wasSkippedInResponse( FormUserResponse $response) {
        return $this->response_answers()
            ->where('form_user_response_id', $response->id)
            ->where('prereq_unsatisfied', true)
            ->count() != 0;
    }
}
