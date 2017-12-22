@if ( $answer->type == 'radio' )
    <div class="form-answer form-group radio">
        <label>
            <input
                type='radio'
                name='answer'
                id='form_{{ $answer->question->form->id }}_question_{{ $answer->question->id }}_answer_{{ $answer->id }}'
                value='{{ $answer->id }}'
                @if (isset($current_response_answer_ids) && in_array($answer->id, $current_response_answer_ids))
                    checked
                @endif
            >
            {{ $answer->label }}
        </label>
    </div>
@elseif ( $answer->type == 'radio_text')
    <div class="form-answer form-group radio">
        <label>
            <input
                type='radio'
                name='answer'
                id='form_{{ $answer->question->form->id }}_question_{{ $answer->question->id }}_answer_{{ $answer->id }}'
                value='{{ $answer->id }}'
                @if (isset($current_response_answer_ids) && in_array($answer->id, $current_response_answer_ids))
                    checked
                @endif
                >
            {{ $answer->label }}
        </label>
        <textarea
            class='form-control'
            name='answer_text'
            rows='3'>{{ $current_response_answer_value }}</textarea>
    </div>
@elseif ( $answer->type == 'checkbox' )
    <div class="form-answer form-group checkbox">
        <label>
            <input
                type='checkbox'
                name='answer[]'
                id='form_{{ $answer->question->form->id }}_question_{{ $answer->question->id }}_answer_{{ $answer->id }}'
                value='{{ $answer->id }}'
                @if (isset($current_response_answer_ids) && in_array($answer->id, $current_response_answer_ids))
                    checked
                @endif
                >
            {{ $answer->label }}
        </label>
    </div>
@elseif ( $answer->type == 'text' )
    <div class="form-answer form-group">
        @if ($answer->label != '')
            <label for='answer'>
                {{ $answer-> label }}
            </label>
        @endif
        <input type='hidden'
               name='answer'
               value='{{ $answer->id }}'>
        <textarea
            class='form-control'
            name='answer_text'
            rows='3'>{{ $current_response_answer_value }}</textarea>
    </div>
@else
@endif