<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FormOutcomeDate extends Model
{
    public function timeline() {
        return $this->belongsTo('App\FormOutcomeTimeline');
    }
}
