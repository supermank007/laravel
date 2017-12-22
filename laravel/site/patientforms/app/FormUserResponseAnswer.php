<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormUserResponseAnswer extends Model
{
    use SoftDeletes;

    public function response() {
        return $this->belongsTo('App\FormUserResponse', 'form_user_response_id');
    }

    public function question() {
        return $this->belongsTo('App\FormQuestion', 'form_question_id');
    }

    public function answer() {
        return $this->belongsTo('App\FormAnswer', 'form_answer_id');
    }
}
