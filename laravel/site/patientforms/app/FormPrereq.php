<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\FormUserResponse;

class FormPrereq extends Model
{
    use SoftDeletes;

    public function parent_form_question() {
        return $this->belongsTo('App\FormQuestion');
    }

    public function child_form_question() {
        return $this->belongsTo('App\FormQuestion');
    }

    public function parent_form_answer() {
        return $this->belongsTo('App\FormAnswer');
    }

    public function isAnswered(FormUserResponse $response) {
        $response_answers = $response->answers()
            ->where('form_question_id', $this->parent_form_question->id)
            ->count();

        return $response_answers > 0;
    }

    public function isSatisfied(FormUserResponse $response) {
        $response_answers = $response->answers()
            ->where('form_answer_id', $this->parent_form_answer->id)
            ->count();

        return $response_answers > 0;
    }
}
