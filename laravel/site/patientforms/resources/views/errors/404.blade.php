@extends('layouts.app-no-sidebar')

@section('title', '404 Error')

@section('content')
    <div id='app'>
        <h1>Error: 404</h1>
        <div class="panel panel-default">
            <div class="panel-body">
                <p>
                    Sorry, we couldn't find what you're looking for.
                </p>
                @if (isset($message) && $message !== '')
                    <hr>
                    <p class="text-danger">
                        <small>
                            <strong>Message:</strong> {{ $message }}
                        </small>
                    </p>
                @endif
            </div>
        </div>
    </div>
@endsection