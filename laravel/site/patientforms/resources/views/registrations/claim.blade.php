@extends('layouts.app-no-sidebar')

@section('title', 'Claim Registration')

@section('sidebar')
    @parent

    <p>Sidebar content here.</p>
@endsection

@section('content')
    <div id='app'>
        <h1>Claim Registration</h1>
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
                
                <form action="" method="POST">
                    <div class="form-group">
                        <label for="registration_number">Registration Number:</label>
                        <input type="text" name="registration_number" class="form-control" placeholder="Registration Number" autofocus>
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="text" name="email" class="form-control" placeholder="Email">
                    </div>
                    {{ csrf_field() }}
                    <div class="form-group">
                        <input type="submit" value="Submit" class="btn btn-default">
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection