<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Program extends Model
{
    use SoftDeletes;

    public function users() {
        return $this->hasMany('App\User');
    }

    public function user_registrations() {
        return $this->hasMany('App\UserRegistration');
    }

    public function forms() {
        return $this->hasMany('App\Form');
    }
}
