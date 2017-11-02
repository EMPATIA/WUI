@extends('private._private.index')
@section('header_styles')
    <style>
        .adv-search{
            margin-top: 20px;
            margin-bottom: 30px;
        }

        .select2-container--default .select2-search--inline .select2-search__field {
            border: 0;
        }
        .select2-container--default .select2-results__option[aria-selected="true"] {
            background-color: #f4f4f5;
        }
        .select2levelsNeeded{
            width: 80%;
        }
    </style>
@endsection
@section('content')

    <div class="card flat topic-data-header" >
        <p><label for="contentStatusComment"> {{trans('privateCbs.pad')}}</label>  {{$cb_title}}</p>
        @if(!empty($cbAuthor))
        <p><label for="contentStatusComment"> {{trans('privateCbs.author')}}</label>
            <a href="{{action('UsersController@show', ['userKey' => $cbAuthor->user_key, 'role' => $cbAuthor->role ?? null])}}">{{$cbAuthor->name}}</a>
        </p>
        @endif
        <p><label for="contentStatusComment"> {{trans('privateCbs.start_date')}}</label>  {{$cb_start_date}}</p>
    </div>

        <div style="margin-top:25px">
            @if(isset($step))
                @php
                $form = ONE::form('vote', trans('privateCbsVote.details'), 'cb', 'pad_votes')
                    ->settings(["model" => isset($voteEvent) ? $voteEvent : null, 'id' => isset($voteEvent) ? $voteEvent->key : null])
                    ->show('CbsVoteController@edit', 'CbsVoteController@delete', ['type' => $type, 'cbKey' => $cbKey, 'voteKey' => isset($voteEvent) ? $voteEvent->key : null, 'step' => $step],
                        'CbsController@create', ['type' => $type, 'cbKey' => isset($cbKey) ? $cbKey : null, 'step' => $step])
                    ->create('CbsVoteController@store', 'CbsController@create', ['type' => $type, 'cbKey' => $cbKey, 'step' => $step])
                    ->edit('CbsVoteController@update', 'CbsVoteController@show', ['type' => $type, 'cbKey' => $cbKey, 'voteKey' => isset($voteEvent) ? $voteEvent->key : null])
                    ->open();
                @endphp
            @else
                @php
                $form = ONE::form('vote', trans('privateCbsVote.details'), 'cb', 'pad_votes')
                    ->settings(["model" => isset($voteEvent) ? $voteEvent : null, 'id' => isset($voteEvent) ? $voteEvent->key : null])
                    ->show('CbsVoteController@edit', 'CbsVoteController@delete', ['type' => $type, 'cbKey' => $cbKey, 'voteKey' => isset($voteEvent) ? $voteEvent->key : null],
                        'CbsController@showVotes', ['type' => $type, 'id' => isset($cbKey) ? $cbKey : null])
                    ->create('CbsVoteController@store', 'CbsController@showVotes', ['type' => $type, 'cbKey' => $cbKey, 'voteKey' => isset($voteEvent) ? $voteEvent->key : null])
                    ->edit('CbsVoteController@update', 'CbsVoteController@show', ['type' => $type, 'cbKey' => $cbKey, 'voteKey' => isset($voteEvent) ? $voteEvent->key : null])
                    ->open();
                @endphp
            @endif

            @if((ONE::actionType('vote') == 'show') || (ONE::actionType('vote') == 'edit'))
                {!! Form::hidden('cbKey', isset($cbKey) ? $cbKey : 0, ['id' => 'cbKey']) !!}
                {!! Form::hidden('voteKey', isset($voteEvent) ? $voteEvent->key : 0, ['id' => 'voteKey']) !!}
                {!! Form::oneText('name', array("name"=>trans('privateCbs.name'),"description"=>trans('privateCbs.nameDescription')), !empty($name) ? $name : null, ['class' => 'form-control', 'id' => 'name', 'required','readonly'=>'readonly']) !!}
                {!! Form::oneText('code', array("name"=>trans('privateCbs.code'),"description"=>trans('privateCbs.codeDescription')), $code ?? null, ['class' => 'form-control', 'id' => 'code', 'readonly'=>'readonly']) !!}
                {!! Form::oneText('voteKey', array("name"=>trans('privateCbs.voteKey'),"description"=>trans('privateCbs.voteKeyDescription')), isset($voteEvent) ? $voteEvent->key : null, ['class' => 'form-control', 'id' => 'voteKey', 'required' => 'required','readonly'=>'readonly']) !!}
                {!! Form::oneText('method', array("name"=>trans('privateCbs.method'),"description"=>trans('privateCbs.methodDescription')), isset($voteEvent) ? $voteEvent->method->name : null, ['class' => 'form-control', 'id' => 'method','readonly' => 'readonly']) !!}
                {!! Form::oneText('description', array("name"=>trans('privateCbs.description'),"description"=>trans('privateCbs.descriptionDescription')), isset($voteEvent) ? $voteEvent->method->description : null, ['class' => 'form-control', 'id' => 'description','readonly'=>'readonly']) !!}
                {!! Form::oneDate('startDate', array("name"=>trans('privateCbs.startDate'),"description"=>trans('privateCbs.startDateDescription')), isset($voteEvent) ? substr($voteEvent->start_date, 0, 10) : null, ['id' => 'startDate', 'readonly' => isset($voteEvent)?($voteEvent->start_date < date('Y-m-d') ? 'readonly' : null):null]) !!}
                {!! Form::oneTime('startTime', array("name"=>trans('privateCbs.startTime'),"description"=>trans('privateCbs.startTimeDescription')), isset($voteEvent) ? substr($voteEvent->start_time, 0, 5) : null, ['id' => 'startTime']) !!}
                {!! Form::oneDate('endDate', array("name"=>trans('privateCbs.endDate'),"description"=>trans('privateCbs.endDateDescription')), isset($voteEvent) ? substr($voteEvent->end_date, 0, 10) : null, ['id' => 'endDate']) !!}
                {!! Form::oneTime('endTime', array("name"=>trans('privateCbs.endTime'),"description"=>trans('privateCbs.endTimeDescription')), isset($voteEvent) ? substr($voteEvent->end_time, 0, 5) : null, ['id' => 'endTime']) !!}

                <div class="card flat margin-top-20">
                    <div class="card-header">{{ trans("privateCbs.configurations")}}</div>
                    <div id="configurationsDiv" class="box-body">
                        {!! $html !!}
                    </div>
                </div>

            @elseif(ONE::actionType('vote') == 'create')

                {!! Form::hidden('cbKey', isset($cbKey) ? $cbKey : 0, ['id' => 'cbKey']) !!}
                {!! Form::oneText('name', array("name"=>trans('privateCbs.voteName'),"description"=>trans('privateCbs.voteNameDescription')), isset($name) ? $name : null, ['class' => 'form-control', 'id' => 'name', 'required']) !!}
                {!! Form::oneText('code', array("name"=>trans('privateCbs.code'),"description"=>trans('privateCbs.codeDescription')),$code ?? null, ['class' => 'form-control', 'id' => 'code']) !!}
                {!! Form::oneDate('startDate', array("name"=>trans('privateCbs.voteStartDate'),"description"=>trans('privateCbs.voteStartDateDescription')), isset($voteEvent) ? substr($voteEvent->start_date, 0, 10) : null, ['id' => 'startDate']) !!}
                {!! Form::oneTime('startTime', array("name"=>trans('privateCbs.voteStartTime'),"description"=>trans('privateCbs.voteStartTimeDescription')), isset($voteEvent) ? substr($voteEvent->start_time, 0, 5) : null, ['id' => 'startTime']) !!}
                {!! Form::oneDate('endDate', array("name"=>trans('privateCbs.voteEndDate'),"description"=>trans('privateCbs.voteEndDateDescription')), isset($voteEvent) ? substr($voteEvent->end_date, 0, 10) : null, ['id' => 'endDate']) !!}
                {!! Form::oneTime('endTime', array("name"=>trans('privateCbs.voteEndTime'),"description"=>trans('privateCbs.voteEndTimeDescription')), isset($voteEvent) ? substr($voteEvent->end_time, 0, 5) : null, ['id' => 'endTime']) !!}


                <div class="card flat">
                    <div class="box-body">
                        <div class="card-title">{{ trans("privateCbs.configurations")}}</div>
                        <div class='form-group'>
                            <label for="description">{{ trans("privateCbs.voteTypes")}}</label>
                            <div for="title" style="font-size:x-small">{{trans('privateCbs.voteTypesDescription')}}</div>

                            <select class="form-control" id="methodGroupSelect" name="methodGroupSelect"
                                    onchange="showMethods()" required>
                                <option value="">Select votes types</option>
                                @foreach($methodGroup as $group)
                                    <option value="{{$group->id}}">{{$group->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div id="methodsDiv">
                        </div>
                    </div>
                    <div id="configurationsDiv" class="box-body">
                    </div>
                </div>

                {{--Advanced Configurations--}}

                {{--<div class="card flat">--}}
                {{--<div class="card-header boldText"><strong>{{ trans("privateCbs.advancedConfigurations")}}</strong>--}}
                {{--</div>--}}
                {{--<div class="box-body">--}}
                {{--<div class='form-group'>--}}
                {{--<label for="description">{{ trans("privateCbs.advancedConfsTypes")}}</label>--}}
                {{--<select class="form-control" id="advancedConfsSelect" name="advancedConfsSelect"--}}
                {{--onchange="getParameterTypes()" required disabled>--}}
                {{--<option value="">{{ trans("privateCbs.selectAdvancedConf") }}</option>--}}
                {{--@foreach($advancedConfigs as $config)--}}
                {{--<option value="{{$config->general_config_type_key}}">{{$config->name}}</option>--}}
                {{--@endforeach--}}
                {{--</select>--}}
                {{--</div>--}}
                {{--<div id="parameterTypesDiv">--}}
                {{--</div>--}}
                {{--</div>--}}
                {{--<div id="advancedConfsDiv" class="box-body">--}}
                {{--</div>--}}
                {{--</div>--}}

            @endif
            <div class="card flat margin-top-20">
                <div class="card-header">{{ trans("privateCbs.genericConfigurations")}}
                </div>
                <div class="box-body row">
                    @foreach($genericConfigs as $config)
                        <div class="col-md-3">
                            @if(str_contains($config->code,'text_'))
                                {!! Form::oneText("genericConfig_" . $config->vote_configuration_key, $config->name, isset($voteConfigs[$config->vote_configuration_key]) ? $voteConfigs[$config->vote_configuration_key] : null, ['class' => 'form-control', 'id' => "genericConfig_" . $config->code]) !!}
                            @else
                                <div class="form-group">
                                    {!! Form::hidden("genericConfig_" . $config->vote_configuration_key, 0) !!}
                                    {!! Form::oneSwitch("genericConfig_" . $config->vote_configuration_key, $config->name, isset($voteConfigs[$config->vote_configuration_key])? (($voteConfigs[$config->vote_configuration_key] == '1')?'checked':''):'', ["id"=>"genericConfig_" . $config->code]) !!}
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

        <!-- <div class="card flat">
                <div class="card-header">
                    {{trans('privateCbs.Securityconfigurations')}}
                </div>
                <div class="card-body">
                    @foreach($configurations as $configuration)
            <div class="col-12" style="padding-left: 0px;">

@if($configuration->code == 'user_level_permissions')
                <div class="card-header">
                    <a class="collapsed" role="button" data-toggle="collapse" href="#collapse_{{$configuration->id}}" aria-expanded="false" aria-controls="collapse_{{$configuration->id}}" style="color: black; text-decoration: underline">
                                        {{$configuration->title}}
                        </a>
                    </div>
@endif
            @if($configuration->code == 'user_level_permissions')
                <div class="card-body">
@foreach($genericConfigs as $option)

                    @if($option->code=='user_level_permissions')

                        <div class="form-group">
                            <label class="col-sm-12 col-md-2 form-control-label" for="genericConfigs_{{$option->code}}">{{$titleConfigurations}}</label>
                                                <div class="col-sm-12 col-md-10">
                                                    <select id="genericConfigs_{{$option->code}}" name="configs[user_level_permissions][{{$option->code}}][]" multiple class="select2levelsNeeded" @if (ONE::actionType('vote') == "show") disabled @endif>

                                                        @if(isset($userLevels))
                            @forelse($userLevels as $level)

                                <option value="{!! $level->login_level_key !!}" @if (in_array($level->login_level_key, isset($vote_conf_Key[$option->vote_configuration_key]) ? $vote_conf_Key[$option->vote_configuration_key] : [])) selected @endif>{!! $level->name !!}</option>

                                                            @empty
                                {{ trans('privateCbs.no_levels_available') }}
                            @endforelse
                        @endif
                                </select>
                            </div>
                        </div>
@endif
                @endforeach
                        </div>
@endif

                    </div>

@endforeach
                </div>
            </div> -->

        {!! $form->make() !!}
    </div>

    </div>
@endsection

@section('scripts')
    <script>

        $(function() {
            var array = ["{{ $type }}", "{{$cb->cb_key}}"]
            getSidebar('{{ action("OneController@getSidebar") }}', 'votes', array, 'padsType' )
        })
        @if(ONE::actionType('vote') == 'create')
        function showMethods() {
            var idMethodGroup = $('#methodGroupSelect').val();

            if (idMethodGroup == "") {
                $('#methodsDiv').html("");
                $('#configurationsDiv').html("");
                return;
            }
            $.ajax({
                method: 'POST', // Type of response and matches what we said in the route
                url: '{{action('CbsVoteController@getMethods')}}', // This is the url we gave in the route
                data: {postId: idMethodGroup, _token: "{{ csrf_token() }}"}, // a JSON object to send back
                success: function (response) { // What to do if we succeed
                    console.log(response);
                    $("#methodsDiv").html(response);
                    $('#configurationsDiv').html("");
                },
                error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                }
            });
        }
        function getMethodConfigurations() {
            var idMethod = $('#methodSelect').val();
            if (idMethod == "") {
                $('#configurationsDiv').html("");
                $('#advancedConfsSelect').attr('disabled');
                return;
            }
            $.ajax({
                method: 'POST', // Type of response and matches what we said in the route
                url: '{{action('CbsVoteController@getMethodConfigurations')}}', // This is the url we gave in the route
                data: {postId: idMethod, _token: "{{ csrf_token() }}"}, // a JSON object to send back
                success: function (response) { // What to do if we succeed
//                    console.log(response);
                    $("#configurationsDiv").html(response);
                    $('#advancedConfsSelect').removeAttr('disabled');
                    if ($('#parameterTypeSelect').length && $('#parameterTypeSelect').val() != "") {
//                        getAdvancedConfigurations();
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                }
            });
        }
        @endif
        {{--function getParameterTypes() {--}}
        {{--var idType = $('#advancedConfsSelect').val();--}}
        {{--if (idType == "") {--}}
        {{--$('#parameterTypesDiv').html("");--}}
        {{--$('#advancedConfsDiv').html("");--}}
        {{--return;--}}
        {{--}--}}
        {{--$.ajax({--}}
        {{--method: 'POST',--}}
        {{--url: '{{action('CbsVoteController@getParameterTypes')}}',--}}
        {{--data: {configCode: idType, _token: "{{ csrf_token() }}"},--}}
        {{--success: function (response) { // What to do if we succeed--}}
        {{--//                    console.log(response);--}}
        {{--$("#parameterTypesDiv").html(response);--}}
        {{--$('#advancedConfsDiv').html("");--}}

        {{--},--}}
        {{--error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail--}}
        {{--console.log("AJAX error: " + textStatus + ' : ' + errorThrown);--}}
        {{--}--}}
        {{--});--}}
        {{--}--}}

        {{----}}
        {{--function getAdvancedConfigurations() {--}}
        {{--var idType = $('#parameterTypeSelect').val();--}}
        {{--if (idType == "") {--}}
        {{--$('#advancedConfsDiv').html("");--}}
        {{--return;--}}
        {{--}--}}
        {{--var idMethod = $('#methodSelect').val();--}}
        {{--$.ajax({--}}
        {{--method: 'POST', // Type of response and matches what we said in the route--}}
        {{--url: '{{action('CbsVoteController@getMethodConfigurations')}}', // This is the url we gave in the route--}}
        {{--data: {postId: idMethod, advancedConf: true,  _token: "{{ csrf_token() }}"}, // a JSON object to send back--}}
        {{--success: function (response) { // What to do if we succeed--}}
        {{--//                    console.log(response);--}}
        {{--$("#advancedConfsDiv").html(response);--}}
        {{--},--}}
        {{--error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail--}}
        {{--console.log("AJAX error: " + textStatus + ' : ' + errorThrown);--}}
        {{--}--}}
        {{--});--}}
        {{--}--}}


            $(".select2levelsNeeded").select2({
            templateResult: function (data) {
                var $res = $('<span></span>');
                var $check = $('<input type="checkbox" class="inputCheckBoxSelect2" style="margin-right:5px;" />');

                $res.text(data.text);

                if (data.element) {
                    $res.prepend($check);
                    $check.prop('checked', data.element.selected);
                }
                return $res;
            }
        });



    </script>
@endsection
