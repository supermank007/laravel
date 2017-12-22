@extends('layouts.app-no-sidebar')

@section('title', 'Form Instructions: ' . $form->name)

@section('sidebar')
    @parent

    <p>Sidebar content here.</p>
@endsection

@section('content')
    <div id='app'>
        <h1>Form Instructions: {{ $form->name }}</h1>
        <div class="panel panel-default">
            <div class="panel-body">
                <h4>Instructions:</h4>
                <p>{{ $form->instructions }}</p>
                <hr>
                <a href='{{ route('forms.takeQuestion', ['form_id' => $form->id, 'question_index' => $question_index]) }}' class="btn btn-primary">{{ $question_index != 1 ? 'Resume' : 'Begin' }}</a>
            </div>
        </div>
    </div>
@endsection