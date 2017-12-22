<div class="response-listing panel panel-default">
<div class="panel-body">
    <small style="opacity:0.7">Response #{{ $response->id }}</small>
    <p>Form: {{ $response->form->name }}</p>
    <p>Responding User: {{ $response->user->email }}</p>
    <p>Submitted Date: @datetime($response->created_at)</p>
    <p>Answers:</p>
    <div class="response-answers">
        <ul>
            @foreach($response->form->questions()->orderBy('order')->get() as $question)
                <li><strong>{{ $question->label }}</strong>: {{ implode( ', ', $question->response_answer_values($response->id)) }}</li>
            @endforeach
        </ul>
    </div>
</div>
</div>