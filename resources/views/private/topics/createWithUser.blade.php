@extends('private._private.index')

@section('header_scripts')
    <!-- Maps -->
    <script  src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCn-K_QLK1mNPM6SjCjnUl2e3neuQ9FX6Q&libraries=places" type="text/javascript"></script>
@endsection

@section('content')
    <div class="card flat topic-data-header margin-bottom-20" >
        <p><label for="contentStatusComment" style="">{{trans('privateCbs.pad')}}</label> {{$cb_title}}<br></p>
        <p><label for="contentStatusComment" style="">{{trans('privateCbs.author')}}</label>
            <a href="{{action('UsersController@show', ['userKey' => $cbAuthor->user_key, 'role' => $cbAuthor->role ?? null])}}">{{$cbAuthor->name}}</a>
            <br></p>
        <p><label for="contentStatusComment" style="">{{trans('privateCbs.start_date')}}</label> {{$cb_start_date}}</p>
    </div>


    <!-- Form -->
    @php
        $form = ONE::form('withUser',  trans("privateContentManager.generic_data") , 'cb', 'topics')
            ->settings(["model" => isset($topic) ? $topic : null, 'id'=>isset($topic) ? $topic->topic_key : null])
            ->show('TopicController@edit', 'TopicController@delete',
                ['type' => $type, 'cbKey' => $cbKey, 'topicKey' => isset($topic) ? $topic->topic_key : null],
                'CbsController@showTopics', ['type' => $type,'cbKey' => isset($cbKey) ? $cbKey : null], $edit ?? null, $delete ?? null)
            ->create('TopicController@storeWithUser', 'CbsController@showTopics' ,
                ['type'=> $type, 'cbKey' => isset($cbKey) ? $cbKey : null,'topicKey' => isset($topic) ? $topic->topic_key : null])
            ->edit('TopicController@update', 'TopicController@show',
                ['type' => $type,'cbKey' => $cbKey,'topicKey' => isset($topic) ? $topic->topic_key : null])
            ->open();
    @endphp

    <!-- Topic Details -->
    <div id="userData" class="card flat">
        <div class="card-header">
            <h4>{{ trans("privateTopics.user_data") }}</h4>
        </div>
        <div class="card-body">
            {!! Form::hidden('userData_role', "user", ['id' => 'role']) !!}
            <!-- User details -->
            {!! Form::oneText('userData_name', trans('user.name'), isset($user) ? $user->name  : null, ['class' => 'form-control', 'id' => 'name']) !!}
            {!! Form::oneText('userData_email', trans('user.email'), isset($user) ? $user->email  : null, ['class' => 'form-control', 'id' => 'email']) !!}

            <div class="row">
                <div class="col-12 col-md-5">
                    {!! Form::onePassword('userData_password', trans('privateUser.password'), null, ['class' => 'form-control', 'id' => 'userData_password',(ONE::actionType('withUser') == "create" ? 'required' : null)]) !!}
                </div>
                <div class="col-12 col-md-5">
                    {!! Form::onePassword('userData_password_confirmation', trans('privateUser.password_confirmation'), null, ['class' => 'form-control', 'id' => 'userData_password_confirmation',(ONE::actionType('withUser') == "create" ? 'required' : null)]) !!}
                </div>
                <div class="col-12 col-md-2">
                    <div class="form-group">
                        <br>
                        <input class="btn btn-secondary" onclick="generatePassword()" type="button" value="{{trans('privateUser.generate_random_password')}}" id="btn_generate_random_password">
                    </div>
                </div>
            </div>

            @if(isset($registerParameters))
                @foreach($registerParameters as $parameter)
                    @if($parameter['parameter_type_code'] == 'text' || $parameter['parameter_type_code'] == 'vat_number')
                        {!! Form::oneText("userData_" . $parameter['parameter_user_type_key'], $parameter['name'],
                            !empty($parameter['value']) ? $parameter['value'] : null,
                            ['class' => 'form-control', 'id' => $parameter['parameter_user_type_key'], ($parameter['mandatory'] == true ? 'required' : null)]) !!}
                    @elseif($parameter['parameter_type_code'] == 'text_area')
                        {!! Form::oneTextArea("userData_" . $parameter['parameter_user_type_key'], $parameter['name'],
                            !empty($parameter['value']) ? $parameter['value']:null,
                            ['class' => 'form-control', 'id' => $parameter['parameter_user_type_key'] , ($parameter['mandatory'] == true ? 'required' : null) ]) !!}
                    @elseif($parameter['parameter_type_code'] == 'radio_buttons')
                        @if(count($parameter['parameter_user_options'])> 0)
                            <div class="form-group">
                                <label for="{{$parameter['parameter_user_type_key']}}">{{ $parameter['name'] }}:@if($parameter['mandatory']) <span class="required-symbol">*</span> @endif</label>
                                @foreach($parameter['parameter_user_options'] as $option)
                                    <div class="radio">
                                        <label>
                                            <input @if(ONE::actionType('withUser') == "show") disabled @endif type="radio" name="userData_{{$parameter['parameter_user_type_key']}}" id="userData_{{$parameter['parameter_user_type_key']}}" value="{{$option['parameter_user_option_key']}}" @if($parameter['mandatory']) required @endif @if(!empty($option['selected']) && $option['selected']) checked @endif>{{$option['name']}}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            <hr style="margin: 10px 0 10px 0">
                        @endif
                    @elseif($parameter['parameter_type_code'] == 'check_box')
                        @if(count($parameter['parameter_user_options'])> 0)
                            @if(ONE::actionType('withUser') == "show")
                                @if(array_search(true, array_column($parameter['parameter_user_options'], 'selected')))
                                    <div class="form-group">
                                        <label for="{{$parameter['parameter_user_type_key']}}">{{ $parameter['name'] }}:@if($parameter['mandatory']) <span class="required-symbol">*</span> @endif</label>
                                        @foreach($parameter['parameter_user_options'] as $option)
                                            @if(!empty($option['selected']) && $option['selected'])
                                                <dd>&#9745;&nbsp;{{$option['name']}}</dd>
                                            @endif
                                        @endforeach
                                    </div>
                                    <hr style="margin: 10px 0 10px 0">
                                @endif
                            @else
                                <div class="form-group">
                                    <label for="{{$parameter['parameter_user_type_key']}}">{{ $parameter['name'] }}:@if($parameter['mandatory']) <span class="required-symbol">*</span> @endif</label>
                                    @foreach($parameter['parameter_user_options'] as $option)
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" value="{{$option['parameter_user_option_key']}}" name="userData_{{$parameter['parameter_user_type_key']}}[]" id="userData_{{$parameter['parameter_user_type_key']}}" @if($parameter['mandatory']) required @endif @if(!empty($option['selected']) && $option['selected']) checked @endif>{{$option['name']}}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                                <hr style="margin: 10px 0 10px 0">
                            @endif
                        @endif
                    @elseif($parameter['parameter_type_code'] == 'dropdown')
                        @if(ONE::actionType('withUser') == "show")
                            @if(array_search(true, array_column($parameter['parameter_user_options'], 'selected')))
                                <div class="form-group">
                                    <label for="{{$parameter['parameter_user_type_key']}}">{{ $parameter['name'] }}: @if($parameter['mandatory']) <span class="required-symbol">*</span> @endif</label>
                                    @foreach($parameter['parameter_user_options'] as $option)
                                        @if(!empty($option['selected']) && $option['selected'])
                                            <dd>{{$option['name']}}</dd>
                                        @endif
                                    @endforeach
                                </div>
                                <hr style="margin: 10px 0 10px 0">
                            @endif
                        @else
                            <div class="form-group">
                                <label for="{{$parameter['parameter_user_type_key']}}">{{ $parameter['name'] }}: @if($parameter['mandatory']) <span class="required-symbol">*</span> @endif</label>
                                <select class="form-control" id="{{$parameter['parameter_user_type_key']}}" name="userData_{{$parameter['parameter_user_type_key']}}" @if($parameter['mandatory']) required @endif>
                                    <option value="" selected>{{trans("publicUser.selectOption")}}</option>
                                    @foreach($parameter['parameter_user_options'] as $option)
                                        <option value="{{$option['parameter_user_option_key']}}" @if(!empty($option['selected']) && $option['selected']) selected @endif>{{$option['name']}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <hr style="margin: 10px 0 10px 0">
                        @endif
                    @elseif($parameter['parameter_type_code'] == 'birthday')
                        {!! Form::oneDate("userData_" . $parameter['parameter_user_type_key'], $parameter['name'], ( (!empty($parameter['value']) && $parameter['value'] != '') ? $parameter['value'] : date('Y-m-d')), ['class' => 'form-control oneDatePicker', 'id' => $parameter['parameter_user_type_key'], ($parameter['mandatory'] == true ? 'required' : null)]) !!}
                    @elseif($parameter['parameter_type_code'] == 'file' && !empty($parameter['value']))
                    <!-- This is not editable here we only see the photo or file to download -->
                        <div class="form-group" style="display:none">
                            <label for="{{$parameter['parameter_user_type_key']}}">{{ $parameter['name'] }}: @if($parameter['mandatory'])
                                    <span class="required-symbol">*</span> @endif</label>
                            <div class="box-tools dropFilesArea" id="{{$parameter['parameter_user_type_key']}}">
                                {!! ONE::fileSingleUploadBox("drop-zone", trans("cb.drag_and_drop_files_to_here") , 'user-file', 'files-list', (isset($parameter['value']['name']) ? $parameter['value']['name'] : null)) !!}
                            </div>
                            {!! Form::hidden("userData_" . $parameter['parameter_user_type_key'], (isset($parameter['value']['id']) ? $parameter['value']['id'] : null), ['id' => 'file_id']) !!}
                        </div>
                        <div class="form-group">
                            <label for="{{$parameter['parameter_user_type_key']}}">
                                {{ $parameter['name'] }}: @if($parameter['mandatory']) <span class="required-symbol">*</span> @endif
                            </label>
                            <div>
                                <a href="{{ action('FilesController@download',["id"=>$parameter['value']['id'], "code" => $parameter['value']['code'], 1, "inline" => 1])}}" target="_blank">
                                    {{ isset($parameter['value']['name']) ? $parameter['value']['name'] : null }}
                                </a>
                            </div>
                        </div>
                    @elseif($parameter['parameter_type_code'] == 'mobile')
                        <div class="form-group">
                            {!! Form::oneText("userData_" . $parameter['parameter_user_type_key'], $parameter['name'],
                                                        !empty($parameter['value']) ? $parameter['value'] : null,
                                                        ['class' => 'form-control', 'id' => $parameter['parameter_user_type_key'], ($parameter['mandatory'] == true ? 'required' : null), 'pattern'=> '\+?\s?[0-9\s]+' ]) !!}
                        </div>
                    @elseif($parameter['parameter_type_code'] == 'numeric')
                            {!! Form::oneNumber("userData_" . $parameter['parameter_user_type_key'], $parameter['name'],
                            !empty($parameter['value']) ? $parameter['value'] : null,
                            ['class' => 'form-control', 'id' => $parameter['parameter_user_type_key'], ($parameter['mandatory'] == true ? 'required' : null)]) !!}
                    @endif
                @endforeach
            @endif

            {!! Form::hidden('userData_confirmed', 1, ['id' => 'confirmed']) !!}
        </div>
    </div>
    <br>
    <div id="topicData" class="card flat">
        <div class="card-header">
            <h4>{{ trans("privateTopics.topic_data") }}</h4>
        </div>
        <div class="card-body">
            {!! Form::oneText('topicData_created_on_behalf',  array("name"=>trans('privateTopics.created_on_behalf'),"description"=>trans('privateTopics.created_on_behalfDescription')), isset($topic->created_on_behalf) ? $topic->created_on_behalf : null, ['class' => 'form-control', 'id' => 'created_on_behalf']) !!}
            {!! Form::oneText('topicData_title', array("name"=>trans('privateTopics.title'),"description"=>trans('privateTopics.titleDescription')), isset($topic) ? $topic->title : null, ['class' => 'form-control', 'id' => 'title', 'required' => 'required']) !!}

            {!! Form::oneTextArea('topicData_summary', array("name"=>trans('privateTopics.summary'),"description"=>trans('privateTopics.summaryDescription')), isset($topic) ? $topic->summary : null, ["size" => "30x1",'class' => 'form-control', 'id' => 'summary', 'style' => 'min-height:25px']) !!}

            {!! Form::oneTextArea('topicData_contents', array("name"=>trans('privateTopics.contents'),"description"=>trans('privateTopics.contentsDescription')), !empty($topic->contents) ? $topic->contents : ((isset($topic) && $topic->first_post->contents != null) ? $topic->first_post->contents : null), ["size" => "30x2",'class' => 'form-control tinyMCE', 'id' => 'contents', 'required' => 'required', 'style' => 'min-height:25px']) !!}

            @if(isset($type))
                @if($type == 'publicConsultation' || $type == 'tematicConsultation')
                    {!! Form::oneDate('topicData_start_date', trans('privateTopics.startDate'), isset($topic) ? $topic->start_date : null, ['class' => 'form-control oneDatePicker', 'id' => 'start_date']) !!}
                    {!! Form::oneDate('topicData_end_date', trans('privateTopics.endDate'), isset($topic) && $topic->end_date!=null ? $topic->end_date  : '', ['class' => 'form-control oneDatePicker', 'id' => 'end_date']) !!}
                @endif
                @if(isset($configurations) && (ONE::checkCBsOption($configurations, 'ALLOW-FILES')))
                    <div class="form-group">
                        <label for="title">{{ trans("cb.add_files") }}</label>
                        {!! ONE::fileSimpleUploadBox("drop-zone", trans("cb.drag_and_drop_files_to_here") , trans('PublicCbs.files'), 'select-files', 'files-list', 'files') !!}
                    </div>
                @endif
                @if(isset($configurations) && (ONE::checkCBsOption($configurations, 'TOPIC-ALLOW-EVENT-ASSOCIATION')))
                    <div class="form-group cbs-div">
                        <label>{{ trans("privateContentManager.select_the_pad") }}</label>
                        <select class="cbs" style="width:100%;"   >
                            <option value=""></option>
                        </select>
                    </div>
                    <div class="form-group topics-div" style="{{ isset($relatedParameter->fetchedTopics) ? '' : 'display:none;'}}">
                        <label for="topicData_parent_topic_key">{{ trans("privateContentManager.select_the_event") }}</label>
                        <select name="topicData_parent_topic_key" class="myTopics" style="width:100%;">
                            <option value=""></option>
                        </select>
                    </div>

                    <script>
                        $(".cbs").select2({
                            placeholder: '{{ trans("privateContentManager.select_the_pad") }}',
                            ajax: {
                                "url" : '{!! action('CbsController@getListOfCbsByType') !!}',
                                "type": "POST",
                                "data": function () {
                                    return {
                                        "_token": "{{ csrf_token() }}",
                                        "type":  "event", // search term
                                    };
                                },
                                processResults: function (data) {
                                    return {
                                        results: $.map(data, function(item) {
                                            return {
                                                text: item.title,
                                                id: item.cb_key
                                            }
                                        })
                                    };
                                }
                            }
                        });
                        $(document).on('change','.cbs',function(){
                            $(".myTopics").select2({
                                placeholder: '{{ trans("privateContentManager.select_the_topics") }}',
                                ajax: {
                                    "url" : '{!! action('CbsController@getListOfTopicsByCb') !!}',
                                    "type": "POST",
                                    "data": function () {
                                        return {
                                            "_token": "{{ csrf_token() }}",
                                            "cbKey":  $(".cbs").val(), // search term
                                        };
                                    },
                                    processResults: function (data) {
                                        return {
                                            results: $.map(data, function(item) {
                                                return {
                                                    text: item.title,
                                                    id: item.topic_key
                                                }
                                            })
                                        };
                                    }
                                }
                            });
                            $(".topics-div").show();
                        });
                    </script>
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
                                        {!! Form::oneSelect('topicData_parameter_'.$param['id'], isset($param['description']) ? ['name' => $param['name'], 'description' => $param['description']] : $param['name'], $param['options'], isset($topicParameters[$param['id']])? $topicParameters[$param['id']]->pivot->value : null, isset($topicParameters[$param['id']])? $param['id']['options'][$topicParameters[$param['id']]->pivot->value] : null, ['class' => 'form-control',($param['mandatory'] == 1)?'Required':''] ) !!}
                                    </div>
                                @elseif($param["code"] == "text")
                                    {{--<br>--}}
                                    <div class="col-sm-12">
                                        {!! Form::oneText('topicData_parameter_'.$param['id'], isset($param['description']) ? ['name' => $param['name'], 'description' => $param['description']] : $param['name'], isset($topicParameters[$param['id']])? $topicParameters[$param['id']]->pivot->value : null, ['class' => 'form-control',($param['mandatory'] == 1)?'Required':'']) !!}
                                    </div>

                                @elseif($param["code"] == "check_box")

                                    <div class="col-sm-12">
                                        @if(!empty($param["options"]))
                                            <label for="parameter_"{{ $param['id'] }}>{{$param['name']}}</label>

                                            @foreach ($param["options"] as $optionValue => $option)
                                                {!! Form::oneCheckbox('topicData_parameter_'.$param['id'] . '[]', $option, $optionValue, isset($topicParameters[$param["id"]]) ? str_contains($topicParameters[$param["id"]]->pivot->value,$optionValue) : false,['id'=>'parameter_'.$param['id'] . '_' . $optionValue,'class' => '',($param['mandatory'] == 1)?'Required':'']) !!}
                                            @endforeach
                                        @else
                                            {!! Form::oneCheckbox('topicData_parameter_'.$param['id'], $param['name'], 1, isset($topicParameters[$param['id']])? $topicParameters[$param['id']]->pivot->value : null,['id'=>'parameter_'.$param['id'],'class' => '',($param['mandatory'] == 1)?'Required':'']) !!}
                                        @endif
                                    </div>
                                @elseif($param["code"] == "going_to_pass")
                                    <div class="col-sm-12">
                                        <label for="parameter_{{ $param['id']}}">{{ $param['name'] }}</label>
                                        {!! Form::oneCheckbox('topicData_parameter_'.$param['id'], $param['description'] ?? '', 1, isset($topicParameters[$param['id']])? $topicParameters[$param['id']]->pivot->value : null,['id'=>'parameter_'.$param['id'],'class' => '',($param['mandatory'] == 1)?'Required':'']) !!}

                                    </div>
                                @elseif($param['code'] == 'numeric')
                                    {{--<br>--}}
                                    <div class="col-sm-12">
                                        {!! Form::oneNumber('topicData_parameter_'.$param['id'], $param['name'], isset($topicParameters[$param['id']])? $topicParameters[$param['id']]->pivot->value : null, ['class' => 'form-control',($param['mandatory'] == 1)?'Required':'']) !!}
                                    </div>
                                @elseif($param['code'] == 'coin')
                                    <div class="col-sm-12">
                                        {!! Form::oneNumber('topicData_parameter_'.$param['id'], $param['name'], isset($topicParameters[$param['id']])? $topicParameters[$param['id']]->pivot->value : null, ['class' => 'form-control',($param['mandatory'] == 1)?'Required':'']) !!}
                                    </div>
                                @elseif($param["code"] == "detail")
                                    <div class="col-sm-12">
                                        {!! Form::oneTextArea('topicData_parameter_'.$param['id'], $param['name'], isset($topicParameters[$param['id']])? $topicParameters[$param['id']]->pivot->value : null, ['class' => 'form-control', 'size' => '30x2', 'style' => 'resize: vertical',($param['mandatory'] == 1)?'Required':'']) !!}
                                    </div>
                                @elseif($param["code"] == "text_area")
                                    {{--<br>--}}
                                    <div class="col-sm-12">
                                        @if($param["id"] == 279)
                                            {!! Form::oneTextArea('topicData_parameter_'.$param['id'], $param['name'], isset($topicParameters[$param['id']])? $topicParameters[$param['id']]->pivot->value : null, ['class' => 'form-control tinyMCE', 'size' => '30x2', 'style' => 'resize: vertical',($param['mandatory'] == 1)?'Required':'']) !!}
                                        @else
                                            {!! Form::oneTextArea('topicData_parameter_'.$param['id'], $param['name'], isset($topicParameters[$param['id']])? $topicParameters[$param['id']]->pivot->value : null, ['class' => 'form-control', 'size' => '30x2', 'style' => 'resize: vertical',($param['mandatory'] == 1)?'Required':'']) !!}
                                        @endif
                                    </div>
                                @elseif($param['code'] == 'radio_buttons')
                                    {{--<br>--}}
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label for="parameterRadio_{!! $param['id'] !!}"> {!! $param['name'] !!}</label>
                                            @foreach($param['options'] as $key => $option)
                                                <div class="form-group">
                                                    <input type="radio" name="topicData_parameter_{!! $param['id'] !!}" value="{!!$key !!}"
                                                           {{($param['mandatory'] == 1)?'Required':''}}
                                                           {{isset($topicParameters[$param['id']])? ($topicParameters[$param['id']]->pivot->value == $key ? 'checked' : '') : ''}}
                                                           ><label> {!! $option !!}</label>
                                                </div>
                                            @endforeach
                                        </div>

                                    </div>
                                @elseif($param["code"] == 'google_maps')
                                    <div class="col-sm-12">
                                        {!! Form::oneMaps('parameter_'.(($param["mandatory"]==1) ? "required_" : "").$param['id'],"Maps",isset($param['value'])? $param['value'] : null,["required" => $param["mandatory"], "defaultLocation" => "38.7436213,-9.1952232", "enableSearch" => true]) !!}
                                    </div>
                                @elseif($param["code"] == "email")
                                    <div class="col-sm-12">
                                        <div class="form-group {{($param['mandatory'] == 1)? 'required' : null}}">
                                            <label for="inputEmailModal">{{$param['name']}}</label>
                                            <input type="email" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$" class="form-control"
                                                   id="inputEmailModal" placeholder="{{$param['name']}}" name="topicData_parameter_{{$param['id']}}" value="{{isset($param['value']) ? $param['value'] : null}}"
                                                    {{($param['mandatory'] == 1)? 'required' : null}}/>
                                        </div>
                                    </div>
                                @elseif($param["code"] == "rich_text")
                                    <div class="col-sm-12">
                                        {!! Form::oneTextArea('topicData_parameter_'.$param['id'], $param['name'], isset($topicParameters[$param['id']])? $topicParameters[$param['id']]->pivot->value : null, ['class' => 'form-control tinyMCE', 'size' => '30x2', 'style' => 'resize: vertical',($param['mandatory'] == 1)?'Required':'']) !!}
                                    </div>
                                @elseif($param["code"] == "parent_topics_rich_text")
                                    <div class="col-sm-12">
                                        {!! Form::oneTextArea('topicData_parameter_'.$param['id'], $param['name'], isset($topicParameters[$param['id']])? $topicParameters[$param['id']]->pivot->value : null, ['class' => 'form-control tinyMCE', 'size' => '30x2', 'style' => 'resize: vertical',($param['mandatory'] == 1)?'Required':'']) !!}
                                    </div>
                                @endif
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    {!! $form->make() !!}
@endsection


@section('scripts')

    <!-- Plupload Javascript fix and bootstrap fix @ start -->
    <link rel="stylesheet" href="/bootstrap/plupload-fix/bootstrap.css">
    <script src="/bootstrap/plupload-fix/bootstrap.min.js"></script>
    <!-- Plupload Javascript fix and bootstrap fix @ End -->
    <script src="{{ asset('vendor/jildertmiedema/laravel-plupload/js/plupload.full.min.js') }}"></script>
    <script src="{{ asset("js/cropper.min.js") }}"></script>
    <script src="{{ asset("js/tinymce/tinymce.min.js") }}"></script>

    @include('private._private.functions') {{-- Helper Functions --}}
    <script>
        {!! ONE::fileUploader('fileUploader', action('FilesController@upload'), 'ideaFileUploaded', 'select-files', 'drop-zone', 'files-list', 'files', 1, isset($uploadKey) ? $uploadKey : "", $allowFiles) !!}
        fileUploader.init();

        updateClickListener();

        updateFilesPostList('#files',1);
        {!! ONE::addTinyMCE(".tinyMCE", ['action' => action('ContentsController@getTinyMCE')]) !!}
    </script>

    <script>
        function updateStatus(topicKey){
            $('#topicKeyStatus').val(topicKey);
            $('#updateStatusModal').modal('show');
        }

        $('#updateStatusModal').on('show.bs.modal', function (event) {
            $('#updateStatus').off();
            $('#updateStatus').on('click', function (evt) {
                var allVals = {};
                var isValid = true;

                //get inputs to update status
                allVals['topicKey'] = $('#topicKeyStatus').val();
                $('#updateStatusModal input:text').each(function () {
                    if($(this).val().length > 0){
                        allVals[$(this).attr('name')] = $(this).val();
                    }
                });
                $('#updateStatusModal textarea').each(function () {
                    if($(this).val().length > 0){
                        allVals[$(this).attr('name')] = $(this).val();
                    }
                });
                $('#updateStatusModal select').each(function () {
                    if($(this).val().length > 0){
                        $(this).closest('.form-group').removeClass('has-error');
                        allVals[$(this).attr('name')] = $(this).val();
                    }else{
                        $(this).closest('.form-group').addClass('has-error');
                        isValid = false;
                    }
                });



                //all values ok to update
                if (isValid) {
                    $('#updateStatusModal input:text').each(function () {
                        $(this).val('');
                    });
                    $('#updateStatusModal textarea').each(function () {
                        $(this).val('');
                    });
                    $('#updateStatusModal select').each(function () {
                        $(this).closest('.form-group').removeClass('has-error');
                        $(this).val('');
                    });
                    $.ajax({
                        method: 'POST', // Type of response and matches what we said in the route
                        url: "{{action('TopicController@updateStatus',['type'=> $type,'cbKey'=>$cbKey])}}", // This is the url we gave in the route
                        data: allVals, // a JSON object to send back
                        success: function (response) { // What to do if we succeed

                            if (response != 'false') {
                                window.location.href = response;
                                toastr.success('{{ trans('privateCbs.update_topic_status_ok') }}', '', {timeOut: 3000,positionClass: "toast-bottom-right"});
                            }
                            $('#updateStatusModal').modal('hide');
                        },
                        error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                            $('#updateStatusModal').modal('hide');
                            toastr.error('{{ trans('privateCbs.error_updating_state_or_sending_email_to_user') }}', '', {timeOut: 3000,positionClass: "toast-bottom-right"});

                        }
                    });
                }
            });
            //clear inputs and close update status modal
            $('#closeUpdateStatus').on('click', function (evt) {
                $('#updateStatusModal input:text').each(function () {
                    $(this).val('');
                });
                $('#updateStatusModal textarea').each(function () {
                    $(this).val('');
                });
                $('#updateStatusModal select').each(function () {
                    $(this).val('');
                });

                $('#updateStatusModal').modal('hide');
            });
            {{--{!! session()->get('LANG_CODE').'json' !!}--}}
        });
    </script>

    <script>
        function generatePassword(){
            $("#generated_password").css('display', 'inline');
            var password = Math.random().toString(36).slice(-8);
            $("#generated_password").val(password)
            $("#userData_password").val(password);
            $("#userData_password_confirmation").val(password);
        }
    </script>

@endsection
