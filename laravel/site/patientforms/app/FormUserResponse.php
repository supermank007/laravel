<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormUserResponse extends Model
{
    use SoftDeletes;

    public function assignment() {
        return $this->belongsTo('App\FormAssignment', 'form_assignment_id');
    }

    public function answers() {
        return $this->hasMany('App\FormUserResponseAnswer');
    }
}
