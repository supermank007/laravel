<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FormOutcomeInterval extends Model
{
    public function form() {
        return $this->belongsTo('App\Form');
    }
}
