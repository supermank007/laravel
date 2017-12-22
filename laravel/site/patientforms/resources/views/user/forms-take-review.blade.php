@extends('layouts.app-no-sidebar')

@section('title', 'Review Form Submission: ' . $response->assignment->form->name)

@section('sidebar')
    @parent

    <p>Sidebar content here.</p>
@endsection

@section('content')
    <div id='app'>
        <h1>Review Form Submission</h1>
        <div class="panel panel-default">
            <div class="panel-body">
                <form action="@relative_route('forms.finish')" method="POST">
                    <p>Form: {{ $response->assignment->form->name }}</p>
                    <p>Description: {{ $response->assignment->form->description }}</p>
                    <p>Review:</p>
                    <div class="response-answers">
                        <table class="table table-striped">
                            <colgroup>
                                <col width="15%">
                                <col width="40%">
                                <col width="40%">
                                <col width="5%">
                            </colgroup>
                            <thead>
                                <th>Question #</th>
                                <th>Question</th>
                                <th>Answer(s)</th>
                                <th></th>
                            </thead>
                            @foreach($response->assignment->form->questionsSorted() as $i => $question)
                                @if ($question->wasSkippedInResponse($response))
                                    @continue
                                @endif
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td>{{ $question->label }}</td>
                                    <td>
                                        {{ implode( ', ', $question->response_answer_values($response->id)) }}
                                    </td>
                                    <td>
                                        @if (! $question->wasSkippedInResponse($response))
                                            <a class="btn btn-default btn-xs" href='{{ route("forms.takeQuestion",
                                                [
                                                    "form_id" => $response->assignment->form->id,
                                                    "question_index" => $i+1
                                                ],
                                                null
                                            ) }}'>Change</a>
                                        @else
                                            (Skipped)
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                    <input type='hidden' name='response' value='{{ $response->id }}'>
                    <input type='hidden' name='submit' value='1'>
                    <button class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
    </div>
@endsection