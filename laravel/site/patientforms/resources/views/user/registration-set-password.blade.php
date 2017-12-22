@extends('layouts.app-no-sidebar')

@section('title', 'Set Password')

@section('sidebar')
    @parent

    <p>Sidebar content here.</p>
@endsection

@section('content')
    <div id='app'>
        <h1>Set Password</h1>
        <div class="panel panel-default">
            <div class="panel-body">
                @if (count($errors) > 0)
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                <form action="/confirm-claim" method="POST">
                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input type="password" name="password" class="form-control" autofocus>
                    </div>
                    <div class="form-group">
                        <label for="password_confirm">Password Confirm:</label>
                        <input type="password" name="password_confirm" class="form-control" autofocus>
                        <span id="helpBlock" class="help-block">Password must be 6 characters or longer.</span>
                    </div>
                    {{ csrf_field() }}
                    <input type="hidden" name="registration_number" value='{{ $registration_number }}'>
                    <input type="hidden" name="email" value='{{ $email }}'>
                    <div class="form-group">
                        <input type="submit" value="Submit" class="btn btn-default">
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection