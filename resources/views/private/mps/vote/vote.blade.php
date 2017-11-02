@extends('private._private.index')

@section('header_styles')
    <style>
        .disabledTab{
            pointer-events: none;
        }

        .progress-bar-green, .progress-bar-success {
            background-color: #62a351!important;
        }

        .nav-pills > li.active > a, .nav-pills > li.active > a:hover, .nav-pills > li.active > a:focus {
            border-top-color: #62a351!important;
            background-color: #62a351!important;
        }

        .navParameterWizard > li.disabled > a {
            color: #dedede!important;
        }
    </style>
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12">
            @php
            $form = ONE::form('node', trans('privateMPVotes.details'))
                    ->settings(["model" => isset($operator) ? $operator : null, 'id' => $operator->operator_key ?? null])
                    ->show('MPVotesController@edit', 'MPVotesController@delete', ['operatorKey' => $operator->operator_key ?? null],
                            'MPsController@showConfigurations', ['mp_key' => $operator->mp->mp_key ?? null])
                    ->create('MPVotesController@store', 'MPsController@showConfigurations', ['mp_key' => $operator->mp->mp_key ?? null])
                    ->edit('MPVotesController@update', 'MPVotesController@index', ['operatorKey' => $operator->operator_key ?? null,'mp_key' => $operator->mp->mp_key ?? null])
                    ->open();
            @endphp

            {!! Form::hidden('operator_key', ($operator->operator_key ?? null)) !!}
            {!! Form::hidden('cb_key', ($operator->pad_key ?? null)) !!}
            {!! Form::hidden('mp_key', ($operator->mp->mp_key ?? null)) !!}
            {!! Form::hidden('operator_type',($operator->operator_type->code ?? null)) !!}
            {!! Form::hidden('voteKey', ($operator->component_key ?? null), ['id' => 'voteKey']) !!}


            <div id="voteWizard" class="modal-body">
                <div class="navbar">
                    <div class="navbar-inner">
                        <ul class="nav nav-pills">
                            <li class="active"><a href="#step_vote_details" data-toggle="tab" data-step="vote_details">{{ trans('privateMPVotes.vote_details') }}</a></li>
                            <li class=""><a href="#step_vote_generic_configurations" class="disabledTab" data-toggle="tab" data-step="vote_generic_configurations">{{ trans('privateMPVotes.vote_generic_configurations') }}</a></li>
                            <li class=""><a href="#step_vote_configuration" class="disabledTab" data-toggle="tab" data-step="vote_configuration">{{ trans('privateMPVotes.vote_configurations') }}</a></li>
                        </ul>
                    </div>
                </div>
                <div class="tab-content">
                    <div class="tab-pane fade in active" id="step_vote_details">
                        <div class="well">
                            {!! Form::oneText('voteName', ['name' => trans('privateMPVotes.vote_name'),'description' => trans("privateMPVotes.help_vote_name")], $voteName ?? null,
                            ['class' => 'form-control', 'id' => 'voteName', 'required' => 'required']) !!}
                            {!! Form::oneDate('startDate',['name' => trans('privateMPVotes.vote_start_date'),'description' => trans("privateMPVotes.help_vote_start_date")], isset($voteEvent) ? substr($voteEvent->start_date, 0, 10) : null, ['id' => 'startDate']) !!}
                            {!! Form::oneTime('startTime',['name' => trans('privateMPVotes.vote_start_time'),'description' => trans("privateMPVotes.help_vote_start_time")], isset($voteEvent) ? substr($voteEvent->start_time, 0, 5) : null, ['id' => 'startTime']) !!}
                            {!! Form::oneDate('endDate',['name' => trans('privateMPVotes.vote_end_date'),'description' => trans("privateMPVotes.help_vote_end_date")], isset($voteEvent) ? substr($voteEvent->end_date, 0, 10) : null, ['id' => 'endDate']) !!}
                            {!! Form::oneTime('endTime',['name' => trans('privateMPVotes.vote_end_time'),'description' => trans("privateMPVotes.help_vote_end_time")], isset($voteEvent) ? substr($voteEvent->end_time, 0, 5) : null, ['id' => 'endTime']) !!}
                        </div>

                        <!-- Buttons: Next -->
                        <div class="row">
                            <div class="col-md-12">
                                <a class="btn btn-flat empatia next pull-right" href="#">{!! trans("privateMPVotes.next") !!} <i class="fa fa-step-forward" aria-hidden="true"></i></a>
                                <br/><br/>
                            </div>
                        </div>

                    </div>

                    <div class="tab-pane fade" id="step_vote_generic_configurations">
                        {{ trans("privateMPVotes.configurations")}}
                        <div class="well">
                            @if(!empty($genericConfigs))
                                <div class="row">
                                    @foreach($genericConfigs as $config)
                                        <div class="col-md-4">
                                            {!! Form::oneSwitch("genericConfig_".$config->vote_configuration_key,
                                                                array("name" => $config->name, "description" => $config->description ),
                                                                isset($voteConfigs[$config->vote_configuration_key])) !!}
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <i class="fa fa-exclamation-triangle" aria-hidden="true"></i> {{ trans("privateMPVotes.generic_configurations_not_available") }}
                            @endif
                        </div>
                        <!-- Buttons: Previous && Next -->
                        <a class="btn btn-flat empatia next pull-right" href="#">{!! trans("privateMPVotes.next") !!} <i class="fa fa-step-forward" aria-hidden="true"></i></a>
                        <a class="btn btn-flat empatia prev " href="#"><i class="fa fa-step-backward" aria-hidden="true"></i> {!! trans("privateMPVotes.previous") !!}</a>
                    </div>


                    <div class="tab-pane fade" id="step_vote_configuration">
                        <div class="well">
                            @if(ONE::actionType('node') != 'create')
                                <div class="card flat">
                                    <div class="card-header">{{ trans("privateMPVotes.configurations")}}</div>
                                    <div class="box-body">
                                        {!! Form::oneText('method', trans('privateMPVotes.method'), isset($voteEvent) ? $voteEvent->method->name : null, ['class' => 'form-control', 'id' => 'method','readonly' => 'readonly']) !!}
                                        {!! Form::oneText('description', trans('privateMPVotes.description'), isset($voteEvent) ? $voteEvent->method->description : null, ['class' => 'form-control', 'id' => 'description','readonly'=>'readonly']) !!}

                                        {!! $html !!}
                                    </div>
                                </div>
                            @else
                                {{ trans("privateMPVotes.configurations")}}
                                <div class='form-group'>
                                    <label for="methodGroupSelect">{{ trans("privateMPVotes.vote_types")}}</label>
                                    <select class="form-control" id="methodGroupSelect" name="methodGroupSelect"
                                            onchange="showMethods()" required>
                                        <option value="">{{ trans("privateMPVotes.select_vote_type")}}</option>
                                        @if(!empty($methodGroup))
                                            @foreach($methodGroup as $group)
                                                <option value="{{$group->id}}">{{$group->name}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div id="methodsDiv">
                                </div>
                                <div id="configurationsDiv" class="box-body">
                                </div>
                            @endif
                        </div>
                        <!-- Buttons: Previous && Next -->
                        <button type="submit" class="btn btn-flat empatia pull-right" form="node">{{trans("privateMPVotes.create")}} </button>
                        <a class="btn btn-flat empatia prev " href="#"><i class="fa fa-step-backward" aria-hidden="true"></i> {!! trans("privateMPVotes.previous") !!}</a>
                    </div>
                </div>

            </div>
            {!! $form->make() !!}
        </div>

    </div>
@endsection
@section('scripts')
    {{--script to get sidebar menu--}}
    <script>
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": false,
            "progressBar": false,
            "positionClass": "toast-bottom-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "0",
            "hideDuration": "0",
            "timeOut": "0",
            "extendedTimeOut": "0",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };

        /* Stepper Engine [create.blade.php] --------------------------- START */
        $('#voteWizard .next').click(function(){

            var stepDiv = $(this).parents('.tab-pane').next().attr("id");
            if($("#voteName").val() =="" ){
                $("#voteName").focus();
                toastr.error("{!! preg_replace( "/\r|\n/", "", htmlentities(trans("privateMPVotes.name_is_required_on_tab"),ENT_QUOTES)) !!} #1!");
                return false;
            }else if($("#startDate").val() == ''){
                $("#startDate").focus();
                toastr.error("{!! preg_replace( "/\r|\n/", "", htmlentities(trans("privateMPVotes.start_date_is_required_on_tab"),ENT_QUOTES)) !!} #1!");
                return false;
            }else if( $("#startTime").val() == ''){
                $("#startTime").focus();
                toastr.error("{!! preg_replace( "/\r|\n/", "", htmlentities(trans("privateMPVotes.start_time_is_required_on_tab"),ENT_QUOTES)) !!} #1!");
                return false;
            }else if($("#endDate").val() == ''){
                $("#endDate").focus();
                toastr.error("{!! preg_replace( "/\r|\n/", "", htmlentities(trans("privateMPVotes.end_date_is_required_on_tab"),ENT_QUOTES)) !!} #1!");
                return false;
            }else if( $("#endTime").val() == '' ){
                $("#endTime").focus();
                toastr.error("{!! preg_replace( "/\r|\n/", "", htmlentities(trans("privateMPVotes.end_time_is_required_on_tab"),ENT_QUOTES)) !!} #1!");
                return false;
            }else {
                var nextId = stepDiv;
                $('[href=#'+nextId+']').removeClass('disabledTab');
                $('[href=#'+nextId+']').tab('show');
                return false;
            }

        });

        $('#voteWizard .prev').click(function(){
            var nextId = $(this).parents('.tab-pane').prev().attr("id");
            $('[href=#'+nextId+']').tab('show');
            return false;
        });

        $('#voteWizard a[data-toggle="tab"]').on('shown.bs.tab', function (e) {

            //update progress
            var step = $(e.target).data('step');
            if(step == 'vote_configuration'){

                $('form').find('input[type=submit]').show();
            }

        });

        $('form').find('input[type=submit]').hide();


    </script>
    <script>

        function showMethods( ) {
            var idMethodGroup = $('#methodGroupSelect').val();

            if (idMethodGroup == "") {
                $('#methodsDiv').html("");
                $('#configurationsDiv').html("");
                return;
            }
            $.ajax({
                method: 'POST', // Type of response and matches what we said in the route
                url: '{{action('CbsVoteController@getMethodsData')}}', // This is the url we gave in the route
                data: {postId: idMethodGroup, _token: "{{ csrf_token() }}"}, // a JSON object to send back
                success: function (response) { // What to do if we succeed
                    // build select with array
                    if (response instanceof Array) {
                        var selectObj = '<p></p><select class="form-control" id="methodSelect" name="methodSelect" required onchange="getMethodConfigurations()">';
                        selectObj += '<option value=""> -- {{ trans("privateMPVotes.select_method_types") }} -- </option>';
                        for( i = 0; i < response.length ; i++  ){
                            selectObj += '<option  value="'+response[i].id+'">'+response[i].name+'</option>';
                        }
                        selectObj += '</select>';

                        $("#methodsDiv").html(selectObj);
                        $('#configurationsDiv').html("");
                    } else {
                        location.reload();
                    }

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
                data: {
                    postId: idMethod,
                    _token: "{{ csrf_token() }}"
                },
                success: function (response) { // What to do if we succeed
                    $("#configurationsDiv").html(response);
                    $('#advancedConfsSelect').removeAttr('disabled');
                    if ($('#parameterTypeSelect').length && $('#parameterTypeSelect').val() != "") {
                        //  getAdvancedConfigurations();
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                }
            });
        }
    </script>
@endsection
