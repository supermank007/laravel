<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormAnswer extends Model
{
    use SoftDeletes;

    public function question() {
        return $this->belongsTo('App\FormQuestion', 'form_question_id');
    }

    public function prereq_answers() {
        return $this->hasMany('App\FormPrereqAnswer');
    }

    public function user_responses() {
        return $this->hasMany('App\FormUserResponse');
    }
}
