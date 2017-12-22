@extends('layouts.app-no-sidebar')

@section('title', 'Create Form')

@section('sidebar')
    @parent

    <p>Sidebar content here.</p>
@endsection

@section('content')
    <div id='app'>
        <h1>Create Form</h1>
        <hr>
        <!-- <form method="GET" action="" id="create-form"> -->
        <form method="POST" action="/forms" @submit.prevent="submitForm" id="create-form">
            <div class="form-group">
                <label for="form-name">Form Name:</label>
                <input name="form-name" class="form-control" type='text' v-model='form.name' placeholder='Form Name' required>
            </div>
            <div class="form-group">
                <label for="form-description">Form Description:</label>
                <textarea name="form-description" class="form-control" v-model="form.description" placeholder="Form Description"></textarea>
            </div>
            <div class="form-group">
                <label for="form-program">Program:</label>
                <select name="form-program" class="form-control" v-model="form.program_id" required>
                        <option value='' selected>Please Select A Program</option>
                    @foreach ($programs as $program)
                        <option value='{{ $program->id }}' >{{ $program->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>
                    Outcome form:
                    <input name="form-outcome-form" type='checkbox' v-model='form.outcome_form'>
                </label>
            </div>
            <hr>
            <h3>Questions:</h3>
            <div class="form-questions">
                <div class="form-question" v-for="(question, question_index) in form.questions"  :data-question-index='question_index' :data-question-id='question.id'>
                    <div class="form-question-info">
                        <div class="row">
                            <div class="form-question-label col-md-7">
                                @{{ question_index + 1 }}. <input name='form-question-label' class="form-control" type='text' v-model='question.label' placeholder="Answer Label">
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
                                    <input name='form-question-answer-label' class="form-control input-sm" type='text' v-model='answer.label' placeholder="Answer Label">
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
        <!--<input type='submit' class="btn btn-lg btn-primary" value='Save' >-->
        <button type='submit' id="createFormSubmitBtn" class="btn btn-lg btn-primary" data-loading-text="Saving..." data-default-text="Save" @click='makeBtnLoading'>Save</button>
        </form>
    
        @include('forms.edit-prereqs-modal')
    </div>
@endsection