@extends('private._private.index')

@section('header_scripts')
    <!-- Maps -->
    <script  src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCn-K_QLK1mNPM6SjCjnUl2e3neuQ9FX6Q&libraries=places" type="text/javascript"></script>

    <!-- Plupload Javascript fix and bootstrap fix @ start -->
    <link rel="stylesheet" href="/bootstrap/plupload-fix/bootstrap.css">
    <script src="/bootstrap/plupload-fix/bootstrap.min.js"></script>
    <!-- Plupload Javascript fix and bootstrap fix @ End -->
    <script src="{{ asset('vendor/jildertmiedema/laravel-plupload/js/plupload.full.min.js') }}"></script>
    <script src="{{ asset("js/cropper.min.js") }}"></script>
@endsection

@section('content')
    <div class="card flat topic-data-header margin-bottom-20" >
        @if($type != 'event')
            <p><label for="contentStatusComment" style="">{{trans('privateCbs.pad')}}</label> {{$cb_title}}<br></p>
        @endif
        <p><label for="contentStatusComment" style="">{{trans('privateCbs.author')}}</label>
            <a href="{{action('UsersController@show', ['userKey' => $cbAuthor->user_key, 'role' => $cbAuthor->role ?? null])}}">{{$cbAuthor->name}}</a>
            <br></p>
        @if($type != 'event')
            <p><label for="contentStatusComment" style="">{{trans('privateCbs.start_date')}}</label> {{$cb_start_date}}</p>
        @endif
    </div>
    <div class="row">
        <div class="@if(ONE::actionType('topic')!='create')col-md-8 @endif col-sm-12">
            <!-- Form -->
            @php
                $form = ONE::form('topic',  trans("privateContentManager.generic_data") , 'cb', 'topics')
                    ->settings(["model" => isset($topic) ? $topic : null, 'id'=>isset($topic) ? $topic->topic_key : null])
                    ->show('TopicController@edit', 'TopicController@delete',
                        ['type' => $type, 'cbKey' => $cbKey, 'topicKey' => isset($topic) ? $topic->topic_key : null],
                        'CbsController@showTopics', ['type' => $type,'cbKey' => isset($cbKey) ? $cbKey : null], $edit ?? null, $delete ?? null)
                    ->create('TopicController@store', 'CbsController@showTopics' ,
                        ['type'=> $type, 'cbKey' => isset($cbKey) ? $cbKey : null,'topicKey' => isset($topic) ? $topic->topic_key : null])
                    ->edit('TopicController@update', 'TopicController@show',
                        ['type' => $type,'cbKey' => $cbKey,'topicKey' => isset($topic) ? $topic->topic_key : null])
                    ->open();
            @endphp


            @if(ONE::actionType('topic')=='show')
                <div class="margin-bottom-20">
                    <a class="btn btn-flat btn-preview" href="javascript:updateStatus('{{$topic->topic_key}}')" style="margin-right: 10px">{{trans('privateCbs.topic_status')}}</a>
                    <a class="btn btn-flat btn-preview" href="{{action("PublicTopicController@show",['cbKey'=>$cbKey,'topic_key'=> isset($topicKey) ? $topicKey : null,'type'=>$type])}}"  target="_blank">
                        <i class="fa fa-eye"></i> {{ trans('privateCbs.preview') }}
                    </a>
                </div>


                @if(isset($topic->versions) && $topic->versions!== null)
                    <div class="form-group margin-bottom-20 margin-top-20">
                        <div class="row">
                            <div class="col-md-6" >
                                <select name="versions" onchange="location = this.value;"  class="form-control" style="font-family: 'FontAwesome','Open Sans', sans-serif!important;">
                                    @foreach ($topic->versions as $topicVersion)
                                        <option value="{{ action('TopicController@show',['type' => $type,'cbKey' => $cbKey,'topicKey' => isset($topic) ? $topic->topic_key : null, 'version' =>$topicVersion->version])}}" @if($topicVersion->version == $topic->version) selected @endif>
                                            {{ trans("privateContentManager.version") }} {{ $topicVersion->version }} ({{ \Carbon\Carbon::parse($topicVersion->created_at->date)->format("Y-m-d H:i") }})
                                            @if($topicVersion->version==$topic->version)
                                                &#xf06e;
                                            @endif
                                            @if($topicVersion->active==1)
                                                &#xf00c;
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6">
                                @if ($topic->active=="1")
                                    <a class="btn btn-flat btn btn-danger" href="{{ action("TopicController@changeActiveVersionStatus", ['type' => $type,'cbKey' => $cbKey,"topicKey" => isset($topic) ? $topic->topic_key : null,"version" => $topic->version, "status" => 0]) }}">
                                        <i class="fa fa-times"></i> {{ trans('form.disable') }}
                                    </a>
                                @else
                                    <a class="btn btn-flat btn btn-success" href="{{ action("TopicController@changeActiveVersionStatus", ['type' => $type,'cbKey' => $cbKey,"topicKey" => isset($topic) ? $topic->topic_key : null,"version" => $topic->version, "status" => 1]) }}">
                                        <i class="fa fa-check"></i> {{ trans('form.active') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            @endif

        <!-- Topic Details -->
            @if($type != 'event')
                {{--  @if(ONE::actionType("topic")=="create")
                    <div class="form-group">
                        <label for="topic_creator">{{ trans("privateTopics.topic_creator") }}</label>
                        <select name="topic_creator" id="topic_creator" style="width:100%;" ></select>
                    </div>
                @endif  --}}
                {!! Form::oneText('created_on_behalf',  array("name"=>trans('privateTopics.created_on_behalf'),"description"=>trans('privateTopics.created_on_behalfDescription')), isset($topic->created_on_behalf) ? $topic->created_on_behalf : null, ['class' => 'form-control', 'id' => 'created_on_behalf']) !!}
            @endif
            {!! Form::oneText('title', array("name"=>trans('privateTopics.title'),"description"=>trans('privateTopics.titleDescription')), isset($topic) ? $topic->title : null, ['class' => 'form-control', 'id' => 'title', 'required' => 'required']) !!}

            {!! Form::oneTextArea('contents', array("name"=>trans('privateTopics.contents'),"description"=>trans('privateTopics.contentsDescription')), !empty($topic->contents) ? $topic->contents : ((isset($topic) && $topic->first_post->contents != null) ? $topic->first_post->contents : null), ["size" => "30x2",'class' => 'form-control tinyMCE', 'id' => 'contents', 'style' => 'min-height:25px']) !!}

            @if(ONE::actionType('topic')=='show')
                {!! Form::oneText('topic_number', array("name"=>trans('privateTopics.topic_number'),"description"=>trans('privateTopics.topic_numberDescription')), isset($topic) ? $topic->topic_number : null, ['class' => 'form-control', 'id' => 'topic_number', 'required' => 'required']) !!}

                {{--{!! Form::oneText('topic_author', array("name"=>trans('privateTopics.author'),"description"=>trans('privateTopics.authorDescription')), isset($user->name) ? "<a href='".action('UsersController@show', ['userKey' => $user->user_key, 'role' => $user->role ?? null])."'>" .$user->name . "</a>" : null, ['class' => 'form-control', 'id' => 'topic_author']) !!}--}}

            <!-- update status modal -->
                <div class="modal fade" tabindex="-1" role="dialog" id="updateStatusModal" >
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="card-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title">{{trans("privateCbs.update_status")}}</h4>
                            </div>
                            <div style="margin-left:20px;">
                                <h5>{{trans('privateCbs.pad')}} : {{$cb_title}}</h5>
                            </div>
                            <div class="modal-body">
                                <div class="card flat">
                                    {!! Form::hidden('topicKeyStatus','', ['id' => 'topicKeyStatus']) !!}
                                    <div class="card-header">{{trans('privateCbs.select_option')}}</div>
                                    <div class="card-body">
                                        <div class="form-group ">
                                            <label for="status_type_code">{{trans('privateCbs.status_types')}}</label>
                                            <div for="status_type_code"  style="font-size:x-small">{{trans('privateCbs.status_typesDescription')}}</div>
                                            <select id="status_type_code" class="form-control" name="status_type_code">
                                                <option selected="selected" value="">{{trans('privateCbs.select_value')}}</option>
                                                <option  value="0">{{trans('privateCbs.withoutstatus')}}</option>
                                                @foreach($statusTypes as $key => $statusType)
                                                    <option value="{{$key}}">{{$statusType}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="contentStatusComment">{{trans('privateCbs.private_comment')}}</label>
                                            <div for="contentStatusComment"  style="font-size:x-small">{{trans('privateCbs.private_commentDescription')}}</div>
                                            <textarea class="form-control" rows="3" id="contentStatusComment" name="contentStatusComment" style="resize: none;"></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label for="contentStatusPublicComment">{{trans('privateCbs.public_comment')}}</label>
                                            <div for="contentStatusPublicComment"  style="font-size:x-small">{{trans('privateCbs.public_commentDescription')}}</div>
                                            <textarea class="form-control" rows="3" id="contentStatusPublicComment" name="contentStatusPublicComment" style="resize: none;"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" id="closeUpdateStatus">{{trans("privateCbs.close")}}</button>
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <button type="button" class="btn btn-primary" id="updateStatus">{{trans("privateCbs.save_changes")}}</button>
                            </div>
                        </div><!-- /.modal-content -->
                    </div><!-- /.modal-dialog -->
                </div><!-- /.modal -->
            @endif

            @if(isset($type))
                @if($type == 'publicConsultation' || $type == 'tematicConsultation')
                    {!! Form::oneDate('start_date', trans('privateTopics.startDate'), isset($topic) ? $topic->start_date : null, ['class' => 'form-control oneDatePicker', 'id' => 'start_date']) !!}
                    {!! Form::oneDate('end_date', trans('privateTopics.endDate'), isset($topic) && $topic->end_date!=null ? $topic->end_date  : '', ['class' => 'form-control oneDatePicker', 'id' => 'end_date']) !!}
                @endif
                @if(ONE::actionType('topic') != 'show' &&isset($configurations) && (ONE::checkCBsOption($configurations, 'ALLOW-FILES')))
                    {{--
                    <div class="form-group">
                        {!! Form::oneFileUpload("files", trans("cb.add_files"), !empty($jsonFileList[1]) ? $jsonFileList[1] : [], isset($uploadKey) ? $uploadKey : "",
                                                         ["name" => "files[1]", "acceptedtypes"=> $allowFiles] ) !!}
                    </div>
                    --}}
                @endif
            @endif

            {{--
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
            --}}

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
                            @if(ONE::actionType('topic') == 'show')
                                <label for="parameter_{{ $param['id'] }}">{{$param['name']}}</label>
                            @endif
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
                                        {!! Form::oneSelect('parameter_'.$param['id'], isset($param['description']) ? ['name' => $param['name'], 'description' => $param['description']] : $param['name'], $param['options'], isset($topicParameters[$param['id']])? $topicParameters[$param['id']]->pivot->value : null, isset($topicParameters[$param['id']])? $param['id']['options'][$topicParameters[$param['id']]->pivot->value] : null, ['class' => 'form-control',($param['mandatory'] == 1)?'Required':''] ) !!}
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
                                                {!! Form::oneCheckbox('parameter_'.$param['id'] . '[]', $option, $optionValue, isset($topicParameters[$param["id"]]) ? str_contains($topicParameters[$param["id"]]->pivot->value,$optionValue) : false,['id'=>'parameter_'.$param['id'] . '_' . $optionValue,'class' => '']) !!}
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
                                        {!! Form::oneNumber('parameter_'.$param['id'], $param['name'], isset($topicParameters[$param['id']])? $topicParameters[$param['id']]->pivot->value : null, ['class' => 'form-control',($param['mandatory'] == 1)?'required':'required']) !!}
                                    </div>
                                @elseif($param['code'] == 'coin')
                                    <div class="col-sm-12">
                                        {!! Form::oneNumber('parameter_'.$param['id'], $param['name'], isset($topicParameters[$param['id']])? $topicParameters[$param['id']]->pivot->value : null, ['class' => 'form-control',($param['mandatory'] == 1)?'Required':'required']) !!}
                                    </div>
                                @elseif($param["code"] == "detail")
                                    <div class="col-sm-12">
                                        {!! Form::oneTextArea('parameter_'.$param['id'], $param['name'], isset($topicParameters[$param['id']])? $topicParameters[$param['id']]->pivot->value : null, ['class' => 'form-control', 'size' => '30x2', 'style' => 'resize: vertical',($param['mandatory'] == 1)?'Required':'']) !!}
                                    </div>
                                @elseif($param["code"] == "text_area")
                                    {{--<br>--}}
                                    <div class="col-sm-12">
                                        @if($param["id"] == 279)
                                            {!! Form::oneTextArea('parameter_'.$param['id'], $param['name'], isset($topicParameters[$param['id']])? $topicParameters[$param['id']]->pivot->value : null, ['class' => 'form-control tinyMCE', 'size' => '30x2', 'style' => 'resize: vertical',($param['mandatory'] == 1)?'Required':'']) !!}
                                        @else
                                            {!! Form::oneTextArea('parameter_'.$param['id'], $param['name'], isset($topicParameters[$param['id']])? $topicParameters[$param['id']]->pivot->value : null, ['class' => 'form-control', 'size' => '30x2', 'style' => 'resize: vertical',($param['mandatory'] == 1)?'Required':'']) !!}
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
                                        @if(ONE::actionType('topic') == 'show') <hr style="margin: 10px 0 10px 0"> @endif

                                    </div>
                                @elseif($param["code"] == 'google_maps')
                                    <div class="col-sm-12">
                                        {!! Form::oneMaps('parameter_'.(($param["mandatory"]==1) ? "required_" : "").$param['id'],"Maps",isset($param['value'])? $param['value'] : null,["required" => $param["mandatory"], "defaultLocation" => "38.7436213,-9.1952232", "enableSearch" => true]) !!}
                                        @if(ONE::actionType('topic') == 'show')
                                            @if($param['value']=="")
                                                <br>
                                                <h5 style="margin-left:5px; margin-top:5px;">{{trans('privateTopics.location')}}</h5>
                                            @endif
                                            <hr style="margin: 10px 0 10px 0">
                                        @endif
                                    </div>
                                @elseif($param["code"] == "email")
                                    @if(ONE::actionType('topic') != 'show')
                                        <div class="col-sm-12">
                                            <div class="form-group {{($param['mandatory'] == 1)? 'required' : null}}">
                                                <label for="inputEmailModal">{{$param['name']}}</label>
                                                <input type="email" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$" class="form-control"
                                                       id="inputEmailModal" placeholder="{{$param['name']}}" name="parameter_{{$param['id']}}" value="{{isset($param['value']) ? $param['value'] : null}}"
                                                        {{($param['mandatory'] == 1)? 'required' : null}}/>
                                            </div>
                                        </div>
                                    @else
                                        <div class="col-sm-12">
                                            <dt> {{isset($param['name']) ? $param['name'] : ""}} </dt>
                                            <dd> {{isset($param['value']) ? $param['value'] : ""}} </dd>
                                            <hr style="margin: 10px 0 10px 0">
                                        </div>
                                    @endif
                                @elseif($param["code"] == "rich_text")
                                    <div class="col-sm-12">
                                        {!! Form::oneTextArea('parameter_'.$param['id'], $param['name'], isset($topicParameters[$param['id']])? $topicParameters[$param['id']]->pivot->value : null, ['class' => 'form-control tinyMCE', 'size' => '30x2', 'style' => 'resize: vertical',($param['mandatory'] == 1)?'Required':'']) !!}
                                    </div>
                                @elseif($param["code"] == "parent_topics_rich_text")
                                    <div class="col-sm-12">
                                        {!! Form::oneTextArea('parameter_'.$param['id'], $param['name'], isset($topicParameters[$param['id']])? $topicParameters[$param['id']]->pivot->value : null, ['class' => 'form-control tinyMCE', 'size' => '30x2', 'style' => 'resize: vertical',($param['mandatory'] == 1)?'Required':'']) !!}
                                    </div>
                                @elseif($param["code"] == "default_image")
                                    <div class="col-sm-12">
                                        {{ Form::oneFileUpload('parameter_'.$param['id'], $param['name'], isset($topicParameters[$param['id']])? json_decode($topicParameters[$param['id']]->pivot->value) : null, !empty($uploadKey) ? $uploadKey :"", array("max_file_size"=>"10mb", "acceptedtypes"=> "images", "multi_selection" => false, "replacefile" => true) ) }}                                    </div>
                                @elseif($param["code"] == "files")
                                    <div class="col-sm-12">
                                        {{ Form::oneFileUpload('parameter_'.$param['id'], $param['name'], isset($topicParameters[$param['id']])? json_decode($topicParameters[$param['id']]->pivot->value) : null, !empty($uploadKey) ? $uploadKey :"", array("max_file_size"=>"200mb") ) }}
                                    </div>
                                @elseif($param["code"] == "images")
                                    <div class="col-sm-12">
                                        {{ Form::oneFileUpload('parameter_'.$param['id'], $param['name'], isset($topicParameters[$param['id']])? json_decode($topicParameters[$param['id']]->pivot->value) : null, !empty($uploadKey) ? $uploadKey :"", array("max_file_size"=>"10mb","acceptedtypes"=> "images",) ) }}
                                    </div>
                                @endif
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif
            {!! $form->make() !!}

            @if(ONE::actionType('topic') == 'show')
                <div class="box-private">
                    <div class="box-header">
                        <div style="font-weight: bold; font-size: large; margin-bottom: 15px;">{{trans('privateTopics.proponents')}}</div>
                    </div>
                    <div class="box-body">
                        <div class="dataTables_wrapper dt-bootstrap no-footer">
                            <table id="cooperators_list" class="table table-responsive table-hover table-striped">
                                <thead>
                                <tr>
                                    <th>{{ trans('privateCbs.name') }}</th>
                                    <th>{{ trans('privateCbs.status') }}</th>
                                    <th>{{trans('privateCbs.permissions')}}</th>
                                    <th>
                                        <button class="btn btn-flat btn-sm btn-create fa fa-plus" onclick="showUsersModal()"></button>
                                        {{--<button class="btn btn-flat btn-sm btn-create fa fa-plus" data-toggle="modal" data-target="#cooperatorModal"></button>--}}
                                    </th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        <div class="col-md-4 col-sm-12">
            <div class="col-xs-12">
                @if(ONE::actionType('topic') == 'show')
                    <div class="box-private">
                        <div class="box-header">
                            <div style="font-weight: bold; font-size: large; margin-bottom: 15px;">{{trans('privateTopics.owner')}}</div>
                        </div>
                        <div class="box-body">
                            @if(!empty($user))
                                <div class="col-md-12" style="text-align: left;padding-left: 0; margin-bottom: 15px;">
                                    <a href="{{ action('UsersController@showUserMessages',['userKey' => $user->user_key, 'type'=> $type, 'cbKey' => isset($cbKey) ? $cbKey : null,'topicKey' => isset($topic) ? $topic->topic_key : null]) }}" class="btn btn-flat empatia" style="position: relative;">
                                        <i class="fa fa-envelope-o" aria-hidden="true"></i>
                                        {{trans('privateTopics.send_message_to_user')}}
                                        @if(isset($user_messages) and $user_messages > 0)
                                            <span class="user-messages-notification">{{ $user_messages }}</span>
                                        @endif
                                    </a>
                                </div>
                                {{--{!! Form::oneText('name', trans('privateTopics.name'), $user->name ?? null, ['class' => 'form-control', 'id' => 'name']) !!}--}}
                                <div class="form-group">
                                    <label for="author">{{trans('privateTopics.name')}}</label>
                                    <br>
                                    <a href="{{action('UsersController@show', ['userKey' => $user->user_key, 'role' => $user->role ?? "user"])}}">{{$user->name}}</a>
                                    <hr style="margin: 10px 0 10px 0">
                                </div>
                                {!! Form::oneText('email', trans('privateTopics.email'), $user->email ?? null, ['class' => 'form-control', 'id' => 'email']) !!}
                                {!! Form::oneDate('created_at', trans('privateTopics.created_at'), $topic->created_at ?? null, ['class' => 'form-control', 'id' => 'created_at']) !!}
                            @else
                                {!! Form::oneText('name', trans('privateTopics.name'), trans('privateTopics.anonymous'), ['class' => 'form-control', 'id' => 'name']) !!}
                                {!! Form::oneDate('created_at', trans('privateTopics.created_at'), $topic->created_at ?? null, ['class' => 'form-control', 'id' => 'created_at']) !!}
                            @endif
                        </div>
                    </div>
                @endif
            </div>
            <div class="col-xs-12">
                @if(ONE::actionType('topic') == 'show')

                    @if(isset($hasAnalysis) && $hasAnalysis)
                        <div class="box-private">
                            <div class="box-header">
                                <div style="font-weight: bold; font-size: large; margin-bottom: 15px;">{{trans('privateTopics.technical_analyses')}}</div>
                            </div>
                            <div class="box-body">
                                @if(!is_null($technicalAnalysis))
                                    @if($technicalAnalysis->technicalAnalysisActive->decision < 0)
                                        {{--decision failed--}}
                                        <img src="{{asset('/images/default/techEvaluation-icon-red.svg') }}" alt="{{trans('privateTopics.rejected')}}" height="32" width="32" data-toggle="tooltip" title="{{trans('privateTopics.rejected')}}">
                                        <a href="{{action("TechnicalAnalysisController@show", ['type' => $type, 'cbKey' => $cbKey, 'topicKey' => !empty($topicKey) ? $topicKey : null ])}}" class="btn btn-danger" role="button">{{trans('privateCbs.show_technical_analysis')}}</a>
                                    @elseif($technicalAnalysis->technicalAnalysisActive->decision > 0)
                                        {{--decision passed--}}
                                        <img src="{{asset('/images/default/techEvaluation-icon-green.svg') }}" alt="{{trans('privateTopics.accepted')}}" height="32" width="32" data-toggle="tooltip" title="{{trans('privateTopics.accepted')}}">
                                        <a href="{{action("TechnicalAnalysisController@show", ['type' => $type, 'cbKey' => $cbKey, 'topicKey' => !empty($topicKey) ? $topicKey : null])}}" class="btn btn-success" role="button">{{trans('privateCbs.show_technical_analysis')}}</a>
                                    @else
                                        {{--decision undetermined--}}
                                        <img src="{{asset('/images/default/techEvaluation-icon.svg') }}" alt="{{trans('privateTopics.not_valuated')}}" height="32" width="32" data-toggle="tooltip" title="{{trans('privateTopics.not_valuated')}}">
                                        <a href="{{action("TechnicalAnalysisController@show", ['type' => $type, 'cbKey' => $cbKey, 'topicKey' => !empty($topicKey) ? $topicKey : null])}}" class="btn btn-default" role="button">{{trans('privateCbs.show_technical_analysis')}}</a>
                                    @endif
                                @else
                                    {{--create technical analyses--}}
                                    <img src="{{asset('/images/default/techEvaluation-icon-grey.svg') }}" alt="{{trans('privateTopics.create_technical_analysis')}}" height="32" width="32" data-toggle="tooltip" title="{{trans('privateTopics.create_technical_analysis')}}">
                                    <a href="{{action('TechnicalAnalysisController@create', ["type"=>$type,"cbKey"=>$cbKey,"topicKey"=>!empty($topicKey) ? $topicKey : null])}}" class="btn btn-flat empatia" role="button">{{trans('privateCbs.create_technical_analysis')}}</a>
                                @endif
                            </div>
                        </div>
                    @endif
                @endif
            </div>
            <div class="col-xs-12">
                @if(ONE::actionType('topic') == 'show' && ONE::verifyModuleAccess('cb','flags'))
                    <div class="box-private"> 
                        <div class="box-header">
                            <div style="font-weight: bold; font-size: large; margin-bottom: 15px;">
                                {{trans('privateTopics.flags')}}
                                <button class="btn btn-flat btn-xs bg-yellow pull-right" onclick="javascript:$('#flagHistoryModal').modal('show');">
                                    <i class="fa fa-binoculars"></i>
                                </button>
                            </div>
                        </div>
                        <div class="box-body" id="flagAttachmentModal">
                            <div class="row">
                                @if(!empty($cb->flags??[]))
                                    @foreach($cb->flags??[] as $key => $flag)
                                        <?php
                                            $currentFlag = collect($flagHistory)
                                                ->where("id","=",$flag->id)
                                                ->sortByDesc("pivot.created_at")
                                                ->first();
                                        ?>
                                        <div class="col-12 col-md-8">
                                            {{ $flag->title }}
                                        </div>
                                        <div class="col-12 col-md-4">
                                            {!! Form::oneSwitch("flag[".$flag->id."][status]",null, ($currentFlag->pivot->active??false) ? true : false,["readonly"=>false]) !!}
                                        </div>
                                    @endforeach
                                    <div class="col-12" style="padding-top:10px;">
                                        <button type="button" class="btn btn-primary pull-right" id="attachFlagSave">
                                            {{trans("privateCbs.save_changes")}}
                                        </button>
                                    </div>
                                @else
                                    <div class="col-12">
                                        {{ trans("privateCbs.no_flags_defined") }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <!-- flag history modal -->
                    <div class="modal fade" tabindex="-1" role="dialog" id="flagHistoryModal" >
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="card-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title">{{trans('privateCbs.flag_history')}}</h4>
                                </div>
                                <div class="modal-body" style="overflow-y: scroll;max-height: 50vh;">
                                    <div id="flagHistory">
                                        @include('private.cbs.flagHistory',["flagHistory" => $flagHistory, "elementKey" => $topicKey, "attachmentCode" => "TOPIC"])
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{trans("privateCbs.close")}}</button>
                                </div>
                            </div><!-- /.modal-content -->
                        </div><!-- /.modal-dialog -->
                    </div><!-- /.modal -->
                @endif
            </div>
            <div class="col-xs-12">
                @if(ONE::actionType('topic') == 'show')
                    @if(isset($configurations) && (ONE::checkCBsOption($configurations, 'TOPIC-ALLOW-EVENT-ASSOCIATION')) && !empty($topic->parent_topic))
                        <div class="box-private" style="margin-top: 30px;">
                            <div class="box-header">
                                <div style="font-weight: bold; font-size: large; margin-bottom: 15px;">{{trans('privateTopics.presentedOnEvent')}}</div>
                            </div>
                            <div class="box-body">
                                <div class="form-group">
                                    <label for="topicData_parent_topic_key">{{ trans("privateTopics.associated_event") }}</label><br>
                                    <a href="{{ action('TopicController@show', ['event',  $topic->parent_topic->cb->cb_key,  $topic->parent_topic->topic_key]) }}">{{ $topic->parent_topic->title }}</a>
                                    <hr style="margin: 10px 0 10px 0">
                                </div>

                            </div>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
    @if(ONE::actionType('topic') == 'show')
        <div class="modal fade" tabindex="-1" role="dialog" id="cooperatorModal" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="card-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">{{trans("privateCbs.add_cooperator")}}</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="tab-pane" id="tab">
                                    <table id="users_list" class="table table-hover table-striped">
                                        <thead>
                                        <tr>
                                            <th style="width:10%;"></th>
                                            <th>{{ trans('user.name') }}</th>
                                        </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{trans("privateCbs.close")}}</button>
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <button type="button" class="btn btn-primary" id="buttonSubmit">{{trans("privateCbs.save_changes")}}</button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
    @endif

@endsection


@section('scripts')
    @if(ONE::actionType("topic")=="create" && $type!="event")
        <script>
            $("#topic_creator").select2({
                placeholder: '{{ trans("privateTopics.select_the_user") }}',
                ajax: {
                    "url" : '{!! action('CbsController@getUsers') !!}',
                    "type": "POST",
                    "data": function () {
                        return {
                            "_token": "{{ csrf_token() }}"
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: $.map(data, function(item) {
                                return {
                                    text: item.name,
                                    id: item.user_key
                                }
                            })
                        };
                    }
                }
            });
        </script>
    @endif

    @if(ONE::actionType('topic') != 'show')
        <script src="{{ asset("js/tinymce/tinymce.min.js") }}"></script>
        @include('private._private.functions') {{-- Helper Functions --}}
        <script>
            /*
            {!! ONE::fileUploader('fileUploader', action('FilesController@upload'), 'ideaFileUploaded', 'select-files', 'drop-zone', 'files-list', 'files', 1, isset($uploadKey) ? $uploadKey : "", $allowFiles) !!}
            fileUploader.init();
            updateClickListener();
            @if(!empty($topicKey))
            updateFilesPostList('#files',1);
            @endif
            */
            {!! ONE::addTinyMCE(".tinyMCE", ['action' => action('ContentManagerController@getTinyMCE')]) !!}
        </script>
    @endif
    <script>

        function showUsersModal(){
            $('#cooperatorModal').modal('show');
        }

        $(".cbTypes").select2({
            placeholder: '{{ trans("privateContentManager.select_the_pad_type") }}',
        });
        @if(isset($relatedParameter->pad_key))
        $(".cbs").select2({
            placeholder: '{{ trans("privateContentManager.select_the_pad") }}',
            ajax: {
                "url" : '{!! action('CbsController@getListOfCbsByType') !!}',
                "type": "POST",
                "data": function () {
                    return {
                        "_token": "{{ csrf_token() }}",
                        "type":  $(".cbTypes").val(), // search term
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
        @else
        $(document).on('change','.cbTypes',function(){
            $(".cbs").select2({
                placeholder: '{{ trans("privateContentManager.select_the_pad") }}',
                ajax: {
                    "url" : '{!! action('CbsController@getListOfCbsByType') !!}',
                    "type": "POST",
                    "data": function () {
                        return {
                            "_token": "{{ csrf_token() }}",
                            "type":  $(".cbTypes").val(), // search term
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
            $(".cbs-div").show();
        });
        @endif
        @if(isset($relatedParameter->fetchedTopics))
        $(".myTopics").select2({
            placeholder: '{{ trans("privateContentManager.select_the_topics") }}',
            ajax: {
                "url": '{!! action('CbsController@getListOfTopicsByCb') !!}',
                "type": "POST",
                "data": function () {
                    return {
                        "_token": "{{ csrf_token() }}",
                        "cbKey": $(".cbs").val(), // search term
                    };
                },
                processResults: function (data) {
                    return {
                        results: $.map(data, function (item) {
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
        @else
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
        @endif
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

        $(function () {
            $('[data-toggle="tooltip"]').tooltip()
        })

        $('#cooperatorModal').on('show.bs.modal', function (event) {

            if (!$.fn.DataTable.isDataTable('#users_list')) {
                $('#users_list').dataTable();
            }

            $('#buttonSubmit').on('click', function (evt) {
                $('#buttonSubmit').off();

                var allVals = [];
                var cbKeyVal = '{{$cbKey ?? null}}';
                var typeVal = '{{$type ?? null}}';


                $('#cooperatorModal input:checked').each(function () {
                    allVals.push($(this).val());
                });

                if (allVals.length > 0) {
                    $.ajax({
                        method: 'post',
                        url: "{{action('TopicController@addCooperator',['topicKey'=> isset($topicKey) ? $topicKey : null])}}",
                        data: {
                            cooperatorsKey: allVals,
                            cbKey: cbKeyVal,
                            type: typeVal
                        },
                        success: function (response) {
                            if (response == 'Ok') {
                                table.ajax.reload();
                            }
                            $('#cooperatorModal').modal('hide');
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            $('#cooperatorModal').modal('hide');
                        }
                    });
                } else {
                    $('#cooperatorModal').modal('hide');
                }
            });
        });

        table = $('#cooperators_list').DataTable({
            language: {
                url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
            },
            responsive: true,
            processing: true,
            serverSide: true,
            ajax: '{!!  URL::action('TopicController@showCooperatorsTable', ['type'=> $type,'cbKey' => $cbKey, 'topicKey' => isset($topicKey) ? $topicKey : null]) !!}',
            columns: [
                { data: 'name', name: 'name', orderable: true, searchable: true},
                { data: 'status', name: 'status', orderable: true, searchable: true},
                { data: 'permissions', name: 'permissions', orderable: false, searchable: false},
                { data: 'action', name: 'action', searchable: false, orderable: false, width: "30px" }
            ],
            order: [['0', 'desc']]
        });

        $('#users_list').DataTable({
            language: {
                url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
            },
            responsive: true,
            processing: true,
            serverSide: true,
            ajax: '{!!  URL::action('TopicController@entityUsers', ['type'=> $type,'cbKey' => $cbKey, 'topicKey'=> isset($topicKey) ? $topicKey : null]) !!}',
            columns: [
                { data: 'cooperatorCheckbox', name: 'cooperatorCheckbox', searchable: false, orderable: false, width: "30px" },
                { data: 'name', name: 'name', orderable: true, searchable: true}
            ],
            order: [['1', 'desc']]
        });

        function toggleCooperatorItem(obj,name){
            if ($(obj).is(":checked")){
                html = "<div id='cooperatorDivItem_"+$(obj).val()+"' class='user-card cooperatorDivItem'>";
                html += "<div style='padding: 5px; margin-left: 60px;'>";
                html += "<b>"+name+"</b>";
                html += "</div>";
                html += "<div style='position: absolute; right: 10px; top: 10px'>";
                html += "<a href='javascript:uncheckCooperator(\""+$(obj).val()+"\")'><i style='color:red;' class='fa fa-remove'></i></a>";
                html += "</div>";
                html += "</div>";
            } else {
                $("#cooperatorDivItem_"+$(obj).val()).detach();
            }
        }

        function uncheckCooperator(userKey){
            $("#cooperatorCheckbox_"+userKey).prop( "checked", false );
            $("#cooperatorDivItem_"+userKey).detach();
        }

        function changePermissions(obj, userKey, topicKey){
            $.ajax({
                method: 'PUT',
                url: "{{action('TopicController@updateCooperatorPermission',['topicKey' => isset($topicKey) ? $topicKey : null])}}",
                data: {
                    permission: $(obj).val(),
                    userKey: userKey
                }
            });
        }


        @if(ONE::actionType('topic') == 'show' && ONE::verifyModuleAccess('cb','flags'))
            $('#attachFlagSave').on('click', function (evt) {
                var allVals = {};
                var isValid = true;

                $('#flagAttachmentModal :input,#flagAttachmentModal :checkbox').each(function (key, value) {
                    if(value.name!="") {
                        if ($(value).is(":checkbox"))
                            allVals[value.name] = $(value).is(":checked") ? "1" : "0";
                        else
                            allVals[value.name] = value.value;
                    }
                });

                allVals.attachmentCode = 'TOPIC';
                allVals.topicKey = '{{ $topic->topic_key }}';
                $.ajax({
                    method: 'POST', // Type of response and matches what we said in the route
                    url: "{{action('FlagsController@attachFlag')}}", // This is the url we gave in the route
                    data: allVals, // a JSON object to send back
                    success: function (response) { // What to do if we succeed
                        location.reload();
                    },
                    error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                        $('#flagAttachmentModal').modal('hide');
                        toastr.error('{{ trans('privateCbs.error_updating_state_or_sending_email_to_user') }}', '', {timeOut: 3000,positionClass: "toast-bottom-right"});

                    }
                });
            });
        @endif
    </script>

@endsection
