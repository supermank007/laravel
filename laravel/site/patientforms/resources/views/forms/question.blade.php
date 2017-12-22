<p>
    <strong>{{ $question->label }}</strong>
    @if ($question->required)
        <small class="text-warning">(Required)</small>
    @endif
</p>
<div class="form-answers">
    <ul>
        @foreach($question->answers()->orderBy('order')->get() as $answer)
            @include('forms.answer')
        @endforeach
    </ul>
</div>