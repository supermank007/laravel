@extends('layouts.app-no-sidebar')

@section('title', 'Error')

@section('sidebar')
    @parent

    <p>Sidebar content here.</p>
@endsection

@section('content')
    <div id='app'>
        <h1>Error</h1>
        <div class="panel panel-default">
            <div class="panel-body">
                <span class="text-danger">{{ $error }}</span>
            </div>
        </div>
    </div>
@endsection