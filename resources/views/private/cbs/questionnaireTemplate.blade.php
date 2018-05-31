<div class="modal-dialog">
    <div class="modal-content">
        @php
        $form = ONE::form('questionnaireTemplate', null, null, null, 'edit')
            ->edit('CbsController@updateQuestionnaireTemplate', null, ['type' => $type,'cbKey' =>isset($cb) ? $cb->cb_key : null, 'actionCode'=>isset($actionCode) ? $actionCode : null, 'f'=>'cbsQuestionnaires', 'voteKey' => !empty($voteKey) ? $voteKey : null])
            ->open();
        @endphp
        <div class="card-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">{{trans("privateCbs.edit_translations")}}</h4>
        </div>
        <div class="modal-body">
            @if(count($languages) > 0)
                @foreach($languages as $language)
                    @php $form->openTabs('tab-translation-'. $language->code, $language->name); @endphp
                    <div style="padding:10px;">
                        @if (ONE::actionType('questionnaireTemplate') == "show")
                            {!! Form::textarea('content_'.$language->code, (isset($translations[$language->code]) ? $translations[$language->code]['content'] : null), ['class' => 'form-control mcEdit', 'id' => 'content_'.$language->code, 'DISABLED'] )!!}
                        @else
                            {!! Form::textarea('content_'.$language->code, (isset($translations[$language->code]) ? $translations[$language->code]['content'] : null), ['class' => 'form-control mcEdit', 'id' => 'content_'.$language->code] )!!}
                        @endif
                        <br>
                        {!! Form::oneText('accept_'.$language->code, trans('privateCbs.accept_modal'), (isset($translations[$language->code]) ? $translations[$language->code]['accept'] : null), ['class' => 'form-control', 'id' => 'accept_'.$language->code]) !!}
                        <br>
                        {!! Form::oneText('ignore_'.$language->code, trans('privateCbs.ignore_modal'), (isset($translations[$language->code]) ? $translations[$language->code]['ignore'] : null), ['class' => 'form-control', 'id' => 'ignore_'.$language->code]) !!}
                    </div>
                @endforeach
                @php $form->makeTabs(); @endphp
            @endif
        </div>
        {!! $form->make() !!}
    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>
    $(function() {
        $("form").submit(function(event) {
            if (event.target.name === 'questionnaireTemplate'){
                event.preventDefault();

                $.ajax({
                    method: 'POST',
                    url: '{!!action("CbsController@updateQuestionnaireTemplate")!!}',
                    data: {
                        cb_key:         '{{$cb->cb_key ?? null}}',
                        type:           '{{$type ?? null}}',
                        action_code:    '{{$actionCode ?? null}}',
                        vote_key:       '{{empty($voteKey) ? null : $voteKey}}',
                        parameters:     $(this).serializeArray()
                    },
                    success: function (response) {
                        $('#translationsModal').modal('hide');
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                    }
                });
            }
        });
    });
</script>
