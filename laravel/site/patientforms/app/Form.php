<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\UserRegistration;
use App\FormPrereq;
use App\FormOutcomeInterval;

class Form extends Model
{
    use SoftDeletes;

    public function program() {
        return $this->belongsTo('App\Program');
    }

    public function creator_user() {
        return $this->belongsTo('App\User');
    }

    public function editor_user() {
        return $this->belongsTo('App\User');
    }

    public function questions() {
        return $this->hasMany('App\FormQuestion');
    }

    public function questionsSorted() {
        $questions = $this->questions->sortBy(function ($question, $key) {
            return implode(' ', [$question->order, $question->id]);
        });

        // Recreate the array so that the keys are in order (without this,
        // referencing the questions array by index would still return the 
        // unsorted question order)
        $questions = $questions->all();
        $ret_questions = [];
        foreach ($questions as $question) {
            $ret_questions[] = $question;
        }
        // Turn it back from an array into a collection
        $ret_questions = collect($ret_questions);

        return $ret_questions;
    }

    // Get index of question in sorted form questions array
    public function questionSortedIndex(FormQuestion $question) {
        $questions = $this->questionsSorted();

        $index = null;
        foreach ($questions as $i => $q) {
            if ($question->id == $q->id) {
                $index = $i;
                break;
            }
        }


        return $index;
    }

    public function assignments() {
        return $this->hasMany('App\FormAssignment');
    }

    public function outcome_intervals() {
        return $this->hasMany('App\FormOutcomeInterval');
    }

    public function isAssignedToUserRegistration($user_registration) {
        if (is_null($user_registration)) return false;
        $assignment = $this->assignments()
            ->where('user_registration_id', $user_registration->id)
            ->first();
        return !is_null($assignment);
    }

    public function completedByUserRegistration(UserRegistration $user_registration) {
        $assignment = $user_registration->form_assignments()->where('form_id', $this->id)->first();

        if (is_null($assignment)) return false;

        if (is_null($assignment->response) || $assignment->response->in_progress) return false;

        return true;
    }

    public function inProgressByUserRegistration($user_registration) {

        if (is_null($user_registration)) return false;

        $assignment = $user_registration->form_assignments()->where('form_id', $this->id)->first();

        if (is_null($assignment)) return false;

        if (is_null($assignment->response) || !$assignment->response->in_progress) return false;

        return true;

    }

    public function isTakeableByUserRegistration($user_registration) {

        $is_takeable =
            ! is_null($user_registration) &&
            $user_registration->user->hasRoles('User') &&
            $this->published &&
            $this->isAssignedToUserRegistration($user_registration) &&
            ! $this->completedByUserRegistration($user_registration);

        return $is_takeable;

    }

    public function isDeletable() {

        $is_deletable = 
            (! $this->published || $this->archived) &&
            ($this->assignments()->where('complete', 0)->count() == 0);
        return $is_deletable;

    }

    public function serialize() {

        $data = [
            'id' => $this->id,
            'published' => $this->published,
            'name' => $this->name,
            "description" => $this->description,
            "instructions" => $this->instructions,
            "show_instructions" => $this->show_instructions,
            "program_id" => $this->program->id,
            "outcome_form" => $this->outcome_form,
            "questions" => [],
            "prereqs" => []
        ];

        $question_ids = [];

        foreach ($this->questions as $question) {
            $question_data = [
                "id" =>$question->id,
                "order" => $question->order,
                "label" => $question->label,
                "required" => $question->required,
                "type" => $question->type,
                "answers" => [],
                "new" => false
            ];

            $question_ids[] = $question->id;

            foreach ($question->answers as $answer) {
                $answer_data = [
                    "id" => $answer->id,
                    "order" => $answer->order,
                    "label" => $answer->label,
                    "type" => $answer->type,
                    "new" => false
                ];

                $question_data['answers'][] = $answer_data;
            }

            $data['questions'][] = $question_data;
        }

        $prereqs = FormPrereq::whereIn('parent_form_question_id', $question_ids)
            ->whereIn('child_form_question_id', $question_ids, 'or')
            ->get();


        foreach ($prereqs as $prereq) {
            $prereq_data = [
                'id' => $prereq->id,
                'parent_question_id' => $prereq->parent_form_question_id,
                'child_question_id' => $prereq->child_form_question_id,
                'answer' => $prereq->parent_form_answer_id,
                'new' => false
            ];

            $data['prereqs'][] = $prereq_data;
        }

        $data['outcome_intervals'] = [];
        $outcome_intervals = $this->outcome_intervals;
        foreach ($outcome_intervals as $outcome_interval) {
            $data['outcome_intervals'][] = [
                'value' => $outcome_interval->interval
            ];
        }

        return $data;
    }
}
