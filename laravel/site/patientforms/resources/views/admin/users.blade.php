@extends('layouts.app-no-sidebar')

@section('title', 'Users')

@section('sidebar')
    @parent

    <p>Sidebar content here.</p>
@endsection

@section('content')
    <div id='app'>
        <h1>User Listing</h1>
        <p class="text-right">
            <a class="btn btn-primary btn-lg" href='{{ route('users.create') }}'>Create New User</a>
        </p>
        <table class="table table-striped user-listing-table table-bordered table-no-inside-border">
            <colgroup>
                <col>
                <col>
                <col>
                <col>
                <col>
                <col style="width:5%">
            </colgroup>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Program</th>
                    <th>Roles</th>
                    <th>Active</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    @if (!$currentUser->hasRoles('Master-Admin') && $user->hasRoles('Master-Admin'))
                        @continue
                    @endif
                    <tr class='user-listing' data-user-id="{{ $user->id }}">
                        <td>{{ $user->fullName() }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->program->name }}</td>
                        <td>
                            @foreach ($user->roles()->get() as $role)
                                <span class="label label-default">{{ $role->role }}</span><br>
                            @endforeach
                        </td>
                        <td>
                            <input type='checkbox' name='user_active' @if ($user->active) checked @endif @click="userActiveState">
                        </td>
                        <td style="white-space:nowrap;">
                            <a href='/user/account/{{ $user->id }}' class="btn btn-default btn-sm">View</a>
                            @if ($user->id != $currentUser->id)
                                <a href='#' class="btn btn-danger btn-sm user-delete">
                                    <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
                                </a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr colspan='5'>No users</tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="modal fade" tabindex="-1" id="user-delete-modal" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content modal-real-content">
                </div><!-- /.modal-content -->
                <div class="modal-content modal-loading">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Confirm Deletion</h4>
                    </div>
                    <div class="modal-body">
                        <p class="text-center">
                            <img src={{ asset("images/ripple.svg") }}><br>
                            <small class="text-muted">Loading...</small>
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
@endsection