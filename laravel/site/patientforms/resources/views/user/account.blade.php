@extends('layouts.app-no-sidebar')

@section('title', 'User: ' . $user->email)

@section('sidebar')
    @parent

    <p>Sidebar content here.</p>
@endsection

@section('content')
    <div id='app'>
        <h1>Account Details</h1>
        <div class="panel panel-default">
            <div class="panel-body">
                @if (isset($message) && $message != '')
                    <div class='alert alert-success'>
                        {{ $message }}
                    </div>
                @endif
                @if (count($errors) > 0)
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form action="" method="POST" class="form">
                    <div class="form-group">
                        <label for="first_name">First Name:</label>
                        <input
                            class="form-control"
                            type="text"
                            name="first_name"
                            id=""
                            @if (! $user->isSameAs($currentUser) && ! $currentUser->hasRoles('Super-Admin', 'Master-Admin')) readonly @endif
                            value='{{ $user->first_name }}'
                        >
                    </div>
                    <div class="form-group">
                        <label for="last_name">Last Name:</label>
                        <input
                            class="form-control"
                            type="text"
                            name="last_name"
                            id=""
                            @if (! $user->isSameAs($currentUser) && ! $currentUser->hasRoles('Super-Admin', 'Master-Admin')) readonly @endif
                            value='{{ $user->last_name }}'
                        >
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input
                            class="form-control"
                            type="text"
                            name="email"
                            id=""
                            @if (! $user->isSameAs($currentUser) && ! $currentUser->hasRoles('Super-Admin', 'Master-Admin')) readonly @endif
                            value='{{ $user->email }}'
                        >
                    </div>
                    <div class="form-group">
                        <label>
                            Active <br>
                            <input
                                type='checkbox'
                                name='active'
                                value='1'
                                @if ($user->active) checked @endif
                                @if (! $user->isSameAs($currentUser) && ! $currentUser->hasRoles('Super-Admin', 'Master-Admin')) onclick='return false;' @endif
                            >
                        </label>
                    </div>
                    <div class="form-group">
                        <label for="program">Program:</label>
                        @if (! $user->isSameAs($currentUser) && ! $currentUser->hasRoles('Super-Admin', 'Master-Admin'))
                            <p>{{ $user->program->name }}</p>
                        @else
                            <select name="program_id" class="form-control">
                                @foreach ($programs as $program)
                                    <option
                                        value="{{ $program->id }}"
                                        @if ($program == $user->program) selected @endif
                                    >
                                        {{ $program->name }}
                                    </option>
                                @endforeach
                            </select>
                        @endif
                    </div>
                    @if ($currentUser->hasRoles('Admin', 'Super-Admin', 'Master-Admin'))
                        <div class="form-group">
                            <label for="roles">Roles:</label>
                            @if (! $currentUser->hasRoles('Super-Admin', 'Master-Admin'))
                                <p>
                                    @foreach ($user->roles()->get() as $role)
                                        <span class="label label-default">{{ $role->role }}</span><br>
                                    @endforeach
                                </p>
                            @else
                                <select name="roles_id[]" class="form-control" multiple>
                                    @foreach ($roles as $role)
                                        <option
                                            value="{{ $role->id }}"
                                            @if ($user->hasRoles($role->role)) selected @endif
                                        >
                                            {{ $role->role }}
                                        </option>
                                    @endforeach
                                </select>
                            @endif
                        </div>
                    @endif
                    @if ($user->id == $currentUser->id)
                        <div class="form-group">
                            <label for="password">
                                New Password
                            </label>
                            <input class="form-control"
                                type='password'
                                name='password'
                                >
                        </div>
                        <div class="form-group">
                            <label for="password_confirm">
                                Confirm New Password
                            </label>
                            <input class="form-control"
                                type='password'
                                name='password_confirm'
                                >
                        </div>
                    @endif
                    @if ($user->isSameAs($currentUser) || $currentUser->hasRoles('Super-Admin', 'Master-Admin'))
                        <div class="form-group">
                            <input type="submit" class="btn btn-primary" value='Update'>
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </div>
@endsection