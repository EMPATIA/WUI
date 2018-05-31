@extends('private._private.index')

@section('header_styles')
    <link rel="stylesheet" type="text/css" href="{!! asset("css/daterangepicker/daterangepicker.css") !!}" />

    <style>
        .entities-counter{
            font-size:22px;
            font-weight: bold;
        }
        .box-body-entities {
            padding: 10px;
        }

        .default-chart{
            height:400px;
            width:100%;
            margin-bottom:40px
        }
    </style>
@endsection

@section('content')

    <div class="row row-sm-eq-height row-md-eq-height row-lg-eq-height">
        <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
            <!-- Total users -->
            <div class="dashboard-box">
                <div class="row">
                    <div class="col-4 col-sm-12 col-lg-4 text-xs-center">
                        <img src="{{ asset('/images/private/backoffice-dashboard_icon-users.svg') }}" alt="Total users">
                    </div>
                    <div class="col-8 col-sm-12 col-lg-8">
                        <span class="default-color">{{ trans("privateUserAnalysis.total_users") }}</span>
                        <span class="info-box-number registered">{{$totalUsers}}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
            <!--  Total page access -->
            <div class="dashboard-box">
                <div class="row">
                    <div class="col-4 col-sm-12 col-lg-4 text-xs-center">
                        <img src="{{ asset('/images/private/backoffice-dashboard_icon-pages.svg') }}" alt="Total page access<">
                    </div>
                    <div class="col-8 col-sm-12 col-lg-8">
                        <span class="default-color">{{ trans("privateUserAnalysis.total_page_access") }}</span>
                        <span class="info-box-number logged_users">{{$total_page_access}}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
            <!-- Average page access per second -->
            <div class="dashboard-box">
                <div class="row">
                    <div class="col-4 col-sm-12 col-lg-4 text-xs-center">
                        <img src="{{ asset('/images/private/backoffice-dashboard_icon-pageseconds.svg') }}" alt="Average page access per second">
                    </div>
                    <div class="col-8 col-sm-12 col-lg-8">
                        <span class="default-color">{{ trans("privateUserAnalysis.average_page_access_per_second") }}</span>
                        <span class="info-box-number ideas">{{round($total_page_access / (5*60),2)}}</span>
                    </div>
                </div>
            </div>
        </div>
        {{--<div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
            <!-- Total comments -->
            <div class="dashboard-box">
                <div class="row">
                    <div class="col-4 col-sm-12 col-lg-4 text-xs-center">
                        <img src="{{ asset('/images/private/backoffice-dashboard_icon-refresh.svg') }}" alt="timelapse">
                    </div>
                    <div class="col-8 col-sm-12 col-lg-8">
                        <select id="timelapse" name="timelapse"  class="select2-default" style="width:100%;">
                            <option value="5">{{ trans("privateUserAnalysis.last_five_min")  }}</option>
                            <option value="3">{{ trans("privateUserAnalysis.last_three_min") }}</option>
                            <option value="2">{{ trans("privateUserAnalysis.last_two_min") }}</option>
                            <option value="1">{{ trans("privateUserAnalysis.last_one_min")  }}</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>--}}
    </div>

    <div class="row no-gutters dashboard ui-sortable">
        @foreach($analysisData as $entityKey => $entityData)
            <div class="col-12 col-lg-6 col-xl-4 ElementToDrag" data-item-id="28">
                <div class="box box-info box-side-by-side dashboardbox">
                    <div class="box-header">
                        <h3 class="dashboard-title text-truncate">
                            {{$entityData->entity_name}}
                        </h3>

                        <div class="box-tools pull-right">
                            <span class="entities-counter default-color">{{count($totalUsersByEntity->$entityKey)}}</span>
                        </div>

                        {{--<div class="box-tools pull-right">
                            <button class="btn btn-box-tool reload-dashboard-button dash-btn" onclick="loadDashboardElement28()">
                                <i class="fa fa-refresh" aria-hidden="true"></i>
                            </button>
                            <button class="btn btn-box-tool dash-btn config-dash-btn" data-id="28">
                                <i class="fa fa-cog" aria-hidden="true"></i>
                            </button>
                            <button class="btn btn-box-tool draggable-dashboard-button dash-btn ui-sortable-handle">
                                <i class="fa fa-arrows" aria-hidden="true"></i>
                            </button>
                            <button type="button" class="btn btn-box-tool dash-btn" data-widget="remove" onclick="removeDashboardItem(28);">
                                <i class="fa fa-times"></i>
                            </button>
                        </div>--}}
                    </div>
                    <div class="box-body box-body-entities">
                        <div class="dashboard-content">
                            <div id="dash_board_element_28" class="dash-board-list"><div class="dashboard-scrollable">

                                    <div class="dashboard-item-wrapper">
                                        <div class="row">
                                            <div class="col-12 ellipis"><b>{{ trans("privateUserAnalysis.name") }}</b></div>
                                        </div>
                                    </div>

                                    @foreach($entityData->sites as $site)
                                        @foreach($site->users->logged as $user)
                                            <div class="dashboard-item-wrapper">
                                                <div class="row">
                                                    <div class="col-12 col-md-4 col-lg-6 ellipis">
                                                        <div class="dashboard-text ellipis">
                                                            <a href="{{ action("UsersController@show", $user->user_key ) }}">
                                                                {{$user->user_name}}
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                        @foreach($site->users->not_logged as $user)
                                            <div class="dashboard-item-wrapper">
                                                <div class="row">
                                                    <div class="col-12 col-md-4 col-lg-6 ellipis">
                                                        <div class="dashboard-text ellipis">
                                                            Anonymous {{$user->php_session_id}}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endforeach
                                </div>

                                {{--<div class="view_full_list">
                                    <div class="row">
                                        <div class="col-12">
                                            <a href="https://ilidio.empatia-dev.onesource.pt/private/type/idea/cbs/40WYZJsG1a8kHs4ATF7QRosdh5DQ76EA/showTopics" class="btn-seemore pull-right">private.view_full_list</a>
                                        </div>
                                    </div>
                                </div>--}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>


    <div class="margin-top-20">
        <div class="box box-primary">
            <!-- Title -->
            <div class="box-header">
                <h3 class="box-title text-truncate">
                    <i class="fa fa-bar-chart" aria-hidden="true"></i> {{ trans('privateUserAnalysis.charts') }}
                </h3>
            </div>
            <!-- Filters -->
            <div class="default-padding">
                <div class="row">
                    <div class='col-12 col-sm-12 col-md-6 col-lg-5 col-xl-4'>
                        <label>{{ trans('privateUserAnalysis.date_range') }}</label>
                        <div class="form-group">
                            <div class='input-group date' id='daterangepicker'  >
                                <input id="daterangepicker" name="daterange" type='text' class="form-control" value="" onchange="javascript:requestCharts();" />
                                <span class="input-group-addon">
                                <i class="fa fa-calendar" aria-hidden="true"></i>
                            </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="clearfix">&nbsp;</div>

                <!-- Chart for «TotalUsers» -->
                <h3 class="dashboard-title margin-top-20">{{ trans("privateUserAnalysis.total_users") }}</h3>
                <!-- Download -->
                <div class="chart-download-wrapper-total_users">
                    <a id="downloadCSV-total-users"  class="btn btn-flat btn-blue pull-right" onclick="javascript:downloadCsvTotalUsers();">
                        <i class="fa fa-file-excel-o" aria-hidden="true"></i> {{ trans('privateCbsVoteAnalysis.download_csv') }}
                    </a>
                </div>
                <div class="clearfix"></div>
                <div id="vizTotalUsers" class="default-chart"></div>
                <div class="clearfix"></div>

                <!-- Chart for «TotalPageAccess» -->
                <h3 class="dashboard-title margin-top-20">{{ trans("privateUserAnalysis.total_page_access") }}</h3>
                <!-- Download -->
                <div class="chart-download-wrapper-total_page_access">
                    <a id="downloadCSV-total-page-access"  class="btn btn-flat btn-blue pull-right" onclick="javascript:downloadCsvTotalPageAccess();">
                        <i class="fa fa-file-excel-o" aria-hidden="true"></i> {{ trans('privateCbsVoteAnalysis.download_csv') }}
                    </a>
                </div>
                <div class="clearfix"></div>
                <div id="vizTotalPageAccess" class="default-chart"></div>
                <div class="clearfix"></div>

                <br>
                <hr>
                <br>
                <div id="chartsEntities">

                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- Date Range Picker -->
    <script type="text/javascript" src="{!! asset("js/daterangepicker/moment.min.js") !!}"></script>
    <script type="text/javascript" src="{!! asset("js/daterangepicker/daterangepicker.js") !!}"></script>

    <script>
        @php
            $start = new \Carbon\Carbon();
        @endphp
        // Datetime pickers
        $('input[name="daterange"]').daterangepicker({
            startDate: '{{ $start }}',
            endDate: '{{  $start->addMinute(15) }}',
            maxDate: new Date(),
            timePicker: true,
            timePicker24Hour: true,
            timePickerIncrement: 5,
            locale: {
                format: 'YYYY-MM-DD HH:mm'
            }
        });

        // Funtions
        function downloadCsvTotalUsers(){
            var d = new Date();
            var suffix_name = d.getFullYear()+"_"+(1+d.getMonth())+"_"+d.getDate()+"_"+d.getHours()+"_"+d.getMinutes()+"_"+d.getSeconds();
            var filename = "total_users_"+suffix_name+".csv";
            downloadCSV(window.chartTotalPageAccess,filename);
        }
        function downloadCsvTotalPageAccess(){
            var d = new Date();
            var suffix_name = d.getFullYear()+"_"+(1+d.getMonth())+"_"+d.getDate()+"_"+d.getHours()+"_"+d.getMinutes()+"_"+d.getSeconds();
            var filename = "total_page_access_"+suffix_name+".csv";
            downloadCSV(window.chartTotalPageAccess, filename);
        }

        function requestCharts(){
            $("#vizTotalUsers").html("");
            $("#vizTotalPageAccess").html("");
            $("#chartsEntities").html("");
            $.ajax({
                method: 'POST', // Type of response and matches what we said in the route
                url: "{{ action("UserAnalysisController@getAnalysisStats") }}", // This is the url we gave in the route
                data:             {
                    start_date: $('input[name="daterange"]').val().split(" - ")[0],
                    end_date: $('input[name="daterange"]').val().split(" - ")[1]
                },
                beforeSend: function () {
                    var ajaxLoader = '<div class="chartLoader"><div><i class="fa fa-spinner fa-pulse fa-3x fa-fw default-color"></i><span class="sr-only">Loading...</span></div></div>';
                    $("#vizTotalUsers").html(ajaxLoader);
                    $("#vizTotalPageAccess").html(ajaxLoader);
                },
                success: function (response)
                {
                    window.tmp = response;

                    if(response.error || response == "false" ) {
                        var errorMessage = "Something went wrong!";
                        toastr.error("Error: "+errorMessage);
                        $("#vizTotalUsers").html('<div class="chartMessage"><div><i class="fa fa-eye-slash" aria-hidden="true"></i> '+errorMessage+'</div></div>');
                        $("#vizTotalPageAccess").html('<div class="chartMessage"><div><i class="fa fa-eye-slash" aria-hidden="true"></i> '+errorMessage+'</div></div>');
                    }

                    $(".chartLoader").remove();

                    // Chart for «TotalUsers» --------------------------------------------------------------------------
                    var dataTotalUsers = response["TotalUsers"];
                    var chartDataTotalUsers = [];

                    // Setting Y range - bug fix when values are 0's
                    var chartRange = [0, 10];
                    for(var i = 0 ; i< dataTotalUsers.length; i++ ){
                        chartDataTotalUsers[i] = JSON.parse(dataTotalUsers[i]);
                        if(chartDataTotalUsers[i].value != 0){
                            chartRange = false;
                        }
                    }

                    // Draw chart «TotalUsers»
                    var svgTotalUsers = d3plus.viz()
                        .container("#vizTotalUsers")  // container DIV to hold the visualization
                        .data(chartDataTotalUsers)  // data to use with the visualization
                        .type("line")       // visualization type
                        .id("name")         // key for which our data is unique on
                        .text("name")       // key to use for display text
                        .y({
                            "value": "value",
                            "range": chartRange
                        })
                        .x("timelapse")          // key to use for x-axis
                        .font({"family": "Helvetica, Arial, sans-serif", "color": "#000"})
                        .resize(true)
                        .time("timelapse")
                        .draw()             // finally, draw the visualization!

                    // Chart for «TotalPageAccess» ---------------------------------------------------------------------
                    var dataTotalPageAccess = response["TotalPageAccess"];
                    var chartTotalPageAccess = [];

                    // Setting Y range - bug fix when values are 0's
                    var chartRange = [0, 10];
                    for(var i = 0 ; i < dataTotalPageAccess.length; i++ ){
                        chartTotalPageAccess[i] = JSON.parse(dataTotalPageAccess[i]);
                        if(chartTotalPageAccess[i].value != 0){
                            chartRange = false;
                        }
                    }

                    // Error message if there are no results in Vote Analysis  :'(
                    if(chartDataTotalUsers.length == 0){
                        var warningMessage = "{!! trim(preg_replace('/\s\s+/', ' ', trans('privateUserAnalysis.no_data_Available'))) !!}";
                        $('#vizTotalUsers').html('<div class="chartMessage"><div><i class="fa fa-eye-slash" aria-hidden="true"></i> '+warningMessage+'</div></div>');
                        toastr.warning(warningMessage);
                        $(".chart-download-wrapper-total_users").remove();
                    } else {
                        window.chartTotalPageAccess = chartTotalPageAccess;
                    }


                    // Draw chart «TotalPageAccess»
                    var visualization = d3plus.viz()
                        .container("#vizTotalPageAccess")  // container DIV to hold the visualization
                        .data(chartTotalPageAccess)  // data to use with the visualization
                        .type("line")       // visualization type
                        .id("name")         // key for which our data is unique on
                        .text("name")       // key to use for display text
                        .y({
                            "value": "value",
                            "range": chartRange
                        })
                        .x("timelapse")          // key to use for x-axis
                        .font({"family": "Helvetica, Arial, sans-serif", "color": "#000"})
                        .resize(true)
                        .time("timelapse")
                        .timeline({"handles": false})
                        .draw()             // finally, draw the visualization!

                    // Error message if there are no results in Vote Analysis  :'(
                    if(chartTotalPageAccess.length == 0){
                        var warningMessage = "{!! trim(preg_replace('/\s\s+/', ' ', trans('privateUserAnalysis.no_data_Available'))) !!}";
                        $('#vizTotalPageAccess').html('<div class="chartMessage"><div><i class="fa fa-eye-slash" aria-hidden="true"></i> '+warningMessage+'</div></div>');
                        toastr.warning(warningMessage);
                        $(".chart-download-wrapper-total_page_access").remove();
                    } else {
                        // data for CSV (javascript)
                        window.chartTotalPageAccess = chartTotalPageAccess;
                    }


                    //  Entities ----------------------------------------------------------
                    var entities = response["entities"];

                    // Chart for «Total Users - Entities» ----------------------------------------------------------
                    var dataEntitiesTotalUsers = response["dataEntitiesTotalUsers"];

                    for (var key in dataEntitiesTotalUsers){
                        $("#chartsEntities").append('<h3 class="dashboard-title margin-top-20">'+entities[key]+' - Total Users</h3>');
                        $("#chartsEntities").append("<div id='dataEntitiesTotalUsers"+key+"' class='default-chart'></div><div class='clearfix'></div>");
                        var dataTotalUsers = dataEntitiesTotalUsers[key];
                        var chartDataTotalUsers = [];

                        // Setting Y range - bug fix when values are 0's
                        var chartRange = [0, 10];
                        for(var i = 0 ; i< dataTotalUsers.length; i++ ){
                            chartDataTotalUsers[i] = JSON.parse(dataTotalUsers[i]);
                            if(chartDataTotalUsers[i].value != 0){
                                chartRange = false;
                            }
                        }

                        // Draw chart «TotalUsers»
                        var svgTotalUsers = d3plus.viz()
                            .container("#dataEntitiesTotalUsers"+key)  // container DIV to hold the visualization
                            .data(chartDataTotalUsers)  // data to use with the visualization
                            .type("line")       // visualization type
                            .id("name")         // key for which our data is unique on
                            .text("name")       // key to use for display text
                            .y({
                                "value": "value",
                                "range": chartRange
                            })
                            .x("timelapse")          // key to use for x-axis
                            .font({"family": "Helvetica, Arial, sans-serif", "color": "#000"})
                            .resize(true)
                            .time("timelapse")
                            .timeline({"handles": false})
                            .draw()             // finally, draw the visualization!

                    }


                    // Chart for «Total Page Access - Entities» ----------------------------------------------------------
                    var dataEntitiesTotalPageAccess = response["dataEntitiesTotalPageAccess"];
                    for (var key in dataEntitiesTotalPageAccess){
                        $("#chartsEntities").append('<h3 class="dashboard-title margin-top-20">'+entities[key]+' - Total Page Access</h3>');
                        $("#chartsEntities").append("<div id='dataEntitiesTotalPageAccess"+key+"' class='default-chart'></div>");
                        var dataTotalPageAccess = dataEntitiesTotalPageAccess[key];
                        var chartDataTotalPageAccess = [];

                        // Setting Y range - bug fix when values are 0's
                        var chartRange = [0, 10];
                        for(var i = 0 ; i< dataTotalPageAccess.length; i++ ){
                            chartDataTotalPageAccess[i] = JSON.parse(dataTotalPageAccess[i]);
                            if(chartDataTotalPageAccess[i].value != 0){
                                chartRange = false;
                            }
                        }

                        // Draw chart «TotalUsers»
                        var svgTotalUsers = d3plus.viz()
                            .container("#dataEntitiesTotalPageAccess"+key)  // container DIV to hold the visualization
                            .data(chartDataTotalPageAccess)  // data to use with the visualization
                            .type("line")       // visualization type
                            .id("name")         // key for which our data is unique on
                            .text("name")       // key to use for display text
                            .y({
                                "value": "value",
                                "range": chartRange
                            })
                            .x("timelapse")          // key to use for x-axis
                            .font({"family": "Helvetica, Arial, sans-serif", "color": "#000"})
                            .resize(true)
                            .time("timelapse")
                            .timeline({"handles": false})
                            .draw()             // finally, draw the visualization!
                    }
                },
                error: function () { // What to do if we fail
                    $(".chartLoader").remove();
                    var errorMessage = "{!! trim(preg_replace('/\\s\\s+/', ' ', trans('privateUserAnalysis.somethingWentWrong'))) !!}";
                    toastr.error(errorMessage);
                    $("#vizTotalUsers").html('<div class="chartMessage"><div><i class="fa fa-eye-slash" aria-hidden="true"></i> '+errorMessage+'</div></div>');
                    $("#vizTotalPageAccess").html('<div class="chartMessage"><div><i class="fa fa-eye-slash" aria-hidden="true"></i> '+errorMessage+'</div></div>');
                }
            });
        }
    </script>
@endsection



