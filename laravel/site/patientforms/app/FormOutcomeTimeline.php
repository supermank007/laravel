<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FormOutcomeTimeline extends Model
{
    public function form() {
        return $this->belongsTo('App\Form');
    }

    public function dates() {
        return $this->hasMany('App\FormOutcomeDate');
    }
}
