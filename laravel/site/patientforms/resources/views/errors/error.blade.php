@extends('layouts.app-no-sidebar')

@section('title', 'Error')

@section('content')
    <div id='app'>
        <h1>Error: {{ $exception->getStatusCode() }}</h1>
        <div class="panel panel-default">
            <div class="panel-body">
                <p>
                    There was an issue processing your request.
                </p>
                @if ($exception->getMessage() !== '')
                    <hr>
                    <p class="text-danger">
                        <small>
                            <strong>Message:</strong> {{ $exception->getMessage() }}
                        </small>
                    </p>
                @endif
            </div>
        </div>
    </div>
@endsection