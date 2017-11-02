@extends('private._private.index')

@section('content')
    <div class="row">
        <div class="col-md-12">
            @php $form = ONE::form('newsletters',trans('privateNewsletters.details'),'notify','message_all_users')
                ->settings(["model" => isset($newsletter) ? $newsletter : null])
                ->show('PrivateNewslettersController@edit', 'PrivateNewslettersController@delete', ['id' => isset($newsletter) ? $newsletter->newsletter_key : null], 'PrivateNewslettersController@index')
                ->create('PrivateNewslettersController@store', 'PrivateNewslettersController@index', ['id' => isset($newsletter) ? $newsletter->newsletter_key : null])
                ->edit('PrivateNewslettersController@update', 'PrivateNewslettersController@show', ['id' => isset($newsletter) ? $newsletter->newsletter_key : null])
                ->open();
            @endphp

            @if(ONE::actionType('newsletters') == 'show' )
                <div class="pull-right">
                    <a href="{!! action('PrivateNewslettersController@sendNewsletter', ["id" => $newsletter->newsletter_key, 'flag' => '0']) !!}" class="btn btn-flat btn-info">
                        <i class="fa fa-check"></i>
                        {{trans('privateNewsletters.testNewsletter')}}
                    </a>&nbsp;
                    @if($newsletter->tested=='1')
                        <a class="btn btn-flat btn-danger active" data-toggle="modal" data-target="#sendNewsletterConfirmation">
                            <i class="fa fa-share"></i>
                            {{ trans('privateNewsletters.sendNewsletter') }}
                        </a>
                    @else
                        <a href="#" class="btn btn-flat btn-info disabled" title="{{ trans('privateNewsletters.test_needed_to_send_newsletter') }}">
                            <i class="fa fa-share"></i>
                            {{trans('privateNewsletters.sendNewsletter')}}
                        </a>
                    @endif
                </div>
            @endif

            {!! Form::oneText('title', trans('privateNewsletters.title'), isset($newsletter) ? $newsletter->title : null, ['class' => 'form-control', 'id' => 'title']) !!}
            {!! Form::oneText('subject', trans('privateNewsletters.subject'), isset($newsletter->subject) ? $newsletter->subject : null, ['class' => 'form-control', 'id' => 'subject', 'required']) !!}

            @if(ONE::verifyModuleAccess('q','q'))
                @if (ONE::actionType("newsletters")=="create")
                    <div class="form-group">
                        <label for="questionnaire">{{trans('privateNewsletters.questionnaire')}}</label>
                        <select id="questionnaire" name="questionnaire" class="select2-searchable form-control" style="width:100%;" data-placeholder="{!! trans("privateNewsletters.associateQuestionnaire") !!}">
                            <option value="null">{{trans('privateNewsletters.selectQuestionnaire')}}</option>
                            @foreach($questionnaires as $questionnaire)
                                <option value="{{$questionnaire->form_key}}" {{ isset($questionnaireKey) && $questionnaire->form_key == $questionnaireKey? 'selected':'' }}  >{!! $questionnaire->title !!}</option>
                            @endforeach
                        </select>
                        <div id="q-info" style="display: {{isset($questionnaireKey) ? 'block' : 'none'}}; margin-top: 5px">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="alert alert-info" role="alert">
                                        {{trans('privateNewsletters.nowCanUseTag')}}<strong> #questionnaire</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endif

            @if (ONE::actionType("newsletters")=="show")
                <dt><i class="fa fa-eye"></i> Preview</dt>
                <div style="border:1px solid #999999;width:100%;height:350px;overflow:auto;">{!! isset($newsletter->content) ? $newsletter->content : null !!}</div>
            @else
                {!! Form::oneTextArea('content', trans('privateNewsletters.content'), isset($newsletter->content) ? $newsletter->content : null, ['class' => 'form-control message-composer', 'id' => 'content']) !!}
            @endif

            {!! $form->make() !!}
        </div>
    </div>
    @if(ONE::actionType('newsletters') == 'show' && isset($newsletter) && $newsletter->tested=='1')
        <div id="sendNewsletterConfirmation" class="modal fade" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="card-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">{{ trans("privateNewsletters.confirm_newsletter_send_header") }}</h4>
                    </div>
                    <div class="modal-body">
                        {{ trans("privateNewsletters.confirm_newsletter_send_text") }}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">
                            {{ trans("privateNewsletters.cancel") }}
                        </button>
                        <a href="{!! action('PrivateNewslettersController@sendNewsletter', ["id" => $newsletter->newsletter_key, 'flag' => '1']) !!}" class="btn btn-danger">
                            {{ trans("privateNewsletters.confirm_newsletter_send") }}
                        </a>
                    </div>
                </div>

            </div>
        </div>

    @endif
@endsection

@section("scripts")
    <script src="{{ asset("js/tinymce/tinymce.min.js") }}"></script>
    <script>
        {!! ONE::addTinyMCE(".message-composer", ['action' => action('PrivateNewslettersController@getTinyMCE')]) !!}

        $('#questionnaire').change(function() {
            if ($('#questionnaire option:selected').val() != 'null'){
                $('#q-info').show();
            } else {
                $('#q-info').hide();
            }
        })
    </script>
@endsection