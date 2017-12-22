@extends('layouts.app-no-sidebar')

@section('title', 'User Responses')

@section('sidebar')
    @parent

    <p>Sidebar content here.</p>
@endsection

@section('content')
    <div id='app'>
        <h1>Response Listing</h1>
        <div>
            @each('forms.response-listing', $responses, 'response')
        </div>
    </div>
@endsection