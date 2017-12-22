@extends('layouts.app-no-sidebar')

@section('title', 'Create Form')

@section('sidebar')
    @parent

    <p>Sidebar content here.</p>
@endsection

@section('content')
    <div id='app'>
        <h1>Create/Edit Form</h1>
        <hr>
        <div class="alert alert-danger" id="form-errors"
            v-show="editFormPage.errors.inputs_with_errors.length > 0 ||
                  editFormPage.errors.questions_with_errors.length > 0">
                <p v-for="error in editFormPage.errors.inputs_with_errors">
                    @{{ error.field }}: @{{ error.error }}
                </p>
                <p v-for="error in editFormPage.errors.questions_with_errors">
                    @{{ error.question }}: @{{ error.error }}
                </p>
        </div>
        <!-- <form method="GET" action="" id="create-form"> -->
        <form method="{{ $form_method }}" action="/forms" @submit.prevent="submitForm" id="create-form">
            <div class="form-group">
                <label class="control-label" for="form-name">Form Name:</label>
                <input name="form-name" class="form-control" type='text' v-model='form.name' placeholder='Form Name' required @keydown.enter.prevent=''>
            </div>
            <div class="form-group">
                <label class="control-label" for="form-description">Form Description:</label>
                <textarea name="form-description" class="form-control" v-model="form.description" placeholder="Form Description"></textarea>
            </div>
            <div class="form-group">
                <label class="control-label" for="form-instructions">Form Instructions:</label>
                <textarea name="form-instructions" class="form-control" v-model="form.instructions" placeholder="Form Instructions"></textarea>
            </div>
            <div class="form-group">
                <label class="control-label">
                    Show form instructions page:
                    <input name="form-show-instructions" type='checkbox' v-model='form.show_instructions'>
                </label>
            </div>
            <div class="form-group">
                <label class="control-label" for="form-program">Program:</label>
                <select name="form-program" class="form-control" v-model="form.program_id" required>
                        <option value='' selected>Please Select A Program</option>
                    @foreach ($programs as $program)
                        <option value='{{ $program->id }}' >{{ $program->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="control-label">
                    Outcome form:
                    <input name="form-outcome-form" type='checkbox' v-model='form.outcome_form'>
                </label>
            </div>
            <div v-if="form.outcome_form">
                <outcome-timeline :intervals='form.outcome_intervals'></outcome-timeline>
            </div>
            <hr>
            <h3>Questions:</h3>
            <div class="form-questions">
                <div class="form-question" v-for="(question, question_index) in questionsSorted"  :data-question-index='question_index' :data-question-id='question.id'>
                    <div class="form-question-info">
                        <div class="row">
                            <div class="form-question-label col-md-7">
                                <span class="form-question-order-controls">
                                    <a href='#' class="form-question-order-up" data-toggle="tooltip" title='Move up' v-if='question_index != 0' @click.prevent='questionOrderUp'>
                                        <span class="glyphicon glyphicon-chevron-up"></span>
                                    </a>
                                    <a href='#' class="form-question-order-down" data-toggle="tooltip" title='Move down' v-if='question_index != form.questions.length - 1' @click.prevent='questionOrderDown'>
                                        <span class="glyphicon glyphicon-chevron-down"></span>
                                    </a>
                                </span>
                                @{{ question_index + 1 }}. <input name='form-question-label' class="form-question-label-input form-control" type='text' v-model='question.label' placeholder="Answer Label" @keydown.enter.prevent='' @keydown.tab.prevent='newQuestion'>
                            </div>
                            <div class="form-question-actions col-md-5 text-right">
                                <label class="form-question-required-label">
                                    Required: <input type="checkbox" name="form-question-required" class="form-question-required" v-model="question.required">
                                </label>
                                <select class="form-control form-question-type" name="form-question-type" v-model='question.type' @change='questionTypeChanged'>
                                    <option value="radio">Radio</option>
                                    <option value="checkbox">Checkbox</option>
                                    <option value="text">Text</option>
                                </select>
                                <span class="dropdown form-question-actions-dropdown" v-if='question_index != 0'>
                                    <button class="btn btn-default dropdown-toggle" type="button" :id="'dropdownMenu' + question_index" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                        <span class="glyphicon glyphicon-option-horizontal"></span>
                                    </button>
                                    <ul class="dropdown-menu" :aria-labelledby="'dropdownMenu' + question_index">
                                        <li><a class="form-question-edit-prereqs" href="#" @click.prevent='editQuestionPrereqs'>Edit Prerequisites</a></li>
                                    </ul>
                                </span>
                                <button class="btn btn-danger" @click.prevent='removeQuestion'>
                                    <span class="glyphicon glyphicon-remove"></span>
                                </button>
                            </div>
                        </div>
    
                    </div>
                    <div class="form-question-answers-header" v-if='question.type == "radio" || question.type == "checkbox"'>
                        Answers
                        <button class="btn btn-default btn-xs pull-right" @click.prevent='toggleAnswers'>
                            <span class="glyphicon glyphicon-minus"></span>
                        </button>
                    </div>
    
                    <div class="form-question-answers" v-if='question.type == "radio" || question.type == "checkbox"'>
                        <div class="form-question-answer container-fluid" v-for="(answer, answer_index) in question.answers" :data-answer-index='answer_index' :data-answer-id='answer.id'>
                            <div class="row">
                                <div class="col-md-9">
                                    <input name='form-question-answer-label' class="form-question-answer-label-input form-control input-sm" type='text' v-model='answer.label' placeholder="Answer Label" @keydown.enter.prevent='' @keydown.tab.prevent='newAnswer'>
                                </div>
                                <div class="col-md-2">
                                    <select class="form-control input-sm" name='form-question-answer-type' v-model='answer.type'>
                                        <option value='radio' v-if='question.type == "radio"'>Radio</option>
                                        <option value='radio_text' v-if='question.type == "radio"'>Radio Text</option>
                                        <option value='checkbox' v-if='question.type == "checkbox"'>Checkbox</option>
                                        <option value='text' v-if='question.type == "text"'>Text</option>
                                    </select>
                                </div>
                                <div class="col-md-1">
                                    <span class="form-question-answer-actions pull-right">
                                        <button class="btn btn-danger btn-sm" @click.prevent='removeAnswer'>
                                            <span class="glyphicon glyphicon-remove"></span>
                                        </button>
                                    </span>
                                </div>
                            </div>
                        </div>  <!-- .form-answer -->
                        <div class="form-question-new-answer" v-if='question.type == "radio" || question.type == "checkbox"'>
                            <button class="btn btn-default form-question-new-answer-btn" @click.prevent='newAnswer'>
                                <span class="glyphicon glyphicon-plus-sign"></span>
                                New Answer
                            </button>
                        </div>
                    </div>
    
                </div> <!-- .form-question -->
    
                <div class="form-new-question">
                    <button class="btn btn-primary form-new-question-btn" @click.prevent='newQuestion'>
                        <span class="glyphicon glyphicon-plus-sign"></span>
                        New Question
                    </button>
                </div>
    
            </div>
            <hr>
            <button type='submit' id="formSaveBtn" class="btn btn-lg btn-default form-submit-btn" data-loading-text="Saving..." data-default-text="Save" @click='makeBtnLoading'>Save</button>
            <button type='submit' id="formPublishBtn" class="btn btn-lg btn-primary form-submit-btn" data-loading-text="Publishing..." data-default-text="Publish" @click='editFormPublish'>Publish</button>
        </form>
    
        @include('forms.edit-prereqs-modal')
    </div>

    <!-- Vue templates -->
    <template id='outcome-timeline'>
        <div class="outcome-timeline-editor">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4>Outcome Timeline</h4>
                </div>
                <div class="panel-body">
                    <div class="outcome-interval" v-for='(interval, index) in intervals' :data-interval-index='index'>
                        <div class="row">
                            <div class="col-md-2">
                                <label :for="'outcome-interval-' + index">Interval @{{ index + 1}}:</label>
                            </div>
                            <div class="col-md-9">
                                <input class="form-control outcome-interval-input" type="text" :name="'outcome-interval' + index" :id="'outcome-interval' + index" v-model="interval.value" @keydown.enter.prevent=''>
                            </div>
                            <div class="col-md-1">
                                <button class="btn btn-danger" @click.prevent='removeOutcomeInterval'>
                                    <span class="glyphicon glyphicon-remove"></span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="new-outcome-interval">
                        <div class="row">
                            <div class="col-md-2">
                                <label for="new-outcome-interval">Interval @{{ intervals.length + 1}}:</label>
                            </div>
                            <div class="col-md-9">
                                <input class="form-control" type="text" placeholder='New interval' name='new-outcome-interval' @focus='newOutcomeInterval' @keydown.enter.prevent=''>
                            </div>
                            <div class="col-md-1">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>
    
    @if (isset($form))
        <script>
            if (typeof(vue_data) === 'undefined') {
                vue_data = {};
            }
            vue_data.form = {!! json_encode($form->serialize()) !!};
        </script>
    @endif
@endsection