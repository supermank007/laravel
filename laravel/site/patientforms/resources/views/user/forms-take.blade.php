@extends('layouts.app-no-sidebar')

@section('title', 'Form: ' . $form->name)

@section('sidebar')
    @parent

    <p>Sidebar content here.</p>
@endsection

@section('content')
    <div id='app'>
        <h1>Take Form</h1>
        <div class="panel panel-default">
            <div class="panel-body">
                <p>Form: {{ $form->name }}</p>
                <p>Description: {{ $form->description }}</p>
                <p># Questions: {{ $form->questions->count() }}</p>
                @if ($form->show_instructions)
                    <a href='{{ route('forms.instructions', ['form_id' => $form->id]) }}' class="btn btn-primary">Next</a>
                @else
                    <a href='{{ route('forms.takeQuestion', ['form_id' => $form->id, 'question_index' => 1]) }}' class="btn btn-primary">Begin</a>
                @endif
            </div>
        </div>
    </div>
@endsection