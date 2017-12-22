
/**
 * First we will load all of this project's JavaScript dependencies which
 * include Vue and Vue Resource. This gives a great starting point for
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('example', require('./components/Example.vue'));

Vue.component(
    'passport-clients',
    require('./components/passport/Clients.vue')
);

Vue.component(
    'passport-authorized-clients',
    require('./components/passport/AuthorizedClients.vue')
);

Vue.component(
    'passport-personal-access-tokens',
    require('./components/passport/PersonalAccessTokens.vue')
);

Vue.component(
    'form-assignment-listing',
    {
        template:
            '#form-assignment-listing',
        props: [
            'assignments',
            'registration_found',
            'form_already_assigned',
            'form_program_matches',
        ],
        methods: {
            formAssign: function (event) {
                this.$parent.$options.methods.formAssign
                    .call(this.$parent, event);
            },

            formUnassign: function (event) {
                this.$parent.$options.methods.formUnassign
                    .call(this.$parent, event);
            },
        },
    }
);

var outcomeTimeline = {
    template:
        '#outcome-timeline',
    props: ['intervals'],
    methods: {
        newOutcomeInterval: function (event) {

            var $target = $(event.target);

            this.$parent.form.outcome_intervals.push(
                {value: null}
            );

            Vue.nextTick(function() {
                $(".outcome-interval-input").last().focus();
            });
        },
        removeOutcomeInterval: function (event) {

            var $target = $(event.target);
            var interval_index = +$target.closest('.outcome-interval').attr('data-interval-index');

            this.$parent.form.outcome_intervals.splice(interval_index, 1);

        }
    }
};

// NOTE: Maybe need to put this into it's own module or JS config file and import
if (typeof(vue_data) === 'undefined') {
    vue_data = {
        form: {
            id: null,
            published: false,
            name: "",
            description: "",
            instructions: "",
            show_instructions: false,
            program_id: 0,
            outcome_form: false,
            questions: [],
            prereqs: [],
            outcome_intervals: [
            ]
        }
    };
}

vue_data = $.extend(vue_data, {
    editPrereqsModal: {
        chosenChildQuestionId: null,
        chosenChildQuestionIndex: null,
        chosenParentQuestion: null,
        chosenParentAnswer: null,
        page: 1,
        // Page constants
        INITIAL_PAGE: 1,
        ADD_NEW_PREREQ_PAGE: 2,
        CHOOSE_PREREQ_ANSWER_PAGE: 3,
        PREREQ_ADDED_PAGE: 4,
    },
    archiveFormModal: {
        chosenFormId: null,
    },
    editFormPage: {
        errors: {
            inputs_with_errors: [],
            questions_with_errors: []
        },
        MAX_FORM_QUESTIONS: 500,
        MAX_FORM_ANSWERS_PER_QUESTION: 500,
        MAX_FORM_PREREQS_PER_QUESTION: 100,
        MAX_FORM_OUTCOME_DATES: 100,
    },
    assignFormPage: {
        assignments: [
        ],
        selected_form_id: -1,
        registration_searched: false,
        registration_found: false,
        form_already_assigned: false,
        form_program_matches: true,
    },
    deleteFormModal: {
        selected_form_id: -1,
    },
});

app = new Vue({
    el: '#app',
    data: vue_data,
    methods: {

        // isEmpty: function (obj) {
        //     // Helper function to determine if object
        //     // is empty (has no properties of its own)

        //     // null and undefined are "empty"
        //     if (obj == null) return true;

        //     // Assume if it has a length property with a non-zero value
        //     // that that property is correct.
        //     if (obj.length > 0)    return false;
        //     if (obj.length === 0)  return true;

        //     // If it isn't an object at this point
        //     // it is empty
        //     if (typeof obj !== "object") return true;

        //     // Otherwise, does it have any properties of its own?
        //     // Note that this doesn't handle
        //     // toString and valueOf enumeration bugs in IE < 9
        //     for (var key in obj) {
        //         if (hasOwnProperty.call(obj, key)) return false;
        //     }

        //     return true;
        // },

        editFormIsValid: function (errors) {

            var errors = errors || this.getEditFormErrors();
            if (errors.inputs_with_errors.length > 0 ||
                errors.questions_with_errors.length > 0) {
                return false;
            }
            return true;

        },

        getEditFormErrors: function (event) {

            var errors = {
                inputs_with_errors: [
                ],
                questions_with_errors: [
                ],
            };

            if (this.form.name === '')
                errors.inputs_with_errors.push(
                    {
                        field: 'Form Name',
                        error: "Field is required"
                    }
                );
            if (this.form.program_id === 0) 
                errors.inputs_with_errors.push(
                    {
                        field: 'Program',
                        error: "Please select a program"
                    }
                );

            // Make sure there is at least one question
            if (this.form.questions.length === 0)
                errors.inputs_with_errors.push(
                    {
                        field: 'Questions',
                        error: "Forms must have at least one question"
                    }
                );

            // Validate form question answers and prereqs
            for (var i = 0; i < this.form.questions.length; i++) {

                var question = this.form.questions[i];
                var index = this.getQuestionIndex(question) + 1;
                if (question.type != 'text' && question.answers.length === 0) {
                    errors.questions_with_errors.push(
                        {
                            question: "Question " + index,
                            error: "Must have at least 1 answer"
                        }
                    );
                } else if (question.answers.length > this.editFormPage.MAX_FORM_ANSWERS_PER_QUESTION) {
                    errors.questions_with_errors.push(
                        {
                            question: "Question " + index,
                            error: "Maximum number of answers per question is " + this.editFormPage.MAX_FORM_ANSWERS_PER_QUESTION
                        }
                    );
                }

                if (this.getQuestionParentPrereqs(question.id).length > this.editFormPage.MAX_FORM_PREREQS_PER_QUESTION) {
                    errors.questions_with_errors.push(
                        {
                            question: "Question " + index,
                            error: "Maximum number of prerequisites per question is " + this.editFormPage.MAX_FORM_PREREQS_PER_QUESTION
                        }
                    );
                }
            }

            // Make sure outcome dates are included if outcome form
            if (this.form.outcome_form) {

                if (this.form.outcome_intervals.length === 0) {
                    errors.inputs_with_errors.push(
                        {
                            field: 'Outcome Intervals',
                            error: "You must add one or more outcome intervals"
                        }
                    );
                } else {

                    for (var i = 0; i < this.form.outcome_intervals.length; i++) {
                        if (this.form.outcome_intervals[i].value === null ||
                            this.form.outcome_intervals[i].value.match(/^[0-9]+$/) === null) {

                            errors.inputs_with_errors.push(
                                {
                                    field: 'Outcome Intervals',
                                    error: "Intervals must be positive integer values"
                                }
                            );
                        }
                    }

                    if (this.form.outcome_intervals.length > this.editFormPage.MAX_FORM_OUTCOME_DATES) {
                        errors.inputs_with_errors.push(
                            {
                                field: 'Outcome Intervals',
                                error: "Maximum number of outcome intervals is " + this.editFormPage.MAX_FORM_OUTCOME_DATES
                            }
                        );
                    }

                }

            }

            return errors;

        },

        displayEditFormErrors: function (errors) {

            var errors = errors || this.getEditFormErrors();

            for (var i = 0; i < errors.inputs_with_errors.length; i++) {



            }

        },

        submitForm: function (event) {

            $target = $(event.target);

            // validate form
            var form_errors = this.getEditFormErrors();

            this.editFormPage.errors = form_errors;

            if (! this.editFormIsValid(form_errors)) {

                $(".form-submit-btn").button('default');

                Vue.nextTick(function() {
                    $("#form-errors")[0].scrollIntoView();
                });
                return;
            }

            var url = "/forms";
            var method = $(event.target).attr('method');

            if (method.toLowerCase() == 'put') {
                url += '/' + vue_data.form.id;
            }

            // Remove unnecessary data
            var ajax_data = JSON.parse(JSON.stringify(vue_data));
            delete ajax_data['editPrereqsModal'];

            $.ajax({
                url: url,
                method: method,
                data: JSON.stringify(ajax_data),
                contentType: 'application/json',
                dataType: 'json',
                success: function (data) {
                    if (data.success) {
                        window.location = '/forms';
                    } else {
                        alert('There was a problem submitting this form.');
                    }
                },
                error: function (data) {
                    alert('There was a problem submitting this form.');
                    console.log(data);
                    $(".form-submit-btn").button('default');
                }
            });

        },

        editFormPublish: function (event) {
            this.form.published = true;
            this.makeBtnLoading(event);
        },

        makeBtnLoading: function (event) {
            var $target = $(event.target);
            if ($target.closest('form')[0].checkValidity())
                $target.button('loading');
        },

        // Form Creation/Management functionality

        toggleAnswers: function (event) {
            var $target = $(event.target);

            if ($target.hasClass('glyphicon')) {
                var glyphicon = $target;
            } else {
                var glyphicon = $target.children('.glyphicon');
            }
            if ( glyphicon.hasClass('glyphicon-minus') ) {
                glyphicon.removeClass('glyphicon-minus');
                glyphicon.addClass('glyphicon-plus');
            } else {
                glyphicon.removeClass('glyphicon-plus');
                glyphicon.addClass('glyphicon-minus');
            }

            $target.closest('.form-question-answers-header')
                .siblings('.form-question-answers')
                .toggleClass('hidden');
        },

        newQuestion: function (event) {

            var $target = $(event.target);

            var largest_id = 0;
            for (var i=0; i < this.form.questions.length; i++) {
                if (this.form.questions[i].id > largest_id) {
                    largest_id = this.form.questions[i].id;
                }
            }

            var new_order = this.questionsSorted[this.questionsSorted.length - 1].order;

            this.form.questions.push(
                {
                    id: largest_id + 1,
                    order: new_order + 1,
                    label: "",
                    required: true,
                    type: "radio",
                    answers: [],
                    new: true
                }
            );

            Vue.nextTick(function() {
                $target.closest(".form-questions").find('.form-question-label-input').last().focus();
            });

        },

        newAnswer: function (event) {
            var $target = $(event.target);

            var question_id = +$target.closest('.form-question').attr('data-question-id');

            var question = this.getQuestionById(question_id);

            var largest_id = this.getLargestQuestionAnswerId(question);

            var new_question_type = "";
            if (question.type == 'radio') {
                new_question_type = 'radio';
            } else if (question.type == 'checkbox') {
                new_question_type = 'checkbox';
            } else if (question.type == 'text') {
                new_question_type = 'text';
            }

            question.answers.push(
                {
                    id: largest_id+1,
                    order: question.answers.length,
                    label: "",
                    type: new_question_type,
                    new: true
                }
            );

            Vue.nextTick(function() {
                $target.closest(".form-question").find('.form-question-answer-label-input').last().focus();
            });

        },

        questionTypeChanged: function (event) {
            var $target = $(event.target);

            var question_id = +$target.closest('.form-question').attr('data-question-id');
            var question = this.getQuestionById(question_id);
            question.answers = [];

            this.removeQuestionChildPrereqs(question);

        },

        getFormById: function (id) {
            for (var i = 0; i < this.all_forms.length; i++) {
                if (this.all_forms[i].id == id) {
                    return this.all_forms[i];
                }
            }
            return null;
        },

        getQuestionById: function (id) {
            return this.questionsIndexed[id] || null;
        },

        getQuestionIndex: function (question) {
            var question_index = null;

            for (var i=0; i < this.form.questions.length; i++) {
                if (this.form.questions[i] == question) {
                    question_index = i;
                }
            }

            return question_index;
        },

        getQuestionAnswerById: function (question_id, answer_id) {
            var answers = this.getQuestionById(question_id).answers;

            for (var i = 0; i < answers.length; i++) {
                if (answers[i].id == answer_id) {
                    return answers[i];
                }
            }

            return null;
        },

        getQuestionAnswerIndex: function (question, answer) {
            var index = null;

            for (var i = 0; i < question.answers.length; i++) {
                if (question.answers[i].id == answer.id) {
                    index = i;
                }
            }

            return index;
        },

        getLargestQuestionAnswerId: function(question) {
            var largest_id = null;
            for (var i=0; i < question.answers.length; i++) {
                if (largest_id === null) {
                    largest_id = question.answers[i].id;
                }
                if (question.answers[i].id > largest_id) {
                    largest_id = question.answers[i].id;
                }
            }

            return largest_id;
        },

        getLargestPrereqId: function () {
            var largest_id = null;
            for (var i=0; i < this.form.prereqs.length; i++) {
                if (largest_id === null) {
                    largest_id = this.form.prereqs[i].id;
                }
                if (this.form.prereqs[i].id > largest_id) {
                    largest_id = this.form.prereqs[i].id;
                }
            }

            return largest_id;
        },

        getAvailablePrereqQuestions: function(question_index) {

            var questions = [];

            for (var i = 0; i < question_index; i++) {
                if (this.form.questions[i].type != 'text') {
                    questions.push(this.form.questions[i]);
                }
            }

            return questions;

        },

        getQuestionParentPrereqs: function (id) {
            var prereqs = [];
            for (var i = 0; i < this.form.prereqs.length; i++) {
                if (this.form.prereqs[i].child_question_id == id) {
                    prereqs.push(this.form.prereqs[i]);
                }
            }
            return prereqs;
        },

        getQuestionChildPrereqs: function (id) {
            var prereqs = [];
            for (var i = 0; i < this.form.prereqs.length; i++) {
                if (this.form.prereqs[i].parent_question_id == id) {
                    prereqs.push(this.form.prereqs[i]);
                }
            }

            return prereqs;
        },

        getPrereqs: function (parent_question_id, child_question_id, answer_id) {
            var parent_question_id = parent_question_id || null;
            var child_question_id = child_question_id || null;
            var answer_id = answer_id || null;

            var ret_prereqs = [];

            for (var i = 0; i < this.form.prereqs.length; i++) {
                var prereq = this.form.prereqs[i];

                var matches = true;

                // Check if prereq matches criteria
                if (
                    parent_question_id !== null &&
                    prereq.parent_question_id != parent_question_id
                    ) {

                    matches = false;
                }
                if (
                    child_question_id !== null &&
                    prereq.child_question_id != child_question_id
                    ) {
                    
                    matches = false;
                }
                if (
                    answer_id !== null &&
                    prereq.answer != answer_id
                    ) {
                    
                    matches = false;
                }

                if (matches) {
                    ret_prereqs.push(prereq);
                }
            }

            return ret_prereqs;
        },

        questionOrderUp: function (event) {

            var $target = $(event.target);

            var question_id = +$target.closest('.form-question').attr('data-question-id');
            var question_index = +$target.closest('.form-question').attr('data-question-index');
            var question = this.questionsSorted[question_index];
            var prev_question = this.questionsSorted[question_index - 1];

            var this_order = question.order;
            question.order = prev_question.order;

            prev_question.order = this_order;

            // Remove prereqs where this question is now before the parent
            // question in the order
            var parent_prereqs = this.getQuestionParentPrereqs(question_id);
            var remove_prereq_ids = [];
            for (var i = 0; i < parent_prereqs.length; i++) {

                var parent_question_id = parent_prereqs[i].parent_question_id;
                var parent_question = this.getQuestionById(parent_question_id);

                if (parent_question.order > question.order) {
                    remove_prereq_ids.push(parent_prereqs[i].id);
                }
            }

            for (var i = 0; i < remove_prereq_ids.length; i++) {
                this.removePrereqById(remove_prereq_ids[i]);
            }

        },

        questionOrderDown: function (event) {

            var $target = $(event.target);

            var question_id = +$target.closest('.form-question').attr('data-question-id');
            var question_index = +$target.closest('.form-question').attr('data-question-index');
            var question = this.questionsSorted[question_index];
            var next_question = this.questionsSorted[question_index + 1];

            var this_order = question.order;
            question.order = next_question.order;

            next_question.order = this_order;

            // Remove prereqs where this question is now after the child
            // question in the order
            var child_prereqs = this.getQuestionChildPrereqs(question_id);
            var remove_prereq_ids = [];
            for (var i = 0; i < child_prereqs.length; i++) {

                var child_question_id = child_prereqs[i].child_question_id;
                var child_question = this.getQuestionById(child_question_id);

                if (child_question.order < question.order) {
                    remove_prereq_ids.push(child_prereqs[i].id);
                }
            }

            for (var i = 0; i < remove_prereq_ids.length; i++) {
                this.removePrereqById(remove_prereq_ids[i]);
            }

        },

        removeQuestion: function (event) {
            var $target = $(event.target);

            var question_id = +$target.closest('.form-question').attr('data-question-id');
            var question = this.getQuestionById(question_id);

            // remove associated Prereqs
            this.removeQuestionParentPrereqs(question);
            this.removeQuestionChildPrereqs(question);

            var question_index = this.getQuestionIndex(question);
            if (question_index !== null) 
                this.form.questions.splice(question_index, 1);

        },

        removeAnswer: function(event) {
            var $target = $(event.target);

            var answer_id = +$target.closest('.form-question-answer').attr('data-answer-id');
            var question_id = +$target.closest('.form-question').attr('data-question-id');
            var question = this.getQuestionById(question_id);
            var answer = this.getQuestionAnswerById(question_id, answer_id);
            // remove associated prereqs
            this.removeAnswerPrereqs(question, answer);

            var answer_index = this.getQuestionAnswerIndex(question, answer);
            question.answers.splice(answer_index, 1);
        },

        removeQuestionParentPrereqs: function (question) {
            var question_id = question.id;
            var prereqs = this.getQuestionParentPrereqs(question_id);

            for (var i = 0; i < prereqs.length; i++) {
                for (var j = 0; j < this.form.prereqs.length; j++) {
                    if (prereqs[i].id == this.form.prereqs[j].id) {
                        this.form.prereqs.splice(j, 1);
                        break;
                    }
                }
            }
        },

        removeQuestionChildPrereqs: function (question) {
            var question_id = question.id;
            var prereqs = this.getQuestionChildPrereqs(question_id);

            for (var i = 0; i < prereqs.length; i++) {
                for (var j = 0; j < this.form.prereqs.length; j++) {
                    if (prereqs[i].id == this.form.prereqs[j].id) {
                        this.form.prereqs.splice(j, 1);
                        break;
                    }
                }
            }
        },

        removeAnswerPrereqs: function (question, answer) {
            var prereq_indices = [];
            for (var i = 0; i < this.form.prereqs.length; i++) {
                if (this.form.prereqs[i].parent_question_id == question.id
                    && this.form.prereqs[i].answer == answer.id) {
                    prereq_indices.push(i);
                }
            }
            for (var i = 0; i < prereq_indices.length; i++) {
                this.form.prereqs.splice(prereq_indices[i], 1);
            }
        },

        removePrereqClick: function (event) {

            var $target = $(event.target);

            var prereq_id = $target.closest('.prereq').attr('data-prereq-id');

            this.removePrereqById( prereq_id );

        },

        removePrereqById: function (id) {

            for (var i=0; i < this.form.prereqs.length; i++) {
                var prereq = this.form.prereqs[i];

                if (prereq.id == id) {
                    this.form.prereqs.splice(i, 1);
                    break;
                }
            }

        },

        removePrereq: function(parent_question_id, child_question_id, answer_id) {

            for (var i=0; i < this.form.prereqs.length; i++) {
                var prereq = this.form.prereqs[i];

                if (
                    prereq.parent_question_id == parent_question_id &&
                    prereq.child_question_id  == child_question_id &&
                    prereq.answer == answer_id
                ) {
                    this.form.prereqs.slice(i, 1);
                }
            }

        },

        editQuestionPrereqs: function (event) {

            var $target = $(event.target);

            if ($target.attr('disabled') == 'disabled') {
                return;
            }

            var question_id = +$target.closest('.form-question').attr('data-question-id');
            var question_index = +$target.closest('.form-question').attr('data-question-index');

            this.showEditQuestionPrereqsPage();

            this.editPrereqsModal.chosenChildQuestionId = question_id;
            this.editPrereqsModal.chosenChildQuestionIndex = question_index;

            $('#edit-prereqs-modal').modal();

        },

        choosePrereqQuestion: function (event) {

            var $target = $(event.target);

            var question_id = $target.closest('.prereq-choose-question')
                .attr('data-question-id');
            var question = this.getQuestionById(question_id);

            this.editPrereqsModal.chosenParentQuestion = question;

            this.showChoosePrereqAnswerPage();

        },

        choosePrereqAnswer: function (event) {

            var $target = $(event.target);

            if ($target.closest('.prereq-choose-answer').hasClass('already-required')) return;

            var answer_id = $target.closest('.prereq-choose-answer')
                .attr('data-answer-id');
            var answer = this.getQuestionAnswerById(this.editPrereqsModal.chosenParentQuestion.id, answer_id);

            this.editPrereqsModal.chosenParentAnswer = answer;

            // check if parent question type is 'radio', if so replace existing Prereqs
            var existing_prereq = false;
            if (this.editPrereqsModal.chosenParentQuestion.type == 'radio') {
                var prereqs = this.getPrereqs(
                    this.editPrereqsModal.chosenParentQuestion.id,
                    this.editPrereqsModal.chosenChildQuestionId
                );
                if (prereqs.length > 0) {
                    var prereq = prereqs[0];
                    prereq.answer = answer_id;
                    existing_prereq = true;
                }
            }

            // add Prereq
            if (!existing_prereq) {
                var largest_id = this.getLargestPrereqId();
                var prereq = {
                    "id": largest_id + 1,
                    "parent_question_id": this.editPrereqsModal.chosenParentQuestion.id,
                    "child_question_id": this.editPrereqsModal.chosenChildQuestionId,
                    "answer": this.editPrereqsModal.chosenParentAnswer.id,
                    "new": true
                }

                this.form.prereqs.push(prereq);
            }

            this.showPrereqAddedPage();

        },

        resetEditPrereqsModal: function () {

            this.editPrereqsModal.chosenParentQuestion = null;
            this.editPrereqsModal.chosenParentAnswer = null;
            this.editPrereqsModal.chosenChildQuestionId = null;
            this.editPrereqsModal.chosenChildQuestionIndex = null;
            this.editPrereqsModal.page = this.editPrereqsModal.INITIAL_PAGE;

        },

        restartEditPrereqsModal: function () {

            this.editPrereqsModal.chosenParentQuestion = null;
            this.editPrereqsModal.chosenParentAnswer = null;
            this.editPrereqsModal.page = this.editPrereqsModal.INITIAL_PAGE;

        },

        closeEditPrereqsModal: function () {

            $('#edit-prereqs-modal').modal('hide');

        },

        // Edit Prereq Modal Pages
        showEditQuestionPrereqsPage: function () {
            this.editPrereqsModal.page = this.editPrereqsModal.INITIAL_PAGE;
        },

        showAddNewPrereqPage: function () {
            this.editPrereqsModal.page = this.editPrereqsModal.ADD_NEW_PREREQ_PAGE;
        },

        showChoosePrereqAnswerPage: function () {
            this.editPrereqsModal.page = this.editPrereqsModal.CHOOSE_PREREQ_ANSWER_PAGE;
        },

        showPrereqAddedPage: function() {
            this.editPrereqsModal.page = this.editPrereqsModal.PREREQ_ADDED_PAGE;
        },

        formPublishedState: function(event) {
            var $target = $(event.target);
            $target.addClass('loading');
            
            var form_id = $target.closest('.form-listing').attr('data-form-id');

            var url = ($target[0].checked) ? '/forms/publish/' + form_id : '/forms/unpublish/' + form_id;

            $.ajax({
                url: url,
                method: 'GET',
                success: function (data) {
                    location.reload();
                },
                error: function (data) {
                    alert('There was a problem performing this action.');
                    console.log(data);
                    $target.removeClass('loading');
                },
                complete: function () {
                }
            });
        },

        formDuplicate: function (event) {

            var $target = $(event.target);
            $target.button('loading');

            var form_id = $target.closest('.form-listing').attr('data-form-id');

            var url = '/forms/duplicate/' + form_id;

            $.ajax({
                url: url,
                method: 'GET', 
                success: function (data) {
                    location.reload();
                },
                error: function (data) {
                    alert('There was a problem performing this action.');
                    console.log(data);
                    $target.button('default');
                },
            });

        },

        userActiveState: function(event) {
            var $target = $(event.target);
            $target.addClass('loading');
            
            var user_id = $target.closest('.user-listing').attr('data-user-id');

            var url = ($target[0].checked) ? '/user/activate/' + user_id : '/user/deactivate/' + user_id;

            $.ajax({
                url: url,
                method: 'GET',
                success: function (data) {
                    location.reload();
                },
                error: function (data) {
                    alert('There was a problem performing this action.');
                    console.log(data);
                    $target.removeClass('loading');
                    $target[0].checked = !$target[0].checked;
                },
                complete: function () {
                }
            });
        },

        formArchive: function(event) {
            var $target = $(event.target);
            //$target.button('loading');
            
            var form_id = $target.closest('.form-listing').attr('data-form-id');

            this.archiveFormModal.chosenFormId = form_id;

            $('#archive-form-modal').modal();
        },

        formArchiveConfirm: function(event) {
            var $target = $(event.target);
            $target.button('loading');

            var url = '/forms/archive/' + this.archiveFormModal.chosenFormId;

            $.ajax({
                url: url,
                method: 'GET',
                success: function (data) {
                    location.reload();
                },
                error: function (data) {
                    alert('There was a problem performing this action.');
                    console.log(data);
                    $target.button('default');
                },
                complete: function () {
                    $('#archive-form-modal').modal('hide');
                }
            });
        },

        formUnarchive: function(event) {
            var $target = $(event.target);
            $target.button('loading');
            
            var form_id = $target.closest('.form-listing').attr('data-form-id');

            var url = '/forms/unarchive/' + form_id;

            $.ajax({
                url: url,
                method: 'GET',
                success: function (data) {
                    location.reload();
                },
                error: function (data) {
                    alert('There was a problem performing this action.');
                    console.log(data);
                    $target.button('default');
                },
                complete: function () {
                }
            });
        },

        formSelectAssign: function(event) {

            var $target = $(event.target);
            this.assignFormPage.selected_form_id = $target.val();

        },

        formAssign: function(event) {

            event.target.action = 'assign/' + this.assignFormPage.selected_form_id;
            $(event.target).button('loading');
            event.target.submit();

        },

        formUnassign: function (event) {

            var $target = $(event.target);
            var form_el = $target.closest('.form-assignment-form');

            form_el[0].action = 'unassign/' + this.assignFormPage.selected_form_id;
            //$(event.target).button('loading');
            form_el.submit();

        },

        formDelete: function(event) {

            var $target = $(event.target);
            var form_id = $target.closest('.form-listing').attr('data-form-id');

            this.deleteFormModal.chosenFormId = form_id;

            $('#delete-form-modal').modal();

        },

        formDeleteConfirm: function(event) {
            var $target = $(event.target);
            $target.button('loading');

            var url = '/forms/' + this.deleteFormModal.chosenFormId;

            $.ajax({
                url: url,
                method: 'DELETE',
                success: function (data) {
                    location.reload();
                },
                error: function (data) {
                    alert('There was a problem performing this action.');
                    console.log(data);
                    $target.button('default');
                },
                complete: function () {
                    $('#delete-form-modal').modal('hide');
                }
            });
        },

        submitAssignmentSearchForm: function (event) {

            $target = $(event.target);
            $target.find('.submit-btn').button('loading');
            $(".assignments-loading").removeClass("hidden");
            $(".form-assignment-listing").addClass("hidden");

            var self = this;

            var data = {
                registration_number: $target[0].registration_number.value
            };

            this.assignFormPage.form_already_assigned = false;
            this.assignFormPage.form_program_matches = true;

            $.ajax({
                url: $target[0].action,
                method: $target[0].method,
                data: data,
                success: function (data) {
                    self.assignFormPage.assignments = data.assignments;
                    self.assignFormPage.registration_found = data.registration_found;

                    // Hide assign form button if this form is already assigned
                    for (var i = 0; i < data.assignments.length; i++) {
                        if (data.assignments[i].form_id === +self.assignFormPage.selected_form_id) {
                            self.assignFormPage.form_already_assigned = true;
                            break;
                        }
                    }
                    // Hide assign form button if this form does not match
                    // registration's program
                    var current_form = self.getFormById(self.assignFormPage.selected_form_id);
                    if (data.program_id !== +current_form.program_id) {
                        self.assignFormPage.form_program_matches = false;
                    }

                    Vue.nextTick(function () {

                        $('#registration_number_hidden').val($("#registration_number").val());

                    });
                },
                error: function (data) {
                    alert("There was a problem searching for registrations");
                    console.log(data);
                },
                complete: function() {
                    self.assignFormPage.registration_searched = true;
                    $(".assignments-loading").addClass("hidden");
                    $(".form-assignment-listing").removeClass("hidden");
                    $target.find('.submit-btn').button('initial');
                }
            });
        },
    },

    filters: {
        capitalize: function (value) {
            if (!value) return ''
            value = value.toString()
            return value.charAt(0).toUpperCase() + value.slice(1)
        }
    },
    computed: {
        questionsIndexed: function() {
            var questions_indexed = {};
            for (var i = 0; i < this.form.questions.length; i++) {
                questions_indexed[ this.form.questions[i].id ] = this.form.questions[i];
            }

            return questions_indexed;
        },
        questionsSorted: function() {
            var questions_sorted = [];
            for (var i = 0; i < this.form.questions.length; i++) {
                questions_sorted.push(this.form.questions[i]);
            }
            questions_sorted.sort(function(a, b) {
                return a.order - b.order;
            });
            return questions_sorted;
        }
    },
    components: {
        'outcome-timeline': outcomeTimeline
    }
});

// Run any time data changes
app.$watch('$data', function() {
    // Show tooltips
    $('[data-toggle="tooltip"]').tooltip();
}, {deep: true});

$(document).ready( function() {

    vue_data.assignFormPage.selected_form_id = $('.assignment-search-form')
        .attr('data-current-form-id');

    $(".program-delete").click(function() {

        var program_id = $(this).closest(".program").attr("data-program-id");

        $("#delete-modal .modal-real-content").hide();
        $("#delete-modal .modal-loading").show();
        $("#delete-modal").modal();

        $.ajax({

            url: "/programs/delete-modal/" + program_id,
            method: "GET",
            success: function (data) {
                $("#delete-modal .modal-real-content").html(data);
                $("#delete-modal #delete-program-confirm").attr("data-program-id", program_id);
                $("#delete-modal #delete-program-confirm").click(deleteProgramAjax);
                $("#delete-modal .modal-real-content").show();
                $("#delete-modal .modal-loading").hide();
            },
            failure: function (error) {
                alert(error);
            }

        });

    });

    function deleteProgramAjax() {

        var program_id = $(this).attr("data-program-id");

        if (program_id !== '') {

            var $btn = $(this).button('loading');

            var action = $('input[name="program-users-action"]:checked', '#program-users-form').val();
            var new_program = $('#program-users-new-program').val();

            $.ajax({

                url: "/programs/" + program_id,
                method: "DELETE",
                data: {
                    action: action,
                    new_program: new_program
                },
                success: function (data) {
                    window.location.reload();
                },
                error: function (error) {
                    alert(error);
                }

            });

        }

    }
    $(".user-registration").click(function () {
        var registration_number = $(this).attr('data-registration-number');
        $("#registration_number").val(registration_number);
        $(".registration-select-form").submit();
    });

    $(".user-delete").click(function() {

        var user_id = $(this).closest(".user-listing").attr("data-user-id");

        $("#user-delete-modal .modal-real-content").hide();
        $("#user-delete-modal .modal-loading").show();
        $("#user-delete-modal").modal();

        $.ajax({

            url: "/users/delete-modal/" + user_id,
            method: "GET",
            success: function (data) {
                $("#user-delete-modal .modal-real-content").html(data);
                $("#user-delete-modal #delete-user-confirm").attr("data-user-id", user_id);
                $("#user-delete-modal #delete-user-confirm").click(deleteUserAjax);
                $("#user-delete-modal .modal-real-content").show();
                $("#user-delete-modal .modal-loading").hide();
            },
            failure: function (error) {
                alert( "There was a problem completing this action." )
                console.log(error);
            }

        });

    });

    function deleteUserAjax() {

        var user_id = $(this).attr("data-user-id");

        if (user_id !== '') {

            var $btn = $(this).button('loading');

            $.ajax({

                url: "/users/" + user_id,
                method: "DELETE",
                data: {
                },
                success: function (data) {
                    window.location.reload();
                },
                error: function (error) {
                    alert( "There was a problem completing this action." )
                    console.log(error);
                }

            });

        }

    }

    $('[data-toggle="tooltip"]').tooltip();
});