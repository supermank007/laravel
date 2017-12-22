<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserRegistration extends Model
{
    use SoftDeletes;

    public $primaryKey = 'id';

    public $incrementing = false;

    public function program() {
        return $this->belongsTo('App\Program');
    }

    public function user() {
        return $this->belongsTo('App\User');
    }

    public function form_assignments() {
        return $this->hasMany('App\FormAssignment');
    }
    
}
