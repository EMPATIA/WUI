<!-- CB Configurations -->

{{-- @if($configuration->code != "general_configurations") --}}
    @foreach($configuration->configurations as $option)

        @if($option->code == "submit_proposals_dates")
            <div style="margin-bottom:15px;">
                <div class="row">
                    <div class="col-sm-12 col-md-6">
                        <label class="input-group btn-group-vertical">{{$option->title}}</label>
                    </div>
                    <div class="col-sm-12 col-md-3">
                        <div class="onoffswitch">
                            <input id="submitProposal" name="configuration_{{$option->id}}" type="checkbox"
                                class="onoffswitch-checkbox" value="1" onclick="submitProposalFunction()">
                            <label for="submitProposal" class="onoffswitch-label">
                                <span class="onoffswitch-inner"></span>
                                <span class="onoffswitch-switch"></span>
                            </label>
                        </div>
                    </div>
                </div>
                <div style="margin-bottom:15px; display:none; box-body p-0" id="submitProposalDiv">
                    <dt><label>{{trans('privateCbs.submit_proposals_dates_configurations')}}</label></dt>
                    <div class=" p-0">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="col-md-6">
                                    <div class="card-body"
                                        style="padding-top: 5px; padding-bottom: 0px;">
            
                                        <div id="voteDate">
                                            <div class="card-block">
                                                <div class='col-md-12'>
                                                    <label>{{ trans('privateCbs.start_submit_proposal') }}</label>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <div class='input-group date'
                                                                    id='datetimepicker7'>
                                                                    <input type='text'
                                                                        value=""
                                                                        class="form-control"
                                                                        name="start_submit_proposal"/>
                                                                    <span class="input-group-addon">
                                                                            <i class="fa fa-calendar" aria-hidden="true"></i>
                                                                        </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class='col-md-12'>
                                                    <label>{{ trans('privateCbs.end_submit_proposal') }}</label>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <div class='input-group date'
                                                                    id='datetimepicker8'>
                                                                    <input type='text'
                                                                        value=""
                                                                        class="form-control"
                                                                        name="end_submit_proposal"/>
                                                                    <span class="input-group-addon">
                                                                            <i class="fa fa-calendar" aria-hidden="true"></i>
                                                                        </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @elseif($option->code == "technical_analysis_dates")
            <div style="margin-bottom:15px;">
                <div class="row">
                    <div class="col-sm-12 col-md-6">
                        <label class="input-group btn-group-vertical">{{$option->title}}</label>
                    </div>
                    <div class="col-sm-12 col-md-3">
                        <div class="onoffswitch">
                            <input id="technicalAnalysis" name="configuration_{{$option->id}}" type="checkbox"
                                class="onoffswitch-checkbox" value="1" onclick="technicalAnalysisFunction()">
                            <label for="technicalAnalysis" class="onoffswitch-label">
                                <span class="onoffswitch-inner"></span>
                                <span class="onoffswitch-switch"></span>
                            </label>
                        </div>
                    </div>
                </div>
                <div style="margin-bottom:15px; display:none;box-body p-0" id="technicalAnalysisDiv">
                    <dt><label>{{trans('privateCbs.technical_analysis_dates_configurations')}}</label></dt>
                    <div class=" p-0">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="col-md-6">
                                    <div class="card-body"
                                        style="padding-top: 5px; padding-bottom: 0px;">
            
                                        <div id="voteDate">
                                            <div class="card-block">
                                                <div class='col-md-12'>
                                                    <label>{{ trans('privateCbs.start_technical_analysis') }}</label>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <div class='input-group date'
                                                                    id='datetimepicker9'>
                                                                    <input type='text'
                                                                        value=""
                                                                        class="form-control"
                                                                        name="start_technical_analysis"/>
                                                                    <span class="input-group-addon">
                                                                            <i class="fa fa-calendar" aria-hidden="true"></i>
                                                                        </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class='col-md-12'>
                                                    <label>{{ trans('privateCbs.end_technical_analysis') }}</label>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <div class='input-group date'
                                                                    id='datetimepicker10'>
                                                                    <input type='text'
                                                                        value=""
                                                                        class="form-control"
                                                                        name="end_technical_analysis"/>
                                                                    <span class="input-group-addon">
                                                                            <i class="fa fa-calendar" aria-hidden="true"></i>
                                                                        </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @elseif($option->code == "complaint_dates")
            <div style="margin-bottom:15px;">
                <div class="row">
                    <div class="col-sm-12 col-md-6">
                        <label class="input-group btn-group-vertical">{{$option->title}}</label>
                    </div>
                    <div class="col-sm-12 col-md-3">
                        <div class="onoffswitch">
                            <input id="complaint" name="configuration_{{$option->id}}" type="checkbox"
                                class="onoffswitch-checkbox" value="1" onclick="complaintFunction()">
                            <label for="complaint" class="onoffswitch-label">
                                <span class="onoffswitch-inner"></span>
                                <span class="onoffswitch-switch"></span>
                            </label>
                        </div>
                    </div>
                </div>
                <div style="margin-bottom:15px; display:none;box-body p-0" id="complaintDiv">
                    <dt><label>{{trans('privateCbs.complaint_dates_configurations')}}</label></dt>
                    <div class=" p-0">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="col-md-6">
                                    <div class="card-body"
                                        style="padding-top: 5px; padding-bottom: 0px;">
            
                                        <div id="voteDate">
                                            <div class="card-block">
                                                <div class='col-md-12'>
                                                    <label>{{ trans('privateCbs.start_complaint') }}</label>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <div class='input-group date'
                                                                    id='datetimepicker11'>
                                                                    <input type='text'
                                                                        value=""
                                                                        class="form-control"
                                                                        name="start_complaint"/>
                                                                    <span class="input-group-addon">
                                                                            <i class="fa fa-calendar" aria-hidden="true"></i>
                                                                        </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class='col-md-12'>
                                                    <label>{{ trans('privateCbs.end_complaint') }}</label>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <div class='input-group date'
                                                                    id='datetimepicker12'>
                                                                    <input type='text'
                                                                        value=""
                                                                        class="form-control"
                                                                        name="end_complaint"/>
                                                                    <span class="input-group-addon">
                                                                            <i class="fa fa-calendar" aria-hidden="true"></i>
                                                                        </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @elseif($option->code == "vote_dates")
            <div style="margin-bottom:15px;">
                <div class="row">
                    <div class="col-sm-12 col-md-6">
                        <label class="input-group btn-group-vertical">{{$option->title}}</label>
                    </div>
                    <div class="col-sm-12 col-md-3">
                        <div class="onoffswitch">
                            <input id="vote" name="configuration_{{$option->id}}" type="checkbox"
                                class="onoffswitch-checkbox" value="1" onclick="voteFunction()">
                            <label for="vote" class="onoffswitch-label">
                                <span class="onoffswitch-inner"></span>
                                <span class="onoffswitch-switch"></span>
                            </label>
                        </div>
                    </div>
                </div>
                <div style="margin-bottom:15px; display:none;box-body p-0" id="voteDiv">
                    <dt><label>{{trans('privateCbs.vote_dates_configuration')}}</label></dt>
                    <div class=" p-0">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="col-md-6">
                                    <div class="card-body"
                                        style="padding-top: 5px; padding-bottom: 0px;">
            
                                        <div id="voteDate">
                                            <div class="card-block">
                                                <div class='col-md-12'>
                                                    <label>{{ trans('privateCbs.datetimepicker_start_vote') }}</label>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <div class='input-group date'
                                                                    id='datetimepicker5'>
                                                                    <input type='text'
                                                                        value=""
                                                                        class="form-control"
                                                                        name="start_vote"/>
                                                                    <span class="input-group-addon">
                                                                            <i class="fa fa-calendar" aria-hidden="true"></i>
                                                                        </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class='col-md-12'>
                                                    <label>{{ trans('privateCbs.datetimepicker_end_vote') }}</label>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <div class='input-group date'
                                                                    id='datetimepicker6'>
                                                                    <input type='text'
                                                                        value=""
                                                                        class="form-control"
                                                                        name="end_vote"/>
                                                                    <span class="input-group-addon">
                                                                            <i class="fa fa-calendar" aria-hidden="true"></i>
                                                                        </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @elseif($option->code == "show_results_dates")
            <div style="margin-bottom:15px;">
                <div class="row">
                    <div class="col-sm-12 col-md-6">
                        <label class="input-group btn-group-vertical">{{$option->title}}</label>
                    </div>
                    <div class="col-sm-12 col-md-3">
                        <div class="onoffswitch">
                            <input id="showResults" name="configuration_{{$option->id}}" type="checkbox"
                                class="onoffswitch-checkbox" value="1" onclick="showResultsFunction()">
                            <label for="showResults" class="onoffswitch-label">
                                <span class="onoffswitch-inner"></span>
                                <span class="onoffswitch-switch"></span>
                            </label>
                        </div>
                    </div>
                </div>
                <div style="margin-bottom:15px; display:none;box-body p-0" id="showResultsDiv">
                    <dt><label>{{trans('privateCbs.show_results_dates_configurations')}}</label></dt>
                    <div class=" p-0">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="col-md-6">
                                    <div class="card-body"
                                        style="padding-top: 5px; padding-bottom: 0px;">
            
                                        <div id="voteDate">
                                            <div class="card-block">
                                                <div class='col-md-12'>
                                                    <label>{{ trans('privateCbs.start_show_results') }}</label>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <div class='input-group date'
                                                                    id='datetimepicker13'>
                                                                    <input type='text'
                                                                        value=""
                                                                        class="form-control"
                                                                        name="start_show_results"/>
                                                                    <span class="input-group-addon">
                                                                            <i class="fa fa-calendar" aria-hidden="true"></i>
                                                                        </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class='col-md-12'>
                                                    <label>{{ trans('privateCbs.end_show_results') }}</label>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <div class='input-group date'
                                                                    id='datetimepicker14'>
                                                                    <input type='text'
                                                                        value=""
                                                                        class="form-control"
                                                                        name="end_show_results"/>
                                                                    <span class="input-group-addon">
                                                                            <i class="fa fa-calendar" aria-hidden="true"></i>
                                                                        </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        
        @elseif($option->code == "allow_filter_status")
            <div style="margin-bottom:15px;">
                <div class="row">
                    <div class="col-sm-12 col-md-6">
                        <label class="input-group btn-group-vertical">{{$option->title}}</label>
                    </div>
                    <div class="col-sm-12 col-md-3">
                        <div class="onoffswitch">
                            <input id="categoryTypeFilter" name="configuration_{{$option->id}}" type="checkbox"
                                   class="onoffswitch-checkbox" value="1" onclick="categoryFilter()">
                            <label for="categoryTypeFilter" class="onoffswitch-label">
                                <span class="onoffswitch-inner"></span>
                                <span class="onoffswitch-switch"></span>
                            </label>
                        </div>
                    </div>
                </div>
                <div id="categoryFilterDiv" class="box-body p-0"></div>
            </div>

        @else()
            <div style="margin-bottom:15px;">
                {!! Form::oneSwitch("configuration_".$option->id,
                                    array("name" => $option->title, "description" => $option->description ),
                                    in_array($option->id, (isset($cbConfigurations) ? $cbConfigurations : []) ),
                                    array("groupClass"=>"row",
                                          "labelClass" => "col-sm-12 col-md-6",
                                          "switchClass" => "col-sm-12 col-md-3"  ) )!!}
            </div>
        @endif
    @endforeach

    

   

    

@if($configuration->code == "topic_options")
    <div class=" p-0">
        <div class="row">
            <div class="col-md-12 pl-0">
                <div class="col-md-6 pl-0">
                    <div class="card-body" style="padding-top: 5px; padding-bottom: 0px;">
                        <div id="topicDate">
                            <div class="card-block">
                                <div class='col-md-12'>
                                    <label>{{ trans('privateCbs.datetimepicker_start_topic') }}</label>
                                    <div class="row ">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <div class='input-group date'
                                                     id='datetimepicker1'>
                                                    <input type='text'
                                                           value=""
                                                           class="form-control"
                                                           name="start_topic"/>
                                                    <span class="input-group-addon">
                                                                <i class="fa fa-calendar" aria-hidden="true"></i>
                                                            </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class='col-md-12'>
                                        <label>{{ trans('privateCbs.datetimepicker_end_topic') }}</label>
                                        <div class="row ">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <div class='input-group date'
                                                         id='datetimepicker2'>
                                                        <input type='text'
                                                               value=""
                                                               class="form-control"
                                                               name="end_topic">
                                                        <span class="input-group-addon">
                                                                <i class="fa fa-calendar" aria-hidden="true"></i>
                                                            </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class='col-md-12'>
                                        <label>{{ trans('privateCbs.datetimepicker_range_topic_edit') }}</label>
                                        <div class="row ">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <div class='input-group date'
                                                         id='datetimepicker3'>
                                                        <input type='text'
                                                               value=""
                                                               class="form-control"
                                                               name="start_topic_edit">
                                                        <span class="input-group-addon">
                                                                <i class="fa fa-calendar" aria-hidden="true"></i>
                                                            </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <div class='input-group date'
                                                         id='datetimepicker4'>
                                                        <input type='text'
                                                               value=""
                                                               class="form-control"
                                                               name="end_topic_edit">
                                                        <span class="input-group-addon">
                                                                <i class="fa fa-calendar" aria-hidden="true"></i>
                                                            </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

<script>

    function submitProposalFunction() {
        if($("#submitProposal").is(":checked") == true){
            $("#submitProposalDiv").show();
        }else{
            $("#submitProposalDiv").hide();
        }
    }

    function technicalAnalysisFunction() {
        if($("#technicalAnalysis").is(":checked") == true){
            $("#technicalAnalysisDiv").show();
        }else{
            $("#technicalAnalysisDiv").hide();
        }
    }

    function complaintFunction() {
        if($("#complaint").is(":checked") == true){
            $("#complaintDiv").show();
        }else{
            $("#complaintDiv").hide();
        }
    }

    function voteFunction() {
        if($("#vote").is(":checked") == true){
            $("#voteDiv").show();
        }else{
            $("#voteDiv").hide();
        }
    }

    function showResultsFunction() {
        if($("#showResults").is(":checked") == true){
            $("#showResultsDiv").show();
        }else{
            $("#showResultsDiv").hide();
        }
    }

    function categoryFilter() {

        if ($("#categoryTypeFilter:checked").length == 1) {
            $.ajax({
                method: 'get', // Type of response and matches what we said in the route
                url: '{{action('CbsController@categoryFilter',[ 'type' => $type ])}}', // This is the url we gave in the route
                data:
                    {
                        "_token": "{{ csrf_token() }}"
                        // 'status' : status->id
                    },// a JSON object to send back
                success: function (response) { // What to do if we succeed
                    console.log(response);
                    $("#categoryFilterDiv").append(response);
                },
                error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                }
            });
        }
        else {
            $("#categoryFilterDiv").empty();
        }
    }

    $(function () {
        $('#datetimepicker1').datetimepicker(
            {
                format: 'YYYY-MM-DD HH:mm:ss',
                minDate: getFormattedDate(new Date())
            }
        );
        $('#datetimepicker2').datetimepicker({
            useCurrent: false, //Important! See issue #1075

            format: 'YYYY-MM-DD HH:mm:ss',
            minDate: getFormattedDate(new Date())

        });
        $('#datetimepicker5').datetimepicker(
            {
                format: 'YYYY-MM-DD HH:mm:ss',
                minDate: getFormattedDate(new Date())
            }
        );
        $('#datetimepicker6').datetimepicker({
            useCurrent: false, //Important! See issue #1075,

            format: 'YYYY-MM-DD HH:mm:ss',
            minDate: getFormattedDate(new Date())

        });
        $('#datetimepicker3').datetimepicker(
            {
                format: 'YYYY-MM-DD HH:mm:ss',
                minDate: getFormattedDate(new Date())
            }
        );
        $('#datetimepicker4').datetimepicker({
            useCurrent: false, //Important! See issue #1075

            format: 'YYYY-MM-DD HH:mm:ss',
            minDate: getFormattedDate(new Date())

        });
        
        $('#datetimepicker7').datetimepicker({
            useCurrent: false, //Important! See issue #1075

            format: 'YYYY-MM-DD HH:mm:ss',
            minDate: getFormattedDate(new Date())

        });
        $('#datetimepicker8').datetimepicker({
            useCurrent: false, //Important! See issue #1075

            format: 'YYYY-MM-DD HH:mm:ss',
            minDate: getFormattedDate(new Date())

        });
        $('#datetimepicker9').datetimepicker({
            useCurrent: false, //Important! See issue #1075

            format: 'YYYY-MM-DD HH:mm:ss',
            minDate: getFormattedDate(new Date())

        });
        $('#datetimepicker10').datetimepicker({
            useCurrent: false, //Important! See issue #1075

            format: 'YYYY-MM-DD HH:mm:ss',
            minDate: getFormattedDate(new Date())

        });
        $('#datetimepicker11').datetimepicker({
            useCurrent: false, //Important! See issue #1075

            format: 'YYYY-MM-DD HH:mm:ss',
            minDate: getFormattedDate(new Date())

        });
        $('#datetimepicker12').datetimepicker({
            useCurrent: false, //Important! See issue #1075

            format: 'YYYY-MM-DD HH:mm:ss',
            minDate: getFormattedDate(new Date())

        });
        $('#datetimepicker13').datetimepicker({
            useCurrent: false, //Important! See issue #1075

            format: 'YYYY-MM-DD HH:mm:ss',
            minDate: getFormattedDate(new Date())

        });
        $('#datetimepicker14').datetimepicker({
            useCurrent: false, //Important! See issue #1075

            format: 'YYYY-MM-DD HH:mm:ss',
            minDate: getFormattedDate(new Date())

        });

        $("#datetimepicker1").on("dp.change", function (e) {
            $('#datetimepicker2').data("DateTimePicker").minDate(e.date);
        });
        $("#datetimepicker2").on("dp.change", function (e) {
            $('#datetimepicker1').data("DateTimePicker").maxDate(e.date);
        });
        $("#datetimepicker3").on("dp.change", function (e) {
            $('#datetimepicker4').data("DateTimePicker").minDate(e.date);
        });
        $("#datetimepicker4").on("dp.change", function (e) {
            $('#datetimepicker3').data("DateTimePicker").maxDate(e.date);
        });

        $("#datetimepicker5").on("dp.change", function (e) {
            $('#datetimepicker6').data("DateTimePicker").minDate(e.date);
        });
        $("#datetimepicker6").on("dp.change", function (e) {
            $('#datetimepicker5').data("DateTimePicker").maxDate(e.date);
        });

        $("#datetimepicker7").on("dp.change", function (e) {
            $('#datetimepicker8').data("DateTimePicker").minDate(e.date);
        });
        $("#datetimepicker8").on("dp.change", function (e) {
            $('#datetimepicker7').data("DateTimePicker").maxDate(e.date);
        });
        $("#datetimepicker9").on("dp.change", function (e) {
            $('#datetimepicker10').data("DateTimePicker").minDate(e.date);
        });
        $("#datetimepicker10").on("dp.change", function (e) {
            $('#datetimepicker9').data("DateTimePicker").maxDate(e.date);
        });
        $("#datetimepicker11").on("dp.change", function (e) {
            $('#datetimepicker12').data("DateTimePicker").minDate(e.date);
        });
        $("#datetimepicker12").on("dp.change", function (e) {
            $('#datetimepicker11').data("DateTimePicker").maxDate(e.date);
        });
        $("#datetimepicker13").on("dp.change", function (e) {
            $('#datetimepicker14').data("DateTimePicker").minDate(e.date);
        });
        $("#datetimepicker14").on("dp.change", function (e) {
            $('#datetimepicker13').data("DateTimePicker").maxDate(e.date);
        });

        function getFormattedDate(date) {
            var day = date.getDate();
            var month = date.getMonth() + 1;
            var year = date.getFullYear().toString().slice(2);
            var output = year + '-' + month + '-' + day;
        }
    });

</script>