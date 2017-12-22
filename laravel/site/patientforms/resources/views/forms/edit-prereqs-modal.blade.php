<div class="modal fade" tabindex="-1" id="edit-prereqs-modal" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">


            <!-- INITIAL PAGE -->
            <div id="initial-page" class="modal-page" v-bind:class='{ hidden: editPrereqsModal.page != editPrereqsModal.INITIAL_PAGE}'>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Edit Prerequisites</h4>
                </div>
                <div class="modal-body">

                    <h3>Prerequisite Questions for Question # @{{ editPrereqsModal.chosenChildQuestionIndex + 1 }}</h3>
                    <table class="table prereq-listing table-striped table-bordered" v-if='getQuestionParentPrereqs(editPrereqsModal.chosenChildQuestionId).length > 0'>
                        <colgroup>
                            <col style="width:47.5%">
                            <col style="width:47.5%">
                            <col style="width:5%">
                        </colgroup>
                        <thead>
                            <th>Parent Question</th>
                            <th>Required Answer</th>
                            <th></th>
                        </thead>
                        <tbody>
                            <tr v-for="prereq in getQuestionParentPrereqs(editPrereqsModal.chosenChildQuestionId)" class="prereq" :data-prereq-id="prereq.id">
                                <td>
                                    @{{ getQuestionById(prereq.parent_question_id).label }}
                                </td>
                                <td>
                                    @{{ getQuestionAnswerById(prereq.parent_question_id, prereq.answer).label }}
                                </td>
                                <td>
                                    <button class="btn btn-danger" @click.prevent='removePrereqClick'>
                                        <span class="glyphicon glyphicon-remove"></span>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <p v-if='getQuestionParentPrereqs(editPrereqsModal.chosenChildQuestionId).length <= 0'>No existing prerequisites</p>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" @click.prevent='showAddNewPrereqPage'>Add New</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>


            <!-- CHOOSE QUESTION PAGE -->
            <div id="choose-question-page" class="modal-page"  v-bind:class='{ hidden: editPrereqsModal.page != editPrereqsModal.ADD_NEW_PREREQ_PAGE}'>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Add Prerequisite Question</h4>
                </div>
                <div class="modal-body">

                    <h3>Add Prerequisite Question for Question # @{{ editPrereqsModal.chosenChildQuestionIndex + 1 }}</h3>
                    <h6>Choose a question</h6>
                    <table class="table table-striped table-bordered">
                        <thead>
                            <th>#</th>
                            <th>Label</th>
                        </thead>
                        <tbody>
                            <tr v-for='(question, question_index) in getAvailablePrereqQuestions(editPrereqsModal.chosenChildQuestionIndex)' class='prereq-choose-question' :data-question-id='question.id' @click.prevent='choosePrereqQuestion'>
                                <td>@{{ question_index + 1 }}</td>
                                <td>@{{ question.label }}</td>
                            </tr>
                        </tbody>
                    </table>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-warning" @click.prevent='showEditQuestionPrereqsPage'>Back</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>

            <!-- CHOOSE PREREQ ANSWER PAGE -->
            <div id="choose-prereq-answer-page" class="modal-page"  v-bind:class='{ hidden: editPrereqsModal.page != editPrereqsModal.CHOOSE_PREREQ_ANSWER_PAGE}'>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Add Prerequisite Question</h4>
                </div>
                <div class="modal-body">

                    <h3>Choose Required Answer for Question # @{{ getQuestionIndex(editPrereqsModal.chosenParentQuestion) + 1 }}</h3>
                    <h6>Choose an answer:</h6>
                    <table class="table table-striped table-bordered" v-if="editPrereqsModal.chosenParentQuestion !== null">
                        <thead>
                            <th>#</th>
                            <th>Label</th>
                        </thead>
                        <tbody>
                            <tr v-for='(answer, answer_index) in editPrereqsModal.chosenParentQuestion.answers' class='prereq-choose-answer' :data-answer-id='answer.id' v-bind:class='{ "already-required": getPrereqs(editPrereqsModal.chosenParentQuestion.id, editPrereqsModal.chosenChildQuestionId, answer.id).length > 0 }' @click.prevent='choosePrereqAnswer'>
                                <td>@{{ answer_index + 1 }}</td>
                                <td>
                                    <span class="prereq-choose-answer-label">
                                        @{{ answer.label }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-warning" @click.prevent='showAddNewPrereqPage'>Back</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>

            <!-- PREREQ ADDED PAGE -->
            <div id="prereq-added-page" class="modal-page"  v-bind:class='{ hidden: editPrereqsModal.page != editPrereqsModal.PREREQ_ADDED_PAGE}'>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Prerequisite Added</h4>
                </div>
                <div class="modal-body" v-if='editPrereqsModal.chosenParentAnswer !== null'>

                    <h3>Done.</h3>
                    <p>A prerequisite for <strong>Question #@{{ editPrereqsModal.chosenChildQuestionIndex + 1 }}</strong> was added.</p>
                    <p>The user must select <strong>"@{{ editPrereqsModal.chosenParentAnswer.label}}"</strong> on <strong>Question #@{{ getQuestionIndex(editPrereqsModal.chosenParentQuestion) + 1 }}</strong> ("<em>@{{ editPrereqsModal.chosenParentQuestion.label }}</em>") in order to see this question.</p>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" @click.prevent='closeEditPrereqsModal'>Done</button>
                    <button type="button" class="btn btn-default" @click.prevent='restartEditPrereqsModal'>Add Another</button>
                </div>
            </div>


        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->