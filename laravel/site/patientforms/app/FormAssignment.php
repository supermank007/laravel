<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormAssignment extends Model
{
    use SoftDeletes;

    public function registration() {
        return $this->belongsTo('App\UserRegistration', 'user_registration_id');
    }

    public function assigner_user() {
        return $this->belongsTo('App\User');
    }

    public function form() {
        return $this->belongsTo('App\Form');
    }

    public function response() {
        return $this->hasOne('App\FormUserResponse');
    }
}
