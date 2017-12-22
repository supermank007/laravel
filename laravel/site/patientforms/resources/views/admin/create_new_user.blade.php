@extends('layouts.app-no-sidebar')

@section('title', 'Create New User')

@section('sidebar')
    @parent

    <p>Sidebar content here.</p>
@endsection

@section('content')
    <div id='app'>
        <h1>Create New User</h1>
        @if (count($errors) > 0)
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form action="/users" method="POST">
            <div class="form-group">
                <label for="first_name">First name:</label>
                <input type="text" name="first_name" class="form-control" placeholder="First name">
            </div>
            <div class="form-group">
                <label for="last_name">Last name:</label>
                <input type="text" name="last_name" class="form-control" placeholder="Last name">
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="text" name="email" class="form-control" placeholder="Email">
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" name="password" class="form-control">
            </div>
            <div class="form-group">
                <label for="password_confirm">Password Confirm:</label>
                <input type="password" name="password_confirm" class="form-control">
            </div>
            <div class="form-group">
                <label for="program">Program:</label>
                <select name="program_id" class="form-control">
                    @foreach ($programs as $program)
                        <option value="{{ $program->id }}">{{ $program->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <p><label>User Roles:</label><br>
                    @foreach ($roles as $role)
                        <label><input type='checkbox' name='role[]' value="{{ $role->id }}"> {{ $role->role }}</label><br>
                    @endforeach
                </p>
            </div>
            {{ csrf_field() }}
            <div class="form-group">
                <input type="submit" value="Submit" class="btn btn-default">
            </div>
        </form>
    </div>
@endsection