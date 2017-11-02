@extends('private._private.index')

@section('content')
    <div class="card flat topic-data-header">
        <p><label for="contentStatusComment" >{{trans('privateCbs.pad')}}</label>  {{$cb->title}}<br></p>
        <p><label for="contentStatusComment" >{{trans('privateCbs.author')}}</label>
            <a href="{{action('UsersController@show', ['userKey' => $author->user_key, 'role' => $author->role ?? null])}}">{{$author->name}}</a>
            <br>
        </p>
        <p><label for="contentStatusComment" >{{trans('privateCbs.start_date')}}</label>  {{$cb->start_date}}</p>
    </div>

    <div class="margin-top-20">
        @php
        $form = ONE::form('cbsQuestionnaires', trans('privateTopic.details'), 'cb', 'cbsQuestionnaires')
            ->settings(["model" => isset($topic) ? $topic : null, 'id'=>isset($topic) ? $topic->topic_key : null])
            ->show('CbsController@editQuestionnaires', null,
                ['type' => isset($type) ? $type : null, 'cbKey' =>isset($cb) ? $cb->cb_key : null],
                null)
            ->edit('CbsController@updateQuestionnaires', 'CbsController@showQuestionnaires',
                ['type' => $type,'cbKey' =>isset($cb) ? $cb->cb_key : null])
            ->open();
        @endphp

        {!! Form::hidden('title', isset($cb) ? $cb->title : null) !!}
        {!! Form::hidden('description', isset($cb) ? $cb->contents : null) !!}
        {!! Form::hidden('start_date', isset($cb) ? $cb->start_date : date('Y-m-d')) !!}
        {!! Form::hidden('end_date', isset($cb) && $cb->end_date!=null ? $cb->end_date  : '') !!}
        {!! Form::hidden('cb_key', isset($cb) ? $cb->cb_key : 0, ['id' => 'cb_key']) !!}

        @if(Session::get('user_role') == 'admin' || ONE::verifyUserPermissionsShow('cb', 'notifications'))
            <div class="card flat">
                <div class="card-title" style="padding: 10px;">{{trans('privateCbs.notifications')}}</div>
                <div class="card-body">
                    @foreach($actions as $action)
                        @if($action->code == 'create_topic' || $action->code == 'comment')
                            <div class="card flat margin-bottom-20">
                                <div class="card-header">
                                    <div>
                                        <a class="collapsed" role="button" data-toggle="collapse"
                                           href="#collapse_{{$action->code}}" aria-expanded="false"
                                           aria-controls="collapse_{{$action->code}}">
                                            {{$action->title}}
                                        </a>
                                    </div>
                                </div>
                                <div id="collapse_{{$action->code}}" class="panel-collapse collapse show" role="tabpanel">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-6 col-sm-6">
                                                {!! Form::oneSwitch("action[$action->code]",$action->title, in_array($action->code, (isset($cbQuestionnaires) ? array_keys($cbQuestionnaires) : [])) , array( "groupClass"=>"row", "labelClass" => "col-12", "switchClass" => "col-12 switchAction", "onchange" => "checkSwitch(this)")) !!}
                                            </div>
                                            <div class="col-6 col-sm-6" >
                                                <label for="questionnaire">{{trans('privateCbs.questionnaire')}}</label>
                                                <select id="questionnaire_{{$action->code}}" class="form-control questionnaire"  name="questionnaire[{{$action->code}}]" @if (ONE::actionType('cbsQuestionnaires') == "show") disabled @endif @if (in_array($action->code, (isset($cbQuestionnaires) ? array_keys($cbQuestionnaires) : []))) required @endif >
                                                    <option selected value="">{{trans('privateCbs.select_value')}}</option>
                                                    @if($questionnaire!=null)
                                                        @foreach($questionnaire as $key => $question)
                                                            <option value="{{$key}}"  @if(isset($cbQuestionnaires[$action->code]->questionnarie_key) && $cbQuestionnaires[$action->code]->questionnarie_key == $key) selected @endif>{{$question}}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row">
                                            <div class="col-6 col-sm-3">
                                                {!! Form::oneSwitch('ignore['.$action->code.']',trans("privateCbs.ignoreQuestionnaire"), isset($cbQuestionnaires[$action->code]) ? $cbQuestionnaires[$action->code]->ignore : 0 , ["groupClass"=>"row", "labelClass" => "col-12", "switchClass" => "col-12"]) !!}
                                            </div>
                                            <div class="col-6 col-sm-3">
                                                {!! Form::oneText('days_ignore['.$action->code.']', trans('privateCbs.days_to_ignore'),isset($cbQuestionnaires[$action->code]) ? $cbQuestionnaires[$action->code]->days_to_ignore : "0", ['class' => 'form-control', 'id' => 'days_ignore', "style" => 'width: 20%']) !!}
                                            </div>
                                            <div class="col-12 col-sm-6">
                                                <a class="btn btn-flat btn-success btn-translation" id="btn_translation_{{$action->code}}" style="margin-top: 25px;" @if (ONE::actionType('cbsQuestionnaires') == "show") disabled @endif>{{trans('privateCbs.modal_translations')}}</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @elseif($action->code == 'vote_event')
                            @foreach($cbVoteEvents as $cbVoteEvent)
                                <div class="card flat">
                                    <div class="card-header">
                                         <div>
                                            <a class="collapsed" role="button" data-toggle="collapse"
                                               href="#collapse_vote_{{ $cbVoteEvent->vote_key }}" aria-expanded="false"
                                               aria-controls="collapse_vote_{{ $cbVoteEvent->vote_key }}">
                                                {{$cbVoteEvent->name}}
                                            </a>
                                        </div>
                                    </div>
                                    <div id="collapse_vote_{{ $cbVoteEvent->vote_key }}" class="panel-collapse collapse show" role="tabpanel">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-6 col-sm-3">
                                                        {!! Form::oneSwitch("action[".$action->code."_".$cbVoteEvent->vote_key."]",$action->title, (in_array($action->code, (isset($cbQuestionnaires) ? array_keys($cbQuestionnaires) : [])) && (isset($cbQuestionnaires[$action->code]->{$cbVoteEvent->vote_key}))) , array( "groupClass"=>"row", "labelClass" => "col-12", "switchClass" => "col-12 switchAction", "onchange" => "checkSwitch(this)")) !!}
                                                    </div>
                                                    <div class="col-6 col-sm-3" >
                                                        <label for="questionnaire">{{trans('privateCbs.questionnaire')}}</label>
                                                        <select id="questionnaire_{{$action->code}}" class="form-control questionnaire"  name="questionnaire[{{$action->code}}_{{$cbVoteEvent->vote_key}}]" @if (ONE::actionType('cbsQuestionnaires') == "show") disabled @endif @if ((in_array($action->code, (isset($cbQuestionnaires) ? array_keys($cbQuestionnaires) : [])) && (isset($cbQuestionnaires[$action->code]->{$cbVoteEvent->vote_key})))) required @endif >
                                                            <option selected value="">{{trans('privateCbs.select_value')}}</option>
                                                            @if($questionnaire!=null)
                                                                @foreach($questionnaire as $key => $question)
                                                                    <option value="{{$key}}" @if((isset($cbQuestionnaires[$action->code]->{$cbVoteEvent->vote_key}->questionnarie_key) && $cbQuestionnaires[$action->code]->{$cbVoteEvent->vote_key}->questionnarie_key == $key)) selected @endif>{{$question}}</option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                    </div>
                                                </div>
                                                <br>
                                                <div class="row">
                                                    <div class="col-6 col-sm-3">
                                                        {!! Form::oneSwitch('ignore['.$action->code.'_'.$cbVoteEvent->vote_key.']',trans("privateCbs.ignoreQuestionnaire"), (isset($cbQuestionnaires[$action->code]->{$cbVoteEvent->vote_key}) ? $cbQuestionnaires[$action->code]->{$cbVoteEvent->vote_key}->ignore : 0), array("groupClass"=>"row", "labelClass" => "col-12", "switchClass" => "col-12") ) !!}
                                                    </div>
                                                    <div class="col-6 col-sm-3">
                                                        {!! Form::oneText('days_ignore['.$action->code.'_'.$cbVoteEvent->vote_key.']', trans('privateCbs.days_to_ignore'),((isset($cbQuestionnaires[$action->code]) && (isset($cbQuestionnaires[$action->code]->{$cbVoteEvent->vote_key})) ? $cbQuestionnaires[$action->code]->{$cbVoteEvent->vote_key}->days_to_ignore : "0")), ['class' => 'form-control', 'id' => 'days_ignore', "style" => 'width: 20%']) !!}
                                                    </div>
                                                    <div class="col-12 col-sm-6">
                                                        <a class="btn btn-flat btn-success btn-translation" id="btn_translation_{{$action->code}}_{{$cbVoteEvent->vote_key}}" style="margin-top: 25px;" @if (ONE::actionType('cbsQuestionnaires') == "show") disabled @endif>{{trans('privateCbs.modal_translations')}}</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                </div>
                            @endforeach
                        @endif
                    @endforeach
                    </div>
                </div>
            </div>
            <div class="modal fade cb_template" tabindex="-1" role="dialog" id="translationsModal" >

            </div>
        @endif
    {!! $form->make() !!}
@endsection

@section('scripts')
    <script>
        function checkSwitch(id) {
            var action = $(id).find("input").attr('id');
            var beginStr=action.indexOf("[");
            var EndStr=action.indexOf("]");
            var finalString=action.substring(beginStr+1,EndStr);

            if ($(id).find("input").is(":checked")){
                $('#questionnaire_'+finalString).prop('required',true);
            } else {
                $('#questionnaire_'+finalString).prop('required',false);
            }
        }

            @if (ONE::actionType('cbsQuestionnaires') == "edit"){
            $(".btn-translation").on("click", (function(event){

                var actionString = this.id;
                var vote_key = null;
                var action = '';

                if (actionString.includes("comment")){
                    action = 'comment';
                } else if (actionString.includes("create_topic")){
                    action = 'create_topic';
                } else if (actionString.includes("vote_event")) {
                    action = 'vote_event';
                }

                if (action === 'vote_event'){
                    vote_key = '{{$cbVoteEvent->vote_key ?? null}}'
                }

                $.ajax({
                    method: 'POST',
                    url: '{!!action("CbsController@editQuestionnaireTemplate", ['f' => 'questionnaireTemplate'])!!}',
                    data: {
                        cb_key:         '{{$cb->cb_key ?? null}}',
                        type:           '{{$type ?? null}}',
                        action_code:    action,
                        vote_key:       vote_key
                    },
                    success: function (response) {
                        $('#translationsModal').html(response);
                        $('#translationsModal').modal('show');
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                    }
                });
            }))
        }
            @endif


            @if (ONE::actionType('cbsQuestionnaires') == "show"){
            $(".btn-translation").on("click", (function(event){
                event.preventDefault()
            }))
        }

        @endif

    </script>

@endsection