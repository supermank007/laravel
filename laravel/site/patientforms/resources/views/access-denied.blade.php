@extends('layouts.app-no-sidebar')

@section('title', 'Access Denied')

@section('sidebar')
    @parent

    <p>Sidebar content here.</p>
@endsection

@section('content')
    <div id='app'>
        <h1>Access Denied</h1>
        <div class="panel panel-default">
            <div class="panel-body">
                Sorry, you don't have permission to view this resource.
            </div>
        </div>
    </div>
@endsection