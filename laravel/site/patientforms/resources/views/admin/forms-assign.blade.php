@extends('layouts.app-no-sidebar')

@section('title', 'Forms')

@section('sidebar')
    @parent

    <p>Sidebar content here.</p>
@endsection

@section('content')
    <div id='app'>
        <h1>Assign Form</h1>
    
        <div class="panel panel-default assignment-search-container">
            <div class="panel-body">
                @if (count($forms) > 0)
                    <form action="@relative_route('assignments.search')" method="POST" class="assignment-search-form form-horizontal" data-current-form-id="{{ $selected_form === null ? $forms[0]->id : $selected_form->id }}" @submit.prevent="submitAssignmentSearchForm">
                        <div class="form-group">
                            <label for="form_id" class="col-sm-2 control-label">Form</label>
                            <div class="col-sm-10">
                                <select name="form_id" id="form_id" class="form-control" v-model="assignFormPage.selected_form_id">
                                    <option v-for="form in all_forms"
                                        :value="form.id">@{{ form.name }} (@{{ form.id }})</option>
                                </select>
                             </div>
                         </div>
                        <div class="form-group">
                            <label for='registration_number' class="col-sm-2 control-label">Registration Number:</label>
                            <div class="col-sm-10">
                                <div class="input-group">
                                    <input type='text' name='registration_number' id='registration_number' placeholder='Registration Number' class="form-control">
                                    <span class="input-group-btn">
                                        <button type='submit' data-loading-text="Loading..." data-initial-text="Search" class="btn btn-info submit-btn">Search</button>
                                    </span>
                                </div>
                            </div>
                        </div>
                        {{ csrf_field() }}
                    </form>
                    <hr>
                    <form-assignment-listing :assignments='assignFormPage.assignments' :registration_found='assignFormPage.registration_found' :form_program_matches='assignFormPage.form_program_matches' :form_already_assigned="assignFormPage.form_already_assigned" v-if='assignFormPage.registration_searched'>
                    </form-assignment-listing>
                    <div class="assignments-loading hidden">
                        <p class="text-center">
                            <img src={{ asset("images/ripple.svg") }}><br>
                            <small class="text-muted">Loading...</small>
                        </p>
                    </div>
                @else
                    <p>There are no forms currently assignable.</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Vue templates -->
    <template id='form-assignment-listing'>
        <div class="form-assignment-listing">
            <div v-if="registration_found">
                <h4 class="text-success">Registration found.</h4>
                <div v-if="assignments.length > 0">
                    <form action="" method="POST" @submit.prevent="formAssign" class="form-assignment-form">
                        <table class='table table-striped table-bordered' >
                            <colgroup>
                                <col></col>
                                <col></col>
                                <col style="width:5%"></col>
                            </colgroup>
                            <thead>
                                <th>Currently Assigned Forms</th>
                                <th>Status</th>
                                <th></th>
                            </thead>
                            <tbody>
                                <tr v-for="assignment in assignments" class="form-assignment" :data-assignment-form-id="assignment.form_id">
                                    <td>@{{ assignment.form_name }}</td>
                                    <td>@{{ assignment.status }}</td>
                                    <td>
                                        <a href='#' class='btn btn-danger' @click='formUnassign'
                                        v-if='assignment.status == "Assigned"'>
                                            <i class="glyphicon glyphicon-remove">
                                            </i>
                                        </a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <input type='hidden' name='registration_number' value='' id="registration_number_hidden">
                        <input type='hidden' name='form_program_id' value='' id="form_program_id_hidden">
                        <button v-if="form_already_assigned === false && form_program_matches === true" type='submit' data-loading-text="Loading..." class="btn btn-primary assign-form-btn">Assign Form to this Registration</button>
                    </form>
                </div>
                <div v-else-if="assignments.length == 0" >
                    <p>No existing assignments found for this registration.</p>
                    <p class="text-danger" v-if="form_program_matches === false">This registration belongs to a different program than the selected form.</p>
                    <form action="" method="POST" @submit.prevent="formAssign">
                        <input type='hidden' name='registration_number' value='' id="registration_number_hidden">
                        <button v-if="form_already_assigned === false && form_program_matches === true" type='submit' data-loading-text="Loading..." class="btn btn-primary assign-form-btn">Assign Form to this Registration</button>
                    </form>
                </div>
            </div>
            <div v-else>
                <h4 class="text-danger">Registration not found.</h4>
            </div>
        </div>
    </template>

    <script>
        if (typeof(vue_data) === 'undefined') {
                vue_data = {};
            }
        vue_data['all_forms'] = [
            @foreach ($forms as $form)
                {
                    "id": "{{ $form->id }}",
                    "name": "{{ $form->name }}",
                    "program_id": "{{ $form->program_id }}",
                },
            @endforeach
        ];
        
    </script>

@endsection