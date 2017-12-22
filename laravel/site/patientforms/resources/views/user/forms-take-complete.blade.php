@extends('layouts.app-no-sidebar')

@section('title', 'Form completed: ' . $form->name)

@section('sidebar')
    @parent

    <p>Sidebar content here.</p>
@endsection

@section('content')
    <div id='app'>
        <h1>Form Completed</h1>
        <div class="panel panel-default">
            <div class="panel-body">
                <form action="@relative_route('forms.finish')">
                    <p>You have completed <strong>{{ $form->name }}</strong>.</p>
                </form>
            </div>
        </div>
    </div>
@endsection