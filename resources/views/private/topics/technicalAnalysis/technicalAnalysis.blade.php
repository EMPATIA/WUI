@extends('private._private.index')


@section('header_styles')
    <style>
        th{
            color: black !important;
        }

        .table-title{
            margin: 10px;
            font-size: larger;
            font-weight: bold;
            color: #66a7dd;
        }
    </style>
@endsection

@section('content')


    <!-- TOPIC DETAILS - BEGIN -->


    <div class="col-xs-12">
        <div class="box-private">
            <div class="panel-group">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <div class="box-header">
                                {{trans("privateTechnicalAnalysis.click_to_view_topic_details") }}
                            </div>
                        </h4>
                    </div>
                    <div id="topicDetails">
                        <div class="box-body">
                            @if($type != 'event' && !empty($topic->created_on_behalf))
                                {!! Form::oneFieldShow('created_on_behalf',  array("name"=>trans('privateTopics.created_on_behalf'),"description"=>trans('privateTopics.created_on_behalfDescription')), isset($topic->created_on_behalf) ? $topic->created_on_behalf : null, ['class' => 'form-control', 'id' => 'created_on_behalf']) !!}
                            @endif
                            {!! Form::oneFieldShow('title', array("name"=>trans('privateTopics.title'),"description"=>trans('privateTopics.titleDescription')), isset($topic) ? $topic->title : null, ['class' => 'form-control', 'id' => 'title', 'required' => 'required']) !!}

                            {!! Form::oneFieldShow('summary', array("name"=>trans('privateTopics.summary'),"description"=>trans('privateTopics.summaryDescription')), isset($topic) ? $topic->summary : null, ["size" => "30x1",'class' => 'form-control', 'id' => 'summary', 'style' => 'min-height:25px']) !!}
                            

                            {!! Form::oneFieldShow('contents', array("name"=>trans('privateTopics.contents'),"description"=>trans('privateTopics.contentsDescription')), !empty($topic->contents) ? $topic->contents : ((isset($topic) && $topic->first_post->contents != null) ? $topic->first_post->contents : null), ["size" => "30x2",'class' => 'form-control tinyMCE', 'id' => 'contents', 'style' => 'min-height:25px']) !!}

                            {!! Form::oneFieldShow('topic_number', array("name"=>trans('privateTopics.topic_number'),"description"=>trans('privateTopics.topic_numberDescription')), isset($topic) ? $topic->topic_number : null, ['class' => 'form-control', 'id' => 'topic_number', 'required' => 'required']) !!}

                            <div class="form-group">
                                <label for="author">{{trans('privateCbs.author')}}</label>
                                <div id="author"  style="font-size:x-small">{{trans('privateTopics.authorDescription')}}</div>
                                <a href="{{action('UsersController@show', ['userKey' => $user->user_key, 'role' => $user->role ?? null])}}">{{$user->name}}</a>
                                <hr style="margin: 10px 0 10px 0">
                            </div>

                            @if(isset($type))
                                @if($type == 'publicConsultation' || $type == 'tematicConsultation')
                                    {!! Form::oneFieldShow('start_date', trans('privateTopics.startDate'), isset($topic) ? $topic->start_date : null, ['class' => 'form-control oneDatePicker', 'id' => 'start_date']) !!}
                                    {!! Form::oneFieldShow('end_date', trans('privateTopics.endDate'), isset($topic) && $topic->end_date!=null ? $topic->end_date  : '', ['class' => 'form-control oneDatePicker', 'id' => 'end_date']) !!}
                                @endif
                            @endif

                            @if( isset($filesByType->images) )
                                <div class="cbImages">
                                    @foreach($filesByType->images as $fileTmp)
                                        <img class="cbImages" src="{{action('FilesController@download', [$fileTmp->file_id, $fileTmp->file_code] )}}" style="height:250px;" />&nbsp;&nbsp;
                                    @endforeach
                                </div>
                            @endif

                            @if( isset($filesByType->videos) )
                                <div class="cbVideos">
                                    @foreach($filesByType->videos as $fileTmp)
                                        <a href="{{action('FilesController@download', [$fileTmp->file_id, $fileTmp->file_code] )}}" style="height:250px;" > {{ $fileTmp->name }} </a>,&nbsp;
                                    @endforeach
                                </div>
                            @endif

                            @if( isset($filesByType->docs) )
                                <div class="cbFiles">
                                    @foreach($filesByType->docs as $fileTmp)
                                        <a href="{{action('FilesController@download', [$fileTmp->file_id, $fileTmp->file_code] )}}" class="btn-submit" style="display: block"> {{ $fileTmp->name }} </a>&nbsp;
                                    @endforeach
                                </div>
                            @endif

                            @if(isset($relatedParameter) && !collect($relatedParameter)->isEmpty())
                                <input type="hidden" value="{{ isset($relatedParameter->id) ? $relatedParameter->id : $relatedParameter->first()->id }}" name="associated_id">
                                <div class="form-group">
                                    <label for="pad_type">{{ trans("privateContentManager.select_the_pad_type") }}</label>
                                    <select name="pad_type" class="cbTypes" style="width:100%;" >
                                        <option value=""></option>
                                        <option value="proposal" {{ (isset($relatedParameter->pad_type) && $relatedParameter->pad_type == 'proposal') ? 'selected' : '' }}>{{ trans("privateContentManager.proposal") }}</option>
                                    </select>
                                </div>
                                <div class="form-group cbs-div" style="{{ isset($relatedParameter->pad_key) ? '' : 'display:none;'}}">
                                    <label for="pad_key">{{ trans("privateContentManager.select_the_pad") }}</label>
                                    <select name="pad_key" class="cbs" style="width:100%;"   >
                                        <option value=""></option>
                                        @if(isset($relatedParameter->pad_key))
                                            <option value="{{ $relatedParameter->pad_key }}" selected>{{ \App\ComModules\CB::getCb($relatedParameter->pad_key)->title }}</option>
                                        @endif
                                    </select>
                                </div>
                                <div class="form-group topics-div" style="{{ isset($relatedParameter->fetchedTopics) ? '' : 'display:none;'}}">
                                    <label for="myTopics">{{ trans("privateContentManager.select_the_topics") }}</label>
                                    <select name="myTopics[]" class="myTopics" style="width:100%;" multiple>
                                        <option value=""></option>
                                        @if(isset($relatedParameter->fetchedTopics))
                                            @foreach($relatedParameter->fetchedTopics as $topic)
                                                <option value="{{$topic->topic_key}}" selected> {{$topic->title}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            @endif

                            @if(isset($parameters) && !empty($parameters))
                                @if(!($phases = collect($parameters)->where('code','=','topic_checkpoint_phase'))->isEmpty())
                                    <div class="topic-phases form-group" style="
                                     background: #eee;
                                     padding: 15px;
                                     border: 1px solid #ccc;
                                     margin-top: 35px;
                                ">
                                        <div class="form-group">
                                            <label>Phases</label>
                                        </div>
                                        @foreach ($phases as $param)
                                            <label for="parameter_{{ $param['id'] }}">{{$param['name']}}</label>
                                            {!! Form::oneCheckbox('parameter_'.$param['id'], $param['description'] ?? '', 1, isset($topicParameters[$param['id']])? ($topicParameters[$param['id']]->pivot->value == null ? 0 : $topicParameters[$param['id']]->pivot->value) : 0,['id'=>'parameter_'.$param['id'],'class' => '',($param['mandatory'] == 1)?'Required':'']) !!}
                                        @endforeach
                                    </div>
                                @endif
                                <div class="form-group">
                                    <div class="row">
                                        @foreach($parameters as $param)
                                            @if($param["code"] != "topic_checkpoint_phase")
                                                @if(!empty($param["options"]) && $param["code"] != "check_box" && $param["code"] != "topic_checkpoints")
                                                    <div class="col-sm-12">
                                                        {!! Form::oneSelect('parameter_'.$param['id'], isset($param['description']) ? ['name' => $param['name'], 'description' => $param['description']] : $param['name'], $param['options'], isset($topicParameters[$param['id']])? $topicParameters[$param['id']]->pivot->value : null, isset($topicParameters[$param['id']])? $param['id']['options'][$topicParameters[$param['id']]->pivot->value] : null, ['class' => 'form-control',($param['mandatory'] == 1)?'Required':'', 'disabled'] ) !!}
                                                    </div>
                                                @elseif($param["code"] == "text")
                                                    {{--<br>--}}
                                                    <div class="col-sm-12">
                                                        {!! Form::oneText('parameter_'.$param['id'], isset($param['description']) ? ['name' => $param['name'], 'description' => $param['description']] : $param['name'], isset($topicParameters[$param['id']])? $topicParameters[$param['id']]->pivot->value : null, ['class' => 'form-control',($param['mandatory'] == 1)?'Required':'']) !!}
                                                    </div>

                                                @elseif($param["code"] == "check_box")
                                                    <div class="col-sm-12">
                                                        @if(!empty($param["options"]))
                                                            <label for="parameter_"{{ $param['id'] }}>{{$param['name']}}</label>

                                                            @foreach ($param["options"] as $optionValue => $option)
                                                                {!! Form::oneCheckbox('parameter_'.$param['id'] . '[]', $option, $optionValue, isset($topicParameters[$param["id"]]) ? str_contains($topicParameters[$param["id"]]->pivot->value,$optionValue) : false,['id'=>'parameter_'.$param['id'] . '_' . $optionValue,'class' => '',($param['mandatory'] == 1)?'Required':'']) !!}
                                                            @endforeach
                                                        @else
                                                            {!! Form::oneCheckbox('parameter_'.$param['id'], $param['name'], 1, isset($topicParameters[$param['id']])? $topicParameters[$param['id']]->pivot->value : null,['id'=>'parameter_'.$param['id'],'class' => '',($param['mandatory'] == 1)?'Required':'']) !!}
                                                        @endif
                                                    </div>
                                                @elseif($param["code"] == "going_to_pass")
                                                    <div class="col-sm-12">
                                                        <label for="parameter_{{ $param['id']}}">{{ $param['name'] }}</label>
                                                        {!! Form::oneCheckbox('parameter_'.$param['id'], $param['description'] ?? '', 1, isset($topicParameters[$param['id']])? $topicParameters[$param['id']]->pivot->value : null,['id'=>'parameter_'.$param['id'],'class' => '',($param['mandatory'] == 1)?'Required':'']) !!}
                                                    </div>
                                                @elseif($param['code'] == 'numeric')
                                                    {{--<br>--}}
                                                    <div class="col-sm-12">
                                                        {!! Form::oneNumber('parameter_'.$param['id'], $param['name'], isset($topicParameters[$param['id']])? $topicParameters[$param['id']]->pivot->value : null, ['class' => 'form-control',($param['mandatory'] == 1)?'Required':'']) !!}
                                                    </div>
                                                @elseif($param['code'] == 'coin')
                                                    <div class="col-sm-12">
                                                        {!! Form::oneNumber('parameter_'.$param['id'], $param['name'], isset($topicParameters[$param['id']])? $topicParameters[$param['id']]->pivot->value : null, ['class' => 'form-control',($param['mandatory'] == 1)?'Required':'']) !!}
                                                    </div>
                                                @elseif($param["code"] == "detail")
                                                    <div class="col-sm-12">
                                                        {!! Form::oneFieldShow('parameter_'.$param['id'], $param['name'], isset($topicParameters[$param['id']])? $topicParameters[$param['id']]->pivot->value : null, ['class' => 'form-control', 'size' => '30x2', 'style' => 'resize: vertical',($param['mandatory'] == 1)?'Required':'']) !!}
                                                    </div>
                                                @elseif($param["code"] == "text_area")
                                                    {{--<br>--}}
                                                    <div class="col-sm-12">
                                                        @if($param["id"] == 279)
                                                            {!! Form::oneFieldShow('parameter_'.$param['id'], $param['name'], isset($topicParameters[$param['id']])? $topicParameters[$param['id']]->pivot->value : null, ['class' => 'form-control tinyMCE', 'size' => '30x2', 'style' => 'resize: vertical',($param['mandatory'] == 1)?'Required':'']) !!}
                                                        @else
                                                            {!! Form::oneFieldShow('parameter_'.$param['id'], $param['name'], isset($topicParameters[$param['id']])? $topicParameters[$param['id']]->pivot->value : null, ['class' => 'form-control', 'size' => '30x2', 'style' => 'resize: vertical',($param['mandatory'] == 1)?'Required':'']) !!}
                                                        @endif
                                                    </div>
                                                @elseif($param["code"] == "date")
                                                    {{--<br>--}}
                                                    <div class="col-sm-12">
                                                        {!! Form::oneDate('parameter_'.$param['id'], isset($param['description']) ? ['name' => $param['name'], 'description' => $param['description']] : $param['name'], isset($topicParameters[$param['id']])? $topicParameters[$param['id']]->pivot->value : null, ['class' => 'form-control oneDatePicker',($param['mandatory'] == 1)?'Required':'']) !!}
                                                    </div>
                                                @elseif($param["code"] == "hour")
                                                    {{--<br>--}}
                                                    <div class="col-sm-12">
                                                        {!! Form::oneTime('parameter_'.$param['id'], isset($param['description']) ? ['name' => $param['name'], 'description' => $param['description']] : $param['name'], isset($topicParameters[$param['id']])? $topicParameters[$param['id']]->pivot->value : null, ['class' => 'form-control oneTimePicker',($param['mandatory'] == 1)?'Required':'']) !!}
                                                    </div>
                                                @elseif($param['code'] == 'radio_buttons')
                                                    {{--<br>--}}
                                                    <div class="col-sm-12">
                                                        <div class="form-group">
                                                            <label for="parameterRadio_{!! $param['id'] !!}"> {!! $param['name'] !!}</label>
                                                            @foreach($param['options'] as $key => $option)
                                                                <div class="form-group">
                                                                    <input type="radio" name="parameter_{!! $param['id'] !!}" value="{!!$key !!}"
                                                                           {{($param['mandatory'] == 1)?'Required':''}}
                                                                           {{isset($topicParameters[$param['id']])? ($topicParameters[$param['id']]->pivot->value == $key ? 'checked' : '') : ''}}
                                                                           @if(ONE::actionType('topic') == 'show') disabled @endif><label> {!! $option !!}</label>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @elseif($param["code"] == 'google_maps')
                                                    <div class="col-sm-12">
                                                        {!! Form::oneMaps('parameter_'.(($param["mandatory"]==1) ? "required_" : "").$param['id'],"Maps",isset($param['value'])? $param['value'] : null,["required" => $param["mandatory"], "defaultLocation" => "38.7436213,-9.1952232", "enableSearch" => true]) !!}
                                                        @if($param['value']=="")
                                                            <br>
                                                            <h5 style="margin-left:5px; margin-top:5px;">{{trans('privateTopics.location')}}</h5>
                                                        @endif
                                                        <hr style="margin: 10px 0 10px 0">
                                                    </div>
                                                @elseif($param["code"] == "email")
                                                    <div class="col-sm-12">
                                                        <dt> {{isset($param['name']) ? $param['name'] : ""}} </dt>
                                                        <dd> {{isset($param['value']) ? $param['value'] : ""}} </dd>
                                                        <hr style="margin: 10px 0 10px 0">
                                                    </div>
                                                @elseif($param["code"] == "rich_text")
                                                    <div class="col-sm-12">
                                                        {!! Form::oneTextArea('parameter_'.$param['id'], $param['name'], isset($topicParameters[$param['id']])? $topicParameters[$param['id']]->pivot->value : null, ['class' => 'form-control tinyMCE', 'size' => '30x2', 'style' => 'resize: vertical',($param['mandatory'] == 1)?'Required':'']) !!}
                                                    </div>
                                                @elseif($param["code"] == "parent_topics_rich_text")
                                                    <div class="col-sm-12">
                                                        {!! Form::oneTextArea('parameter_'.$param['id'], $param['name'], isset($topicParameters[$param['id']])? $topicParameters[$param['id']]->pivot->value : null, ['class' => 'form-control tinyMCE', 'size' => '30x2', 'style' => 'resize: vertical',($param['mandatory'] == 1)?'Required':'']) !!}
                                                    </div>
                                                @endif
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- TOPIC DETAILS - END -->




    <div class="margin-top-20">
        @php
            if (ONE::actionType('technicalAnalysis') != 'create')
                $formTitle = trans('privateTechnicalAnalysis.technical_analysis_from_topic').' '.(isset($technicalAnalysis->topic_title) ? $technicalAnalysis->topic_title : null);
            else
                $formTitle = trans('privateTechnicalAnalysis.technical_analysis');

            if(!in_array(ONE::getUserKey(),["OKtxhee8gnkTyPVlWLVMfZkiHfApnS4G","HGqbDfHnfDxMFstQcpKZSl0XaG5XaNZ0","ReRUSLZs9RvZ1CBinzLPOF9xgeyWKnxS","welfX1NcdZyaAaOpkjziQQTKzPLKnzSv","KnDGHYbWwkmm40a6ki9CkpruWvDSxffP","AmawFZ1jiR92iBpoq7ycVy9eRzXIgERG","cvwZ6RbE8mIYHTEznMsdvIE1oVJydyTC","M3Z4doELUqgN0O2miDt8AkI19F7ULMKS","57UbDJ7lgzdRc0vnbqHqXOmRdLqq9Oll","xE2InyIXxgRvYZlOFYFqnWhcM49L56R0","Gv4OrMROG1MPLVuyuZM64StzzWosLEV7"]))
                $backButton  = 'TopicController@show';
            else
                $backButton  = 'CbsController@showTopics';

            $form = ONE::form('technicalAnalysis', $formTitle,'orchestrator', 'technical_evaluation')
                ->create('TechnicalAnalysisController@store', $backButton, ['type'=> $type,'cbKey' => isset($cbKey) ? $cbKey : null,'topicKey' => $topicKey ?? null])
                ->show('TechnicalAnalysisController@edit', 'TechnicalAnalysisController@delete',
                        ['type' => $type,'cbKey' => isset($cbKey) ? $cbKey : null,'topicKey' => isset($topicKey) ? $topicKey : null,'version' => isset($technicalAnalysis) ? $technicalAnalysis->version : null],$backButton, ['type' => $type,'cbKey' => isset($cbKey) ? $cbKey : null,$topicKey ?? null])

                ->edit('TechnicalAnalysisController@update', 'TechnicalAnalysisController@show',['type' => $type,'cbKey' => $cbKey,'topicKey' => isset($topicKey) ? $topicKey : null,'version' => isset($technicalAnalysis) ? $technicalAnalysis->version : null])
                ->open();
        @endphp

        @if(ONE::actionType('technicalAnalysis') == 'show')
            <div class="row">
                <div class="col-6">
                    {!! Form::select('technicalAnalysisVersions', $technicalAnalysisVersions ?? null, isset($technicalAnalysis) ? $technicalAnalysis->version : null, ['class' => 'form-control', 'id' => 'technicalAnalysisVersions']) !!} {{--save dif id--}}
                </div>
                <div class="col-6">
                    @if(!$technicalAnalysis->active)
                        {!! Form::button('<i class="fa fa-check"></i>&nbsp;' . trans('privateTechnicalAnalysis.activate_version'), ['class' => 'btn btn-flat btn-success', 'onclick' => "location.href='".action('TechnicalAnalysisController@activateVersion', [ 'type' => $type, 'cbKey' => $cbKey, 'topicKey' => $topicKey,'version' => $technicalAnalysis->version])."'" ]) !!}
                    @endif
                </div>
            </div>
        @endif

        @if(!empty($technicalAnalysisQuestions))
            <div class="table-title">
                {{trans('privateTechnicalAnalysis.questions_and_answers')}}
            </div>
            <table class="table table-responsive table-hover table-condensed">
                <thead>
                <tr>
                    <th>{{ trans('privateTechnicalAnalysis.accepted') }}</th>
                    <th>{{ trans('privateTechnicalAnalysis.questions') }}</th>
                    <th>{{trans('privateTechnicalAnalysis.answers')}}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($technicalAnalysisQuestions as $technicalAnalysisQuestion)
                    @if($technicalAnalysisQuestion->acceptable)
                        <tr>
                            <td width="10%">
                                {!! Form::oneSwitch("accepted[".$technicalAnalysisQuestion->tech_analysis_question_key."]", null, $technicalAnalysisQuestion->technical_analysis_question_answers[0]->accepted ?? 0, ['id' => 'accepted_'.$technicalAnalysisQuestion->tech_analysis_question_key, ONE::actionType('technicalAnalysis') != 'edit' ? 'disabled' : ''])!!}
                            </td>
                            <td width="45%">
                                {{isset($technicalAnalysisQuestion) ? $technicalAnalysisQuestion->question : null}}
                            </td>
                            <td width="45%">
                                {!! Form::oneTextArea('question_'.$technicalAnalysisQuestion->tech_analysis_question_key,null, isset($technicalAnalysisQuestion->technical_analysis_question_answers[0]) ? $technicalAnalysisQuestion->technical_analysis_question_answers[0]->value : null, ["size" => "30x2",'class' => 'form-control','style' => 'min-height:25px']) !!}
                            </td>
                        </tr>
                    @endif
                @endforeach
                </tbody>
            </table>
            <hr>

            <div class="table-title">
                {{trans('privateTechnicalAnalysis.details')}}
            </div>
            <table class="table table-responsive table-hover table-condensed">
                <thead>
                <tr>
                    <th>{{ trans('privateTechnicalAnalysis.detail') }}</th>
                    <th>{{trans('privateTechnicalAnalysis.detail_value')}}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($technicalAnalysisQuestions as $technicalAnalysisQuestion)
                    @if(!$technicalAnalysisQuestion->acceptable)
                        <tr>
                            <td width="25%">
                                {{isset($technicalAnalysisQuestion) ? $technicalAnalysisQuestion->question : null}}
                            </td>
                            <td width="75%">
                                {!! Form::oneTextArea('question_'.$technicalAnalysisQuestion->tech_analysis_question_key,null, isset($technicalAnalysisQuestion->technical_analysis_question_answers[0]) ? $technicalAnalysisQuestion->technical_analysis_question_answers[0]->value : null, ["size" => "30x2",'class' => 'form-control','style' => 'min-height:25px']) !!}
                            </td>
                        </tr>
                    @endif
                @endforeach
                </tbody>
            </table>
        @endif

        @if(ONE::actionType('technicalAnalysis') != 'show')
            <div class="form-group required">
                <dt>{{ trans("privateTechnicalAnalysis.decision_title") }}</dt>
                <br>
                <div class="btn-group" data-toggle="buttons">
                    <label class="btn btn-primary @if(($technicalAnalysis->decision??0)==-1) active @endif">
                        <input type="radio" name="decision" id="failed" value="-1" autocomplete="off"
                               @if(($technicalAnalysis->decision??0)==-1) checked @endif>
                        {{ trans("privateTechnicalAnalysis.rejected") }}
                    </label>
                    <label class="btn btn-primary @if(($technicalAnalysis->decision??0)==0) active @endif">
                        <input type="radio" name="decision" id="undetermined" value="0" autocomplete="off"
                               @if(($technicalAnalysis->decision??0)==0) checked @endif>
                        {{ trans("privateTechnicalAnalysis.not_valuated") }}
                    </label>
                    <label class="btn btn-primary @if(($technicalAnalysis->decision??0)==1) active @endif">
                        <input type="radio" name="decision" id="passed" value="1" autocomplete="off"
                               @if(($technicalAnalysis->decision??0)==1) checked @endif>
                        {{ trans("privateTechnicalAnalysis.accepted") }}
                    </label>
                </div>
            </div>
        @else
            <dt>{{ trans("privateTechnicalAnalysis.decision_title") }}</dt>
            <dd>
                @if(($technicalAnalysis->decision??0)==-1)
                    {{ trans("privateTechnicalAnalysis.rejected") }}
                @elseif(($technicalAnalysis->decision??0)==1)
                    {{ trans("privateTechnicalAnalysis.not_valuated") }}
                @else
                    {{ trans("privateTechnicalAnalysis.accepted") }}
                @endif
            </dd>
        @endif

        @if(ONE::actionType('technicalAnalysis') == 'show')
            <hr>
            <dt>{{ trans("privateTechnicalAnalysis.updated_by") }}</dt>
            <dd>
                <a href="{{action('UsersController@show', ['userKey' => $updated_by->user_key, 'role' => $updated_by->role ?? null])}}">
                    {{ $updated_by->name ?? null }}
                </a>
            </dd>
        @endif
        {!! $form->make() !!}
    </div>
@endsection

@section('scripts')
    <script>
        //get version selected and reload page to that version
        $("#technicalAnalysisVersions").change(function(){
            var version = this.value;
            var url = window.location.href;
            @if(!empty($actionUrl))
                url = '{{$actionUrl}}/'+version;
            @endif
                window.location = url;
        });

        $( document ).ready(function() {
            $( "td > hr" ).remove();
        });
    </script>
@endsection


