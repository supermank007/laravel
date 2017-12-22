@extends('layouts.app-no-sidebar')

@section('title', 'Login')

@section('sidebar')
    @parent

    <p>Sidebar content here.</p>
@endsection

@section('content')
    <div id='app'>
        <h1>Login</h1>
        @foreach ($errors as $error)
            <div class="alert alert-danger">
                <strong>Error: </strong> {{ $error }}
            </div>
        @endforeach
        <div class="panel panel-default">
            <div class="panel-body">
                <form action="" method="POST" action="{{ url('/login') }}">
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="text" name="email" class="form-control" placeholder="Email" autofocus>
                    </div>
                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input type="password" name="password" class="form-control">
                    </div>
                    {{ csrf_field() }}
                    <div class="form-group">
                        <input type="submit" value="Submit" class="btn btn-default">
                    </div>
                    <div class="form-group">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="remember"> Remember Me
                            </label>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <p>
            If you haven't created an account yet, <a href='/claim'>claim your registration here</a>.
        </p>
        <p>
            Forgot your password? <a href='{{ url('/password/email') }}'>Reset your password</a>.
        </p>
    </div>
@endsection
