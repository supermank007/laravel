@extends('layouts.app-no-sidebar')

@section('title', 'Create New Program')

@section('sidebar')
    @parent

    <p>Sidebar content here.</p>
@endsection

@section('content')
    <div id='app'>
        <h1>Create New Program</h1>
        @if (count($errors) > 0)
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form
            @if (!isset($program))
                action="@relative_route('programs.store')"
            @else
                action="@relative_route('programs.update', ['program' => $program->id])"
            @endif
            method="POST">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" name="name" class="form-control" placeholder="Program name" value='{{ $program->name or ''}}'>
            </div>
            <div class="form-group">
                <label for="description">Description:</label>
                <textarea name="description" class="form-control" placeholder="Description">{{ $program->description or ''}}</textarea>
            </div>
            @if (isset($program))
                {{ method_field('PUT') }}
            @endif
            {{ csrf_field() }}
            <div class="form-group">
                <input type="submit" value="Submit" class="btn btn-default">
            </div>
        </form>
    </div>
@endsection