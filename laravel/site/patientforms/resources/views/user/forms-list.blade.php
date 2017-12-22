@extends('layouts.app-no-sidebar')

@section('title', 'Forms')

@section('sidebar')
    @parent

    <p>Sidebar content here.</p>
@endsection

@section('content')
    <div id='app'>
        <h1>Form Listing</h1>
        @include('forms.form-listing', ['forms' => $forms])
    </div>
@endsection