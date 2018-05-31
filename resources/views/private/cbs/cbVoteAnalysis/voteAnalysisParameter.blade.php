@extends('private._private.index')

@section('content')
    @include('private.cbs.cbVoteAnalysis.cbDetails')

    @if(!empty($voteEvents))
            <div class="row">
                <div class="col-12" style="padding-bottom: 20px">
                    <div><label>{{ trans('privateCbsVoteAnalysis.vote_event') }}</label></div>
                    <select id="voteEventSelect" name="voteEventSelect" class="voteEventSelect" style="width: 50%;">
                        <option value="">{{ trans('privateCbsVoteAnalysis.select_vote_event') }}</option>
                        @foreach($voteEvents as $key => $voteEvent)
                            <option value="{!! $key !!}" @if(Session::get("voteEventKey") == $key) selected @endif>{!! $voteEvent !!}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        @endif
        <div class="row">
            <div class="col-12 tabs-left" id="nav-analysis" hidden="hidden">

                <ul class="nav nav-tabs" role="tablist">

                    @foreach($userParameters as $key => $parameter)
                        <li role="presentation" class="nav-item"><a class="nav-link {{($loop->iteration == 1 ? 'active' : null)}}" href="#tab_{{$key}}" role="tab" id="{{$key}}" data-toggle="tab" aria-expanded="true">{{ trans('privateCbsVoteAnalysis.vote_analysis_by') }} {{$parameter}}</a></li>
                    @endforeach
                </ul>
                <!-- Tab panes -->
                <div id="tab-content" class="tab-content background-white">
                    @foreach($userParameters as $key => $parameter)
                        <div role="tabpanel" class="tab-pane {{($loop->iteration == 1 ? 'active' : null)}}" id="tab_{{$key}}">
                            @if(!empty($voteEventKey) && $loop->iteration == 1)
                                <div class="row">
                                    <div class="col-12" style="padding-bottom: 20px;padding-top: 20px">
                                        <div class="box-info">
                                            <div class="box-header">
                                                <h3 class="box-title"><i class="fa"></i> {{ trans('privateCbsVoteAnalysis.statistics_total_by') }} {{$parameterName ?? ''}}</h3>
                                            </div>
                                            <div class="box-body">
                                                <div id="statistics_by_{{$parameterKey}}" style="height: 300px">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    @if(!empty($parameterCode) && $parameterCode != 'birthday')
                                        <div class="col-12">
                                            <div class="box-info">
                                                <div class="box-header">
                                                    <h3 class="box-title"><i class="fa"></i> {{ trans('privateCbsVoteAnalysis.statistics_topics_by') }} {{$parameterName ?? ''}}</h3>
                                                </div>
                                                <div class="box-body">
                                                    <table id="proposals_list_{{$parameterKey}}" class="table table-responsive  table-striped">
                                                        <thead>
                                                        <tr>
                                                            <th rowspan="2">{{ trans('privateCbsVoteAnalysis.title') }}</th>
                                                            <th rowspan="2" style="width: 20px;">{{ trans('privateCbsVoteAnalysis.totals') }}</th>
                                                            @foreach($parametersOptions as $option)
                                                                <th colspan="3">{{$option}}</th>
                                                            @endforeach
                                                        </tr>
                                                        <tr>
                                                            @foreach($parametersOptions as $option)
                                                                <th style="width: 20px;">B</th>
                                                                <th style="width: 20px;">+</th>
                                                                <th style="width: 20px;">-</th>
                                                            @endforeach

                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        @foreach($votesByTopicParameter as $vote)
                                                            <tr>
                                                                <td>{{$vote->title}}</td>
                                                                <td>{{ $vote->total ?? 0 }}</td>
                                                                @foreach( $parametersOptions as $option)
                                                                    <td>{{ $vote->parameter_options->{$option}->balance ?? 0 }}</td>
                                                                    <td>{{ $vote->parameter_options->{$option}->positives ?? 0 }}</td>
                                                                    <td>{{ $vote->parameter_options->{$option}->negatives ?? 0 }}</td>
                                                                @endforeach
                                                            </tr>
                                                        @endforeach
                                                        </tbody>

                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>


                                <div class="row">
                                    {{--CountByParameterOption--}}
                                    <div class="col-md-4">
                                        <div class="box box-success">
                                            <div class="box-header">
                                                <h3 class="box-title"><i class="fa"></i>{{trans('privateCbsVoteAnalysis.count_total_votes_by')}} {{$parameterName ?? ''}}</h3>
                                            </div>
                                            <div class="box-body">
                                                <div class="col-md-11">
                                                    <canvas id="countByParameter_{{$parameterKey}}" style="height:230px"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{--FirstByParameterOption--}}
                                    <div class="col-md-4">
                                        <div class="box box-success">
                                            <div class="box-header">
                                                <h3 class="box-title"><i class="fa"></i>{{trans('privateCbsVoteAnalysis.count_first_by')}} {{$parameterName ?? ''}}</h3>
                                            </div>
                                            <div class="box-body">
                                                <div class="col-md-11">
                                                    <canvas id="firstByParameter_{{$parameterKey}}" style="height:230px"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{--SecondByParameterOption--}}
                                    <div class="col-md-4">
                                        <div class="box box-success">
                                            <div class="box-header">
                                                <h3 class="box-title"><i class="fa"></i>{{trans('privateCbsVoteAnalysis.count_second_by')}} {{$parameterName ?? ''}}</h3>
                                            </div>
                                            <div class="box-body">
                                                <div class="col-md-11">
                                                    <canvas id="secondByParameter_{{$parameterKey}}" style="height:230px"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
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
        getVoteAnalysis(id,voteEventKey);
    });

    @if(empty($voteEvents))
         $('#nav-analysis').removeAttr('hidden');
    @endif


    $( "#voteEventSelect" ).change(function() {
        renderSelectedOptions();
    });

    function getVoteAnalysis(id,voteEventKey){
        $.ajax({
            method: 'POST', // Type of response and matches what we said in the route
            url: "{{action('CbsController@getVoteAnalysis')}}", // This is the url we gave in the route
            data: {
                statistics_type: 'vote_analysis_by_parameter',
                vote_event_key: voteEventKey,
                parameter_key: id
            }, beforeSend: function () {
                var ajaxLoader = '<div class="chartLoader"><div><i class="fa fa-circle-o-notch fa-spin fa-3x fa-fw default-color"></i><span class="sr-only">Loading...</span></div></div>';
                $('#tab_'+id).html(ajaxLoader);
            },
            success: function (response) { // What to do if we succeed
                if (response != 'false') {
                    $('#tab_'+id).empty();
                    $('#tab_'+id).append(response);
                } else {
                    var errorMessage = "{!! trim(preg_replace('/\s\s+/', ' ', trans('privateCbsVoteAnalysis.no_data_available'))) !!}";
                    toastr.warning(errorMessage);
                    if ($('#tab_' + id).length != 0) {
                        $('#tab_' + id).html('<div class="chartMessage"><div><i class="fa fa-eye-slash" aria-hidden="true"></i> ' + errorMessage + '</div></div>');
                    }else{
                        $('#tab-content').html('<div class="chartMessage"><div><i class="fa fa-eye-slash" aria-hidden="true"></i> '+errorMessage+'</div></div>');
                    }
                }
            },
            error: function () { // What to do if we fail
                var errorMessage = "{!! trim(preg_replace('/\s\s+/', ' ', trans('privateCbsVoteAnalysis.something_went_wrong'))) !!}";
                toastr.error(errorMessage);
                $('#tab_'+id).html('<div class="chartMessage"><div><i class="fa fa-eye-slash" aria-hidden="true"></i> '+errorMessage+'</div></div>');
            }
        });
    }

    function renderSelectedOptions(){
        var keySelected = $("#voteEventSelect").val();
        if(keySelected.length == 0){
            $('#nav-analysis').attr('hidden','hidden');
            $('#userParameterSelectDiv').hide();
        }else{
            var navActive = $("#nav-analysis").find(".active").attr('id');
            getVoteAnalysis(navActive,keySelected);
            $('#nav-analysis').removeAttr('hidden');
        }
    }
</script>

@if(!empty($voteEventKey))
    <script>
        var statistics_by_parameter = [
            @foreach($votesByParameter->total as $option => $vote)
                { "{!! trans('privateCbsVoteAnalysis.option_name') !!}":"{{ $option }}","{!! trans('privateCbsVoteAnalysis.total_votes') !!}": {{ $vote }}},
            @endforeach
        ];

        var visualization = d3plus.viz()
            .container("#statistics_by_{{$parameterKey}}")  // container DIV to hold the visualization
            .data(statistics_by_parameter)  // data to use with the visualization
            .type("bar")// visualization type
            .id("{!! trans('privateCbsVoteAnalysis.option_name') !!}")
            .y("{!! trans('privateCbsVoteAnalysis.option_name') !!}")         // key to use for y-axis
            .y({"scale": "discrete"}) // Manually set Y-axis to be discrete
            .x( "{!! trans('privateCbsVoteAnalysis.total_votes') !!}")// key to use for x-axis
            .legend({
                "value": true,
                "size": 35
            })
            .font({"family": "Helvetica, Arial, sans-serif", "color": "#000"})
            .resize(true)
            .draw();


    </script>


    <script>

        $(document).ready(function() {
            var table = $('#proposals_list_{{$parameterKey}}').DataTable({
                "paging":   false,
                "info":     false,
                "bFilter" :  false
            } );


            $('#proposals_list_{{$parameterKey}} tbody')
                .on( 'mouseenter', 'td', function () {
                    var colIdx = table.cell(this).index().column;
                    $( table.cells().nodes() ).removeClass( 'highlight');
                    $( table.column( colIdx ).nodes() ).addClass( 'highlight' );
                } );
        } );

        $(function () {


            var barChartOptions = {
                //Boolean - Whether the scale should start at zero, or an order of magnitude down from the lowest value
                scaleBeginAtZero: true,
                //Boolean - Whether grid lines are shown across the chart
                scaleShowGridLines: true,
                //String - Colour of the grid lines
                scaleGridLineColor: "rgba(0,0,0,.2)",
                //Number - Width of the grid lines
                scaleGridLineWidth: 1,
                //Boolean - Whether to show horizontal lines (except X axis)
                scaleShowHorizontalLines: true,
                //Boolean - Whether to show vertical lines (except Y axis)
                scaleShowVerticalLines: true,
                //Boolean - If there is a stroke on each bar
                barShowStroke: true,
                //Number - Pixel width of the bar stroke
                barStrokeWidth: 2,
                //Number - Spacing between each of the X value sets
                barValueSpacing: 5,
                //Number - Spacing between data sets within X values
                barDatasetSpacing: 1,
                //Boolean - whether to make the chart responsive
                responsive: true,
                maintainAspectRatio: false
            };
            barChartOptions.datasetFill = false;

            //--------------
            //- CountByParameter -
            //--------------
            // Get context with jQuery - using jQuery's .get() method.
            var countByparameter = $("#countByParameter_{{$parameterKey}}").get(0).getContext("2d");
            // This will get the first returned node in the jQuery collection.
            var countByparameterBarChart = new Chart(countByparameter);

            var countByparameter = {
                labels: [
                    @foreach($parametersOptions as $option)
                        "{{$option}}",
                    @endforeach
                ],
                datasets: [
                    {
                        label: "Positives",
                        fillColor: "rgba(0, 255, 0, 1)",
                        strokeColor: "rgba(0, 255, 0, 1)",
                        pointColor: "rgba(0, 255, 0, 1)",
                        pointStrokeColor: "#c1c7d1",
                        pointHighlightFill: "#fff",
                        pointHighlightStroke: "rgba(220,220,220,1)",
                        data:[
                            @foreach($parametersOptions as $option)
                            {{ $countByParameter->{$option}->positive ?? 0 }},
                            @endforeach
                        ]
                    },
                    {
                        label: "Negatives",
                        fillColor: "rgba(255,0,0,0.9)",
                        strokeColor: "rgba(255,0,0,0.8)",
                        pointColor: "#3b8bba",
                        pointStrokeColor: "rgba(255,0,0,1)",
                        pointHighlightFill: "#fff",
                        pointHighlightStroke: "rgba(60,141,188,1)",
                        data: [
                            @foreach($parametersOptions as $option)
                            {{ $countByParameter->{$option}->negative ?? 0 }},
                            @endforeach
                        ]
                    }
                ]
            };

            countByparameterBarChart.Bar(countByparameter, barChartOptions);


            //--------------
            //- FirstByGender -
            //--------------
            // Get context with jQuery - using jQuery's .get() method.
            var firstByParameter = $("#firstByParameter_{{$parameterKey}}").get(0).getContext("2d");
            // This will get the first returned node in the jQuery collection.
            var firstByParameterBarChart = new Chart(firstByParameter);

            var firstByParameter = {
                labels: [
                    @foreach($parametersOptions as $option)
                        "{{$option}}",
                    @endforeach
                ],
                datasets: [
                    {
                        label: "Positives",
                        fillColor: "rgba(0, 255, 0, 1)",
                        strokeColor: "rgba(0, 255, 0, 1)",
                        pointColor: "rgba(0, 255, 0, 1)",
                        pointStrokeColor: "#c1c7d1",
                        pointHighlightFill: "#fff",
                        pointHighlightStroke: "rgba(220,220,220,1)",
                        data:[
                            @foreach($parametersOptions as $option)
                            {{ $firstByParameter->{$option}->positive ?? 0 }},
                            @endforeach
                        ]
                    },
                    {
                        label: "Negatives",
                        fillColor: "rgba(255,0,0,0.9)",
                        strokeColor: "rgba(255,0,0,0.8)",
                        pointColor: "#3b8bba",
                        pointStrokeColor: "rgba(255,0,0,1)",
                        pointHighlightFill: "#fff",
                        pointHighlightStroke: "rgba(60,141,188,1)",
                        data: [
                            @foreach($parametersOptions as $option)
                            {{ $firstByParameter->{$option}->negative ?? 0 }},
                            @endforeach
                        ]
                    }
                ]
            };
            firstByParameterBarChart.Bar(firstByParameter, barChartOptions);

            //--------------
            //- SecondByGNb -
            //--------------
            // Get context with jQuery - using jQuery's .get() method.
            var secondByParameter = $("#secondByParameter_{{$parameterKey}}").get(0).getContext("2d");
            // This will get the first returned node in the jQuery collection.
            var secondByParameterBarChart = new Chart(secondByParameter);

            var secondByParameter = {
                labels: [
                    @foreach($parametersOptions as $option)
                        "{{$option}}",
                    @endforeach
                ],
                datasets: [
                    {
                        label: "Positives",
                        fillColor: "rgba(0, 255, 0, 1)",
                        strokeColor: "rgba(0, 255, 0, 1)",
                        pointColor: "rgba(0, 255, 0, 1)",
                        pointStrokeColor: "#c1c7d1",
                        pointHighlightFill: "#fff",
                        pointHighlightStroke: "rgba(220,220,220,1)",
                        data:[
                            @foreach($parametersOptions as $option)
                            {{ $secondByParameter->{$option}->positive ?? 0 }},
                            @endforeach
                        ]
                    },
                    {
                        label: "Negatives",
                        fillColor: "rgba(255,0,0,0.9)",
                        strokeColor: "rgba(255,0,0,0.8)",
                        pointColor: "#3b8bba",
                        pointStrokeColor: "rgba(255,0,0,1)",
                        pointHighlightFill: "#fff",
                        pointHighlightStroke: "rgba(60,141,188,1)",
                        data: [
                            @foreach($parametersOptions as $option)
                            {{ $secondByParameter->{$option}->negative ?? 0 }},
                            @endforeach
                        ]
                    }
                ]
            };
            secondByParameterBarChart.Bar(secondByParameter, barChartOptions);
        });
    </script>
@endif


@if(!empty(Session::get("voteEventKey")) && !empty($voteEvents) && array_key_exists(Session::get("voteEventKey"),$voteEvents))
    <script>
        $( document ).ready(function() {
            renderSelectedOptions();
        });
    </script>
@endif

@endsection