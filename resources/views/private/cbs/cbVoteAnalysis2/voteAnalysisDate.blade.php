@extends('private._private.index')

@section('content')
    @include('private.cbs.cbVoteAnalysis2.details.cbDetails')

    @if(!empty($voteEvents))
        <div class="margin-bottom-20">
            <div class="row">
                <div class="col-12">
                    <div><label>{{ trans('privateCbsVoteAnalysis.vote_event') }}</label></div>
                    <select id="voteEventSelect" name="voteEventSelect" class="voteEventSelect">
                        <option value="">{{ trans('privateCbsVoteAnalysis.select_vote_event') }}</option>
                        @foreach($voteEvents as $key => $voteEvent)
                            <option value="{!! $key !!}" @if(Session::get("voteEventKey") == $key) selected @endif>{!! $voteEvent !!}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    @endif

    @include('private.cbs.cbVoteAnalysis2.details.cbMoreDetails')

    <div class="row">
        <div class="col-12 tabs-left" id="nav-analysis" hidden="hidden">
            <ul id="tabVoteAnalysis" class="nav nav-tabs" role="tablist">
                <li role="presentation" class="nav-item">
                    <a class="nav-link active" href="#tab_vote_analysis_date2" role="tab" id="vote_analysis_date2" data-toggle="tab" aria-expanded="true">
                        {{ trans('privateCbsVoteAnalysis.per_day') }}
                    </a>
                </li>
                <li role="presentation" class="nav-item">
                    <a class="nav-link" href="#tab_vote_analysis_hour2" role="tab" id="vote_analysis_hour2" data-toggle="tab" aria-expanded="false">
                        {{ trans('privateCbsVoteAnalysis.per_hour') }}
                    </a>
                </li>
            </ul>
            <!-- Tab panes -->
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane default-padding active" id="tab_vote_analysis_date2">
                    <!-- Filters -->
                    <div id="tabFiltersVoteAnalysisByDate" class="tabFilters">
                        <div class="row">
                            <div class="col-12 col-md-4 col-lg-3 col-xl-3">
                                <!-- Date range -->
                                <label>{{ trans('privateUserAnalysis.date_range') }}</label>
                                <div class="form-group">
                                    <div class='input-group date' id=''>
                                        <input id="daterangepicker1" name="dr_vote_analysis_date2" type='text' class="form-control"
                                            value="@if(!empty($voteEventObj) && $voteEventObj->start_date != "0000-00-00 00:00" && $voteEventObj->end_date != "0000-00-00 00:00")
                                                        {{ substr($voteEventObj->start_date,0,10) }} - {{substr($voteEventObj->end_date,0,10)}}
                                                    @else
                                                        {{ Carbon\Carbon::now()->subDays(30)->toDateString() }} - {{ Carbon\Carbon::now()->toDateString() }}
                                                    @endif" />
                                        <span class="input-group-addon">
                                          <i class="fa fa-calendar" aria-hidden="true"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-8 col-lg-9 col-xl-9">
                                <div class="pull-right margin-top-20">
                                    <div data-toggle="buttons">
                                        <label class="btn btn-primary">
                                            <input id="oneWeek" name="timelapse" type="radio" value="1" >
                                            {{ trans('privateUserAnalysis.one_week') }}
                                        </label>
                                        <label class="btn btn-primary">
                                            <input id="oneMonth" name="timelapse" type="radio" value="1" >
                                            {{ trans('privateUserAnalysis.one_month') }}
                                        </label>
                                        <label class="btn btn-primary">
                                            <input id="twoMonths" name="timelapse" type="radio" value="1" >
                                            {{ trans('privateUserAnalysis.two_months') }}
                                        </label>
                                        <label class="btn btn-primary">
                                            <input id="votePeriod" name="timelapse" type="radio" value="1" >
                                            {{ trans('privateUserAnalysis.vote_period') }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="margin-top-30">
                            <div class="row">
                                <div class="col-12">
                                    <span class="text-bold">{{ trans('privateUserAnalysis.votes') }}</span>
                                </div>
                            </div>
                            <div class="box-filter-no-border">
                                <div class="row">
                                    <div class="col-12 col-md-6 col-lg-3 col-xl-2">
                                        {!! Form::oneSwitch2('count_voters',trans('privateUserAnalysis.voters'), 1, array("value"=> 1, "readonly"=>false))  !!}
                                    </div>
                                    <div class="col-12 col-md-6 col-lg-3 col-xl-2">
                                        {!! Form::oneSwitch2('total',trans('privateUserAnalysis.total'), 1, array("value"=> 1, "readonly"=>false))  !!}
                                    </div>
                                    <div class="col-12 col-md-6 col-lg-3 col-xl-2">
                                        {!! Form::oneSwitch2('positive',trans('privateUserAnalysis.positives'), 0, array("value"=> 1, "readonly"=>false))  !!}
                                    </div>
                                    <div class="col-12 col-md-6 col-lg-3 col-xl-2">
                                        {!! Form::oneSwitch2('negative',trans('privateUserAnalysis.negative'), 0, array("value"=> 1, "readonly"=>false))  !!}
                                    </div>
                                    <div class="col-12 col-md-6 col-lg-3 col-xl-2">
                                        {!! Form::oneSwitch2('balance',trans('privateUserAnalysis.balance'), 0, array("value"=> 1, "readonly"=>false))  !!}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="margin-top-30">
                            <div class="row">
                                <div class="col-12">
                                    <span class="text-bold">{{ trans('privateUserAnalysis.channels') }}</span>
                                </div>
                            </div>

                            <div class="margin-bottom-20">
                                <div class="row">
                                    <div class="col-12 col-md-6 col-lg-3 col-xl-3">
                                        {!! Form::oneSwitch2('web',trans('privateUserAnalysis.web'), 0, array("value"=> 1, "readonly"=>false))  !!}
                                    </div>
                                    <div class="col-12 col-md-6 col-lg-3 col-xl-3">
                                        {!! Form::oneSwitch2('pc',trans('privateUserAnalysis.pc'), 0, array("value"=> 1, "readonly"=>false))  !!}
                                    </div>
                                    <div class="col-12 col-md-6 col-lg-3 col-xl-3">
                                        {!! Form::oneSwitch2('mobile',trans('privateUserAnalysis.mobile'), 0, array("value"=> 1, "readonly"=>false))  !!}
                                    </div>
                                    <div class="col-12 col-md-6 col-lg-3 col-xl-3">
                                        {!! Form::oneSwitch2('tablet',trans('privateUserAnalysis.tablet'), 0, array("value"=> 1, "readonly"=>false))  !!}
                                    </div>
                                </div>
                            </div>

                            <div class="margin-top-20">
                                <div class="row">
                                    <div class="col-12 col-md-6 col-lg-3 col-xl-3">
                                        {!! Form::oneSwitch2('sms',trans('privateUserAnalysis.sms'), 0, array("value"=> 1, "readonly"=>false))  !!}
                                    </div>
                                    <div class="col-12 col-md-6 col-lg-3 col-xl-3">
                                        {!! Form::oneSwitch2('kiosk',trans('privateUserAnalysis.kiosk'), 0, array("value"=> 1, "readonly"=>false))  !!}
                                    </div>
                                    <div class="col-12 col-md-6 col-lg-3 col-xl-3">
                                        {!! Form::oneSwitch2('in_person',trans('privateUserAnalysis.in_person'), 0, array("value"=> 1, "readonly"=>false))  !!}
                                    </div>
                                    <div class="col-12 col-md-6 col-lg-3 col-xl-3">
                                        {!! Form::oneSwitch2('other',trans('privateUserAnalysis.other'), 0, array("value"=> 1, "readonly"=>false))  !!}
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if(!empty($topics))
                        <div class="margin-top-30">
                            <div class="row">
                                <div class="col-12">
                                    <span class="text-bold">{{ trans('privateUserAnalysis.view') }}</span>
                                </div>
                            </div>
                            <div class="col-12">
                                <div style="margin: 5px 0;">
                                    <label>
                                        <input id="view_filter_1" name="view_filter" type="radio" value="all" checked onclick="javascript:$('#topicsList').hide();"> {{ trans('privateUserAnalysis.all') }}
                                    </label>
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <label>
                                        <input id="view_filter_2" name="view_filter" type="radio" value="by_topic" onclick="javascript:$('#topicsList').show();"> {{ trans('privateUserAnalysis.by_topic') }}
                                    </label>
                                </div>
                            </div>
                            <div id="topicsList" style="display:none;">
                                <div class="row">
                                    <div class="col-12">

                                        <select id="topic_key" name="topic_key" class="form-control select2-searchable" style="width: 100%;">
                                            <option value=""> {{ trans('privateUserAnalysis.select_topic') }} </option>
                                            @foreach($topics as $topic)
                                                <option value="{!! $topic->topic_key !!}">#{!! $topic->topic_number !!} - {!! $topic->title !!}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <div class="margin-top-30">
                            <div class="row">
                                <div class="col-12">
                                    <button id="applyFilters" class="btn btn-flat empatia margin-top-20" type="button">{{ trans('privateUserAnalysis.apply') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="tab_vote_analysis_date2_content">
                        @if(!empty($voteEventKey))
                            {{-- <h3>{{ trans('privateCbsVoteAnalysis.total_vote_statistics_by_date') }}</h3> --}}
                            <div id="statistics_by_top_chart-download-wrapper" class="chart-download-wrapper">
                                <a id="statistics_by_top_DownloadCSV" class="btn btn-flat btn-blue pull-right">
                                    <i class="fa fa-file-excel-o" aria-hidden="true"></i> {{ trans('privateUserAnalysis.download_csv') }}
                                </a>
                            </div>

                            <div id="statistics_by_date" style="height:600px">
                            </div>
                        @endif
                    </div>
                </div>
                <div role="tabpanel" class="tab-pane default-padding" id="tab_vote_analysis_hour2">
                    <div id="tabFiltersVoteAnalysisByHour" class="tabFilters">
                        <div class="row">
                            <div class="col-12 col-md-6 col-lg-3 col-xl-3">
                                <!-- Date range -->
                                <label>{{ trans('privateUserAnalysis.date_range') }}</label>
                                <div class="form-group">
                                    <div class='input-group date' id='daterangepicker2_wrapper'>
                                        <!--  onchange="javascript:requestChart2(this);"-->
                                        <input id="daterangepicker2" name="dr_vote_analysis_hour" type='text' class="form-control" value="" />
                                        <span class="input-group-addon"> <i class="fa fa-calendar" aria-hidden="true"></i></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-8 col-lg-9 col-xl-9">
                                <div class="pull-right margin-top-20">
                                    <div data-toggle="buttons">
                                        <label class="btn btn-primary">
                                            <input id="lastTwelveHours" name="timelapse2" type="radio" value="1" >
                                            {{ trans('privateUserAnalysis.last_twelve_hours') }}
                                        </label>
                                        <label class="btn btn-primary">
                                            <input id="lastDay" name="timelapse2" type="radio" value="1" >
                                            {{ trans('privateUserAnalysis.last_day') }}
                                        </label>
                                        <label class="btn btn-primary">
                                            <input id="lastTwoDays" name="timelapse2" type="radio" value="1" >
                                            {{ trans('privateUserAnalysis.last_two_days') }}
                                        </label>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="margin-top-30">
                            <div class="row">
                                <div class="col-12">
                                    <span class="text-bold">{{ trans('privateUserAnalysis.votes') }}</span>
                                </div>
                            </div>
                            <div class="box-filter-no-border">
                                <div class="row">
                                    <div class="col-12 col-md-6 col-lg-3 col-xl-2">
                                        {!! Form::oneSwitch2('count_voters_2',trans('privateUserAnalysis.voters'), 1, array("value"=> 1, "readonly"=>false))  !!}
                                    </div>
                                    <div class="col-12 col-md-6 col-lg-3 col-xl-2">
                                        {!! Form::oneSwitch2('total_2',trans('privateUserAnalysis.total'), 1, array("value"=> 1, "readonly"=>false))  !!}
                                    </div>
                                    <div class="col-12 col-md-6 col-lg-3 col-xl-2">
                                        {!! Form::oneSwitch2('positive_2',trans('privateUserAnalysis.positives'), 0, array("value"=> 1, "readonly"=>false))  !!}
                                    </div>
                                    <div class="col-12 col-md-6 col-lg-3 col-xl-2">
                                        {!! Form::oneSwitch2('negative_2',trans('privateUserAnalysis.negative'), 0, array("value"=> 1, "readonly"=>false))  !!}
                                    </div>
                                    <div class="col-12 col-md-6 col-lg-3 col-xl-2">
                                        {!! Form::oneSwitch2('balance_2',trans('privateUserAnalysis.balance'), 0, array("value"=> 1, "readonly"=>false))  !!}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="margin-top-30">
                            <div class="row">
                                <div class="col-12">
                                    <span class="text-bold">{{ trans('privateUserAnalysis.channels') }}</span>
                                </div>
                            </div>

                            <div class="margin-bottom-20">
                                <div class="row">
                                    <div class="col-12 col-md-6 col-lg-3 col-xl-3">
                                        {!! Form::oneSwitch2('web_2',trans('privateUserAnalysis.web'), 0, array("value"=> 1, "readonly"=>false))  !!}
                                    </div>
                                    <div class="col-12 col-md-6 col-lg-3 col-xl-3">
                                        {!! Form::oneSwitch2('pc_2',trans('privateUserAnalysis.pc'), 0, array("value"=> 1, "readonly"=>false))  !!}
                                    </div>
                                    <div class="col-12 col-md-6 col-lg-3 col-xl-3">
                                        {!! Form::oneSwitch2('mobile_2',trans('privateUserAnalysis.mobile'), 0, array("value"=> 1, "readonly"=>false))  !!}
                                    </div>
                                    <div class="col-12 col-md-6 col-lg-3 col-xl-3">
                                        {!! Form::oneSwitch2('tablet_2',trans('privateUserAnalysis.tablet'), 0, array("value"=> 1, "readonly"=>false))  !!}
                                    </div>
                                </div>
                            </div>

                            <div class="margin-top-20">
                                <div class="row">
                                    <div class="col-12 col-md-6 col-lg-3 col-xl-3">
                                        {!! Form::oneSwitch2('sms_2',trans('privateUserAnalysis.sms'), 0, array("value"=> 1, "readonly"=>false))  !!}
                                    </div>
                                    <div class="col-12 col-md-6 col-lg-3 col-xl-3">
                                        {!! Form::oneSwitch2('kiosk_2',trans('privateUserAnalysis.kiosk'), 0, array("value"=> 1, "readonly"=>false))  !!}
                                    </div>
                                    <div class="col-12 col-md-6 col-lg-3 col-xl-3">
                                        {!! Form::oneSwitch2('in_person_2',trans('privateUserAnalysis.in_person'), 0, array("value"=> 1, "readonly"=>false))  !!}
                                    </div>
                                    <div class="col-12 col-md-6 col-lg-3 col-xl-3">
                                        {!! Form::oneSwitch2('other_2',trans('privateUserAnalysis.other'), 0, array("value"=> 1, "readonly"=>false))  !!}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="margin-top-30">
                            <div class="row">
                                <div class="col-12">
                                    <button id="applyFilters2" class="btn btn-flat empatia margin-top-20" type="button">{{ trans('privateUserAnalysis.apply') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="tab_vote_analysis_hour2_content"></div>
                </div>
            </div>
            <!-- Ajax Loader for Tab -->
            <div id="tabOptionLoader" class="tabOptionLoader default-color" style="height: 100%; width: 100%; position: absolute; top: 0px; left: 0px; background-color: rgba(250, 250, 250, 0.35); z-index: 100; display: none;">
                <div class="text-center" style="display:flex;justify-content:center;align-items:center;height:100%;width:100%;">
                    <i class="fa fa-circle-o-notch fa-spin fa-3x fa-fw"></i>
                    <span class="sr-only">Loading...</span>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>

        $("#voteEventSelect").select2();
        $("#userParameterSelect").select2();

        $('a[data-toggle="tab"]').on('shown.bs.tab', function () {
            var id = $(this).attr('id');
            var voteEventKey = '{{$voteEventKey ?? null}}';
            if(voteEventKey.length == 0){
                voteEventKey = $( "#voteEventSelect" ).val();
            }
            if(id == "vote_analysis_date2"){
                @if(!empty($voteEventObj))
                    getVoteAnalysis(id,voteEventKey,'{{$voteEventObj->start_date}}','{{$voteEventObj->end_date}}');
                @endif
            } else if("vote_analysis_hour2") {
                var startDate = $("#daterangepicker2").val().split(" - ")[0];
                var endDate   = $("#daterangepicker2").val().split(" - ")[1];
                getVoteAnalysis2(id, voteEventKey, startDate, endDate);
            }
        });

        @if(empty($voteEvents))
        $('#nav-analysis').removeAttr('hidden');
        @endif

        $( "#voteEventSelect" ).change(function() {
            // update chart
            renderSelectedOptions();
        });

        $("#userParameterSelect").change(function() {
            var keySelected = $('#voteEventSelect').val();
            var navActive = $("#nav-analysis").find(".active").attr('id');
            getVoteAnalysis(navActive,keySelected);
        });

        function getVoteAnalysis(id,voteEventKey,startDate,endDate){
            var topic_key = "";
            if ($('#view_filter_2').is(":checked")  ) {
                topic_key = $("#topic_key").val();
            }

            var navActive = $("#nav-analysis").find(".active").attr('id');
            if(navActive == "vote_analysis_date2" ){
                var total = $('#total').is(":checked");
                var positive = $('#positive').is(":checked");
                var negative = $('#negative').is(":checked");
            } else if(navActive == "vote_analysis_hour2" ){
                var total = $('#total_2').is(":checked");
                var positive = $('#positive_2').is(":checked");
                var negative = $('#negative_2').is(":checked");
                var balance = $('#balance_2').is(":checked");
            }

            $.ajax({
                method: 'POST', // Type of response and matches what we said in the route
                url: "{{action('CbsController@getVoteAnalysis')}}", // This is the url we gave in the route
                data: {
                    statistics_type: id,
                    vote_event_key: voteEventKey,
                    parameter_key: '',
                    start_date: startDate,
                    end_date: endDate,
                    total: total,
                    positive: positive,
                    negative: negative,
                    balance: balance,
                    topic_key: topic_key,
                    view_submitted: $('input[name=view_submitted]:checked').val()
                },beforeSend: function () {
                    $("#tabOptionLoader").show();
                },
                success: function (response) { // What to do if we succeed
                    if(response == "false") {
                        var errorMessage = "{!! trim(preg_replace('/\s\s+/', ' ', trans('privateCbsVoteAnalysis.something_went_wrong'))) !!}";
                        $('#tab_' + id+'_content').html('<div class="chartMessage"><div><i class="fa fa-eye-slash" aria-hidden="true"></i> ' + errorMessage + '</div></div>');
                        toastr.error(errorMessage);
                    } else {
                        $('#tab_'+id+'_content').empty();
                        $('#tab_'+id+'_content').append(response);
                    }
                    $("#tabOptionLoader").hide();
                },
                error: function () { // What to do if we fail
                    var errorMessage = "{!! trim(preg_replace('/\s\s+/', ' ', trans('privateCbsVoteAnalysis.something_went_wrong'))) !!}";
                    $('#tab_'+id+'_content').html('<div class="chartMessage"><div><i class="fa fa-eye-slash" aria-hidden="true"></i> '+errorMessage+'</div></div>');
                    toastr.error(errorMessage);
                    $("#tabOptionLoader").hide();
                }
            });
        }

        function renderSelectedOptions(){
            var keySelected = "";
            if( typeof $("#voteEventSelect").val() != "undefined") {
                keySelected = $("#voteEventSelect").val();
            } else {
                keySelected = "{{$voteEventKey ?? null}}";
            }
            if(keySelected.length == 0){
                $('#nav-analysis').attr('hidden','hidden');
            } else {
                var navActive = $("#nav-analysis").find(".active").attr('id');
                var startDate  = "";
                var endDate = "";
                if(navActive=="vote_analysis_date2") {
                    var startDate = $("#daterangepicker1").val().split(" - ")[0];
                    var endDate = $("#daterangepicker1").val().split(" - ")[1];
                }else if(navActive=="vote_analysis_hour2") {
                    var startDate = $("#daterangepicker2").val().split(" - ")[0];
                    var endDate = $("#daterangepicker2").val().split(" - ")[1];
                }
                getVoteAnalysis(navActive,keySelected,startDate,endDate);
                $('#nav-analysis').removeAttr('hidden');
            }
        }

        function getVoteAnalysis2(id,voteEventKey,startDate,endDate){
            $.ajax({
                method: 'POST', // Type of response and matches what we said in the route
                url: "{{action('CbsController@getVoteAnalysis')}}", // This is the url we gave in the route
                data: {
                    statistics_type: id,
                    vote_event_key: voteEventKey,
                    parameter_key: '',
                    start_date: startDate,
                    end_date: endDate,
                    view_submitted: $('input[name=view_submitted]:checked').val()
                },beforeSend: function () {
                    var ajaxLoader = '<div class="chartLoader"><div><i class="fa fa-circle-o-notch fa-spin fa-3x fa-fw default-color"></i><span class="sr-only">Loading...</span></div></div>';
                    $('#tab_'+id+'_content').html(ajaxLoader);
                },
                success: function (response) { // What to do if we succeed
                    if(response == "false") {
                        var errorMessage = "{!! trim(preg_replace('/\s\s+/', ' ', trans('privateCbsVoteAnalysis.something_went_wrong'))) !!}";
                        $('#tab_' + id+'_content').html('<div class="chartMessage"><div><i class="fa fa-eye-slash" aria-hidden="true"></i> ' + errorMessage + '</div></div>');
                        toastr.error(errorMessage);
                    } else {
                        $('#tab_'+id+'_content').empty();
                        $('#tab_'+id+'_content').append(response);
                    }
                    // Remove loader
                    $(".chartLoader").remove();
                },
                error: function () { // What to do if we fail
                    var errorMessage = "{!! trim(preg_replace('/\s\s+/', ' ', trans('privateCbsVoteAnalysis.something_went_wrong'))) !!}";
                    $('#tab_'+id+'_content').html('<div class="chartMessage"><div><i class="fa fa-eye-slash" aria-hidden="true"></i> '+errorMessage+'</div></div>');
                    toastr.error(errorMessage);
                    $(".chartLoader").remove();
                }
            });
        }
    </script>

    @if(!empty($voteEventKey))
        <script>
            var startDate = $("#daterangepicker1").val().split(" - ")[0];
            var endDate   = $("#daterangepicker1").val().split(" - ")[1];
            var navActive = $("#nav-analysis").find(".active").attr('id');
            var voteEventKey = '{{$voteEventKey ?? null}}';
            if(voteEventKey.length == 0){
                voteEventKey = $( "#voteEventSelect" ).val();
            }
            // getVoteAnalysis(navActive,voteEventKey, startDate, endDate);
            window.voteEventKey = "{{ $voteEventKey }}";
        </script>
        @include('private.cbs.cbVoteAnalysis2.details.cbDetailsScript')
    @endif

    @if(!empty(Session::get("voteEventKey")) && !empty($voteEvents) && array_key_exists(Session::get("voteEventKey"),$voteEvents))
        <script>
            $( document ).ready(function() {
                renderSelectedOptions();
            });
        </script>
    @endif

    <!-- Date Range Picker - JavaScript -->
    <script>
        $( document ).ready(function() {
            // Date Range pickers
            $('input[name="dr_vote_analysis_date2"]').daterangepicker({
                timePicker: false,
                timePickerIncrement: 5,
                locale: {
                    format: 'YYYY-MM-DD'
                },
                @if(!empty($voteEventObj->start_date))
                minDate: "{{$voteEventObj->start_date}}", /*  minDate: new Date(2017,11,1),*/
                @endif
                @if(!empty($voteEventObj->end_date))
                maxDate: "{{$voteEventObj->end_date}}",
                @endif
            }, myCallback);

            function myCallback(start, end) {
                var startDate = start.format('YYYY-MM-DD');
                var endDate = end.format('YYYY-MM-DD');
                requestChart(startDate, endDate);
                $('#oneWeek').prop('checked', false);
                $('#oneMonth').prop('checked', false);
                $('#twoMonths').prop('checked', false);
            }

            var dateArrayRange = getCurrentDateRangeForHoursCharts();
            startDate = dateArrayRange[0];
            endDate = dateArrayRange[1];

            // Date Range pickers
            $('input[name="dr_vote_analysis_hour"]').daterangepicker({
                timePicker: true,
                timePicker24Hour: true,
                timePickerIncrement: 5,
                startDate: startDate,
                endDate: endDate,
                dateLimit: {
                    days: 2
                },
                locale: {
                    format: 'YYYY-MM-DD HH:mm'
                },
                @if(!empty($voteEventObj->start_date))
                minDate: "{{$voteEventObj->start_date}}",
                @endif
                @if(!empty($voteEventObj->end_date))
                maxDate: "{{$voteEventObj->end_date}}",
                @endif
            }, myCallback2);

            function myCallback2(start, end) {
                var startDate = start.format('YYYY-MM-DD');
                var endDate = end.format('YYYY-MM-DD');
                requestChart2(startDate, endDate);
            }

        });

        function requestChart(startDate, endDate){
            var navActive = $("#nav-analysis").find(".active").attr('id');
            var voteEventKey = '{{$voteEventKey ?? null}}';
            if(voteEventKey.length == 0){
                voteEventKey = $("#voteEventSelect").val();
            }
            getVoteAnalysis(navActive,voteEventKey, startDate, endDate);
        }

        function requestChart2(startDate, endDate){
            var navActive = $("#nav-analysis").find(".active").attr('id');
            var voteEventKey = '{{$voteEventKey ?? null}}';
            if(voteEventKey.length == 0){
                voteEventKey = $("#voteEventSelect").val();
            }
            getVoteAnalysis2(navActive,voteEventKey, startDate, endDate);
        }

        $( "#applyFilters" ).click(function() {
            var startDate = $("#daterangepicker1").val().split(" - ")[0];
            var endDate   = $("#daterangepicker1").val().split(" - ")[1];
            requestChart(startDate, endDate);
        });


        $( "#applyFilters2" ).click(function() {
            var startDate = $("#daterangepicker2").val().split(" - ")[0];
            var endDate   = $("#daterangepicker2").val().split(" - ")[1];
            requestChart2(startDate, endDate);
        });

        $("#oneWeek").change(function() {
            if($(this).val() == 1){
                var d = new Date();
                var endDate = d.getFullYear() + "-" + addZero(d.getMonth()+1) + "-" + addZero(d.getDate());
                d.setDate(d.getDate() - 7);
                var startDate2 = d.getFullYear() + "-" + addZero(d.getMonth()+1) + "-" + addZero(d.getDate());
                // $("#daterangepicker1").val(startDate2+" - "+endDate);
                $("#daterangepicker2").data('daterangepicker').setStartDate(startDate2);
                $("#daterangepicker2").data('daterangepicker').setEndDate(endDate);
            }
        });

        $("#oneMonth").change(function() {
            if($(this).val() == 1){
                var d = new Date();
                var endDate = d.getFullYear() + "-" + addZero(d.getMonth()+1) + "-" + addZero(d.getDate());
                d.setDate(d.getDate() - 31);
                var startDate2 = d.getFullYear() + "-" + addZero(d.getMonth()+1) + "-" + addZero(d.getDate());
                // $("#daterangepicker1").val(startDate2+" - "+endDate);
                $("#daterangepicker2").data('daterangepicker').setStartDate(startDate2);
                $("#daterangepicker2").data('daterangepicker').setEndDate(endDate);
            }
        });

        $("#twoMonths").change(function() {
            if($(this).val() == 1){
                var d = new Date();
                var endDate = d.getFullYear() + "-" + addZero(d.getMonth()+1) + "-" + addZero(d.getDate());
                d.setDate(d.getDate() - 62);
                var startDate2 = d.getFullYear() + "-" + addZero(d.getMonth()+1) + "-" + addZero(d.getDate());
                // $("#daterangepicker1").val(startDate2+" - "+endDate);
                $("#daterangepicker2").data('daterangepicker').setStartDate(startDate2);
                $("#daterangepicker2").data('daterangepicker').setEndDate(endDate);
            }
        });

        @if(!empty($voteEventObj))
        $("#votePeriod").change(function() {
            if($(this).val() == 1){
                $("#daterangepicker1").val("{{ substr($voteEventObj->start_date,0,10) }} - {{  substr($voteEventObj->end_date,0,10) }}");
            }
        });
        @endif


        $("#lastDay").change(function() {
            if($(this).val() == 1){
                var d = new Date();
                var h = addZero(d.getHours());
                var m = addZero(d.getMinutes());
                var endDate = d.getFullYear() + "-" + addZero(d.getMonth()+1) + "-" + addZero(d.getDate())+" "+h + ":" + m;
                d.setDate(d.getDate() - 1);
                var startDate2 = d.getFullYear() + "-" + addZero(d.getMonth()+1) + "-" + addZero(d.getDate()) +" "+h + ":" + m;
                //$("#daterangepicker2").val(startDate2+" - "+endDate);
                $("#daterangepicker2").data('daterangepicker').setStartDate(startDate2);
                $("#daterangepicker2").data('daterangepicker').setEndDate(endDate);
            }
        });

        $("#lastTwoDays").change(function() {
            if($(this).val() == 1){
                var d = new Date();
                var h = addZero(d.getHours());
                var m = addZero(d.getMinutes());
                var endDate = d.getFullYear() + "-" + addZero(d.getMonth()+1) + "-" + addZero(d.getDate())+" "+h + ":" + m;
                d.setDate(d.getDate() - 2);
                var startDate2 = d.getFullYear() + "-" + addZero((d.getMonth()+1)) + "-" + addZero(d.getDate())+" "+h + ":" + m;
                // $("#daterangepicker2").val(startDate2+" - "+endDate);
                $("#daterangepicker2").data('daterangepicker').setStartDate(startDate2);
                $("#daterangepicker2").data('daterangepicker').setEndDate(endDate);
            }
        });

        $("#lastTwelveHours").change(function() {
            if($(this).val() == 1){
                var d = new Date();
                var h = addZero(d.getHours());
                var m = addZero(d.getMinutes());
                var endDate = d.getFullYear() + "-" + addZero(d.getMonth()+1) + "-" + addZero(d.getDate())+" "+h + ":" + m;
                d.setHours(d.getHours() - 12);
                var startDate2 = d.getFullYear() + "-" + addZero(d.getMonth()+1) + "-" + addZero(d.getDate())+" "+h + ":" + m;
                // $("#daterangepicker2").val(startDate2+" - "+endDate);
                $("#daterangepicker2").data('daterangepicker').setStartDate(startDate2);
                $("#daterangepicker2").data('daterangepicker').setEndDate(endDate);
            }
        });

        function getCurrentDateRangeForHoursCharts(){
            var d = new Date();
            var h = addZero(d.getHours());
            var m = addZero(d.getMinutes());
            var endDate = d.getFullYear() + "-" + addZero(d.getMonth()+1) + "-" + addZero(d.getDate())+" "+h + ":" + m;
            d.setDate(d.getDate() - 1);
            var startDate = d.getFullYear() + "-" + addZero(d.getMonth()+1) + "-" + addZero(d.getDate())+" "+h + ":" + m;
            return [startDate, endDate];
        }
    </script>
@endsection