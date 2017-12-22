@extends('layouts.app-no-sidebar')

@section('title', 'Forms')

@section('content')
    <div id='app'>
        <h1>Form Listing</h1>
        @if ($currentUserRegistration === null)
            <div class='pull-left'>
                <small>
                    <i class="glyphicon glyphicon-exclamation-sign"></i> <a href='@relative_route('select_registration')'>Select a registration</a> to take forms.
                </small>
            </div>
        @endif
        <div class="text-right">
            <p>
                @if ( $currentUser && $currentUser->hasRoles('Super-Admin', 'Master-Admin') )
                    <a href="@relative_route('forms.create')" class="btn btn-primary btn-lg"><i class="glyphicon glyphicon-plus-sign"></i> New Form</a>
                @endif
            </p>
        </div>
        @include('forms.form-listing', ['forms' => $forms])

        <div class="modal fade" tabindex="-1" id="archive-form-modal" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content modal-real-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Confirm Archival</h4>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to archive this form?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-loading-text="Loading..." @click.prevent='formArchiveConfirm'>Archive</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->

        <div class="modal fade" tabindex="-1" id="delete-form-modal" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content modal-real-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Confirm Deletion</h4>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete this form?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-loading-text="Loading..." @click.prevent='formDeleteConfirm'>Delete</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
    </div>
@endsection