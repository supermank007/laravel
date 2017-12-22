@extends('layouts.app-no-sidebar')

@section('title', 'Auth Tokens...?')

@section('sidebar')
    @parent

    <p>Sidebar content here.</p>
@endsection

@section('content')
    <h1>Auth Tokens...?</h1>
    <div id="app">
        <passport-clients></passport-clients>
        <passport-authorized-clients></passport-authorized-clients>
        <passport-personal-access-tokens></passport-personal-access-tokens>
    </div>
@endsection