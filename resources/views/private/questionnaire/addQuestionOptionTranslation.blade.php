<div id="div{{ $inputId }}" class="questionOptionsWrapper div{{ $inputId }}">
    <div class="row">
        <div class="col-md-12">
            <input id="label_{{ $inputId }}_{!! $languageCode !!}"
                   name="label_{{ $inputId }}_{!! $languageCode !!}"
                   @if(!empty($questionOption))
                        value="@if(!empty(collect($questionOption->question_option_translations)->keyBy('language_code')[$languageCode])){!! collect($questionOption->question_option_translations)->keyBy('language_code')[$languageCode]->label!!}@endif"
                   @endif
                   class="form-control inline label_{!!$inputId !!}"
                   placeholder="@if(!empty($questionOption)){!! $questionOption->label !!} @else {!! trans("privateQuestionnaireAddQuestionOption.newOptionTranslations") !!} @endif"
                   style="margin-bottom:5px;">
        </div>
    </div>
    <hr>
</div>
