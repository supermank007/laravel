@extends('layouts.app')

@section('title', 'Forms')

@section('sidebar')
    @parent

    <p>Sidebar content here.</p>
@endsection

@section('content')
    <h1>Form Listing</h1>
    <ul>
        @forelse ($forms as $form)
            <li>
                @each('forms.form-listing', $forms, 'form')
            </li>
        @empty
            <li>No forms</li>
        @endforelse
    </ul>
@endsection