<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FormPrereqAnswer extends Model
{
    public function prereq() {
        return $this->belongsTo('App\FormPrereq', 'form_prereq_id');
    }

    public function answer() {
        return $this->belongsTo('App\FormAnswer', 'form_answer_id');
    }
}
