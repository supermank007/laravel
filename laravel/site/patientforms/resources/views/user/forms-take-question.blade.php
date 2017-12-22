@extends('layouts.app-no-sidebar')

@section('title', $form->name . ': Question ' . $question_index)

@section('sidebar')
    @parent

    <p>Sidebar content here.</p>
@endsection

@section('content')
    <div id='app'>
        <h1>{{ $form->name }}</h1>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Question {{ $question_index }} (of {{ count($form->questions) }})</h3>
            </div>
            <div class="panel-body">
                @if ($errors->has('answer'))
                    <div class="alert alert-danger">This question is required.</div>
                @elseif ($errors->has('answer_text'))
                    <div class="alert alert-danger">This question is required.</div>
                @endif
                @if (isset($_GET['error']) && $_GET['error'] == "required")
                    <div class="alert alert-danger">This question is required.</div>
                @endif
                <div class="form-questions panel panel-default">
                    <div class='panel-body'>
                        <form action="" method="POST">
                            @include('forms.question')
                            <hr>
                            <input type='hidden' name='form_id' value='{{ $form->id }}'>
                            <div class="form-group">
                                @if ($question_index !== 1)
                                    <a
                                        href='{{ route('forms.takeQuestion', ['form_id' => $form->id, 'question_index' => $question_index - 1, 'back' => true]) }}'
                                        class="btn btn-default btn-lg">
                                            Back
                                    </a>
                                @endif
                                <input type="submit" class="btn btn-primary btn-lg pull-right"
                                    @if ($question_index === count($form->questions))
                                        value="Finish"
                                    @else
                                        value="Next"
                                    @endif
                                >
                                @if ( session('completed_forms') && in_array( $form->id, session('completed_forms') ) )
                                    <input
                                        name="go_to_review"
                                        type="submit"
                                        value="Review"
                                        class="btn btn-default btn-lg pull-right form-review-btn">
                                @endif
                            </div>
                        </form>
                     </div>
                </div>
            </div>
        </div>
    </div>
@endsection