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
    @if(!empty($parameterCode) && $parameterCode == 'birthday')
        <div class="col-12" style="padding-bottom: 20px;padding-top: 20px">
            <div class="box-info">
                <div class="box-header">
                    <h3 class="box-title"><i class="fa"></i> {{ trans('privateCbsVoteAnalysis.statistics_total_voters_by') }} {{$parameterName ?? ''}}</h3>
                </div>
                <div class="box-body">

                    <div id="statistics_total_pie_by_{{$parameterKey}}" style="height: 300px"></div>

                </div>
            </div>
        </div>
    @endif

    <div class="col-12">
        <div class="box-info">
            <div class="box-header">
                <h3 class="box-title"><i class="fa"></i> {{ trans('privateCbsVoteAnalysis.statistics_topics_by') }} {{trans('privateCbsVoteAnalysis.and') }} {{$parameterName ?? ''}}</h3>
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
                                <td>{{ $vote->parameter_options->{$option}->positive ?? 0 }}</td>
                                <td>{{ $vote->parameter_options->{$option}->negative ?? 0 }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                    </tbody>

                </table>
            </div>
        </div>
    </div>


    @if(!empty($parameterCode) && $parameterCode == 'birthday' && !empty($secondParametersOptions) && !empty($thirdParametersOptions) && !empty($statisticsByAgeTwoParams))
        <div class="col-12">
            <div class="box-info">
                <div class="box-header">
                    <h3 class="box-title"><i class="fa"></i> {{ trans('privateCbsVoteAnalysis.statistics_topics_by') }} {{$ageInterval ?? '+90'}} {{ trans('privateCbsVoteAnalysis.years') }}, {{$secondParameterName ?? ''}} {{ trans('privateCbsVoteAnalysis.and') }} {{$thirdParameterName ?? ''}}</h3>
                </div>
                <div class="box-body">
                    <table id="proposals_list_{{$parameterKey}}" class="table table-responsive  table-striped">
                        <thead>
                        <tr>
                            <th rowspan="3">{{ trans('privateCbsVoteAnalysis.title') }}</th>
                            <th rowspan="3" style="width: 20px;">{{ trans('privateCbsVoteAnalysis.totals') }}</th>
                            @foreach($secondParametersOptions as $option)
                                <th colspan="{{3*count($thirdParametersOptions)}}">{{$option}}</th>
                            @endforeach
                        </tr>
                        <tr>
                            @foreach($secondParametersOptions as $option)
                                @foreach($thirdParametersOptions as $option)
                                    <th colspan="3">{{$option}}</th>
                                @endforeach
                            @endforeach

                        </tr>
                        <tr>
                            @foreach($secondParametersOptions as $option)
                                @foreach($thirdParametersOptions as $option)
                                    <th style="width: 20px;">B</th>
                                    <th style="width: 20px;">+</th>
                                    <th style="width: 20px;">-</th>
                                @endforeach
                            @endforeach

                        </tr>
                        </thead>
                        <tbody>
                        @foreach($statisticsByAgeTwoParams as $vote)
                            <tr>
                                <td>{{$vote->title}}</td>
                                <td>{{ $vote->total ?? 0 }}</td>
                                @foreach($secondParametersOptions as $secondOption)
                                    @foreach($thirdParametersOptions as $thirdOption)
                                        <td>{{ $vote->parameter_options->{$secondOption}->{$thirdOption}->balance ?? 0 }}</td>
                                        <td>{{ $vote->parameter_options->{$secondOption}->{$thirdOption}->positive ?? 0 }}</td>
                                        <td>{{ $vote->parameter_options->{$secondOption}->{$thirdOption}->negative ?? 0 }}</td>
                                    @endforeach
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

@if(!empty($parameterCode) && $parameterCode != 'birthday')
    <div class="row">
        <div class="col-md-12">
            <div class="box-info">
                <div class="box-header">
                    <h3 class="box-title">{{ trans('privateCbsVoteAnalysis.statistics_topics_by') }} {{$parameterName ?? ''}}</h3>
                </div>
                <div class="box-body">
                    <div id="statistics_by_topic_parameter_{{$parameterKey}}" style="min-height: 600px;width: 100%;"></div>
                </div>
            </div>
        </div>
    </div>
@endif

<div class="row">
    {{--CountByParameterOption--}}
    <div class="col-md-4">
        <div class="box-info">
            <div class="box-header">
                <h3 class="box-title"><i class="fa"></i>{{trans('privateCbsVoteAnalysis.count_total_votes_by')}} {{$parameterName ?? ''}}</h3>
            </div>
            <div class="box-body">
                <div class="col-md-11">
                    <canvas id="countByParameter_{{$parameterKey}}" style="height:600px"></canvas>
                </div>
            </div>
        </div>
    </div>
    {{--FirstByParameterOption--}}
    <div class="col-md-4">
        <div class="box-info">
            <div class="box-header">
                <h3 class="box-title"><i class="fa"></i>{{trans('privateCbsVoteAnalysis.count_first_by')}} {{$parameterName ?? ''}}</h3>
            </div>
            <div class="box-body">
                <div class="col-md-11">
                    <canvas id="firstByParameter_{{$parameterKey}}" style="height:600px"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{--SecondByParameterOption--}}
    <div class="col-md-4">
        <div class="box-info">
            <div class="box-header">
                <h3 class="box-title"><i class="fa"></i>{{trans('privateCbsVoteAnalysis.count_second_by')}} {{$parameterName ?? ''}}</h3>
            </div>
            <div class="box-body">
                <div class="col-md-11">
                    <canvas id="secondByParameter_{{$parameterKey}}" style="height:600px"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

@if(!empty($parameterCode) && $parameterCode != 'birthday')
    <div class="box-info text-center">
        <div class="box-header">
            <h3 class="box-title"><i class="fa"></i> {{trans('privateCbsVoteAnalysis.statistics_topics_by') }} {{trans('privateCbsVoteAnalysis.and') }} {{$parameterName ?? ''}}</h3>
        </div>
        <div class="box-body">
            <div class="col-md-12" style="height: 600px">
                <div id="proposals_votes_{{$parameterKey}}"></div>
            </div>

        </div>
    </div>
@endif

@if(!empty($secondParameterName) && !empty($parameterCode) && $parameterCode != 'birthday')
    <div class="box-info text-center">
        <div class="box-header">
            <h3 class="box-title"><i class="fa"></i> {{trans('privateCbsVoteAnalysis.vote_analysis_by') }} {{$parameterName ?? ''}} {{trans('privateCbsVoteAnalysis.and') }} {{$secondParameterName ?? ''}}</h3>
        </div>
        <div class="box-body">
            <div class="col-md-12" style="height: 300px">
                <div id="population_by_two_parameters_{{$parameterKey}}"></div>
            </div>

        </div>
    </div>
@endif




@if(!empty($parameterCode) && $parameterCode == 'neighborhood' && !empty($commutersStatistics))
    <div class="box-info text-center">
        <div class="box-header">
            <h3 class="box-title"><i class="fa"></i> {{trans('privateCbsVoteAnalysis.commuters_statistics_by') }} {{$parameterName ?? ''}}</h3>
        </div>
        <div class="box-body">
            <div class="col-md-12" style="height: 400px">
                <div id="statistics_by_commuters_{{$parameterKey}}"></div>
            </div>
        </div>
    </div>
@endif



{{-- ******************  Section Scripts ****************** --}}

@if(!empty($parameterCode) && $parameterCode == 'neighborhood' && !empty($commutersStatistics))
    <script>
        var sample_data = [
            @foreach($commutersStatistics as $neighborhood => $values)
                {"name": "{!! trans('privateCbsVoteAnalysis.positive_votes') !!}", "neighborhood": "{{$neighborhood}}", "{!! trans('privateCbsVoteAnalysis.votes') !!}": {{$values->positive ?? 0}},"hex": "#00CC00"},
                {"name": "{!! trans('privateCbsVoteAnalysis.negative_votes') !!}", "neighborhood": "{{$neighborhood}}", "{!! trans('privateCbsVoteAnalysis.votes') !!}": {{$values->negative ?? 0}},"hex": "#CC0000"},
            @endforeach
        ];


        var visualization = d3plus.viz()
            .container("#statistics_by_commuters_{{$parameterKey}}")
            .data(sample_data)
            .id(["name", "neighborhood"])
            .size("{!! trans('privateCbsVoteAnalysis.votes') !!}")
            .type("radar")
            .color("hex")
            .font({"family": "Helvetica, Arial, sans-serif", "color": "#000"}).resize(true)
            .draw();
    </script>
@endif




@if(!empty($secondParameterName))
    <script>
        var data = [
            @foreach($votePopulationTwoParameters as $key => $population)
                @foreach($population as $pop => $value)
                    {"{!! $secondParameterName !!}": "{!! $key !!}", "name":"{!!$pop!!}", "{!! trans('privateCbsVoteAnalysis.total_voters') !!}": {{$value ?? 0}}},
                @endforeach
            @endforeach
        ];

        var visualization = d3plus.viz()
            .container("#population_by_two_parameters_{{$parameterKey}}")
            .data(data)
            .type("bar")
            .id("name")
            .x("{!! $secondParameterName !!}")
            .y("{!! trans('privateCbsVoteAnalysis.total_voters') !!}")
            .font({"family": "Helvetica, Arial, sans-serif", "color": "#000"}).resize(true)
            .draw()
    </script>
@endif
@if(!empty($parameterCode) && $parameterCode != 'birthday')

    <script>
        @php $j = 0; @endphp
        var statistics_by_topic_data = [
            @foreach($votesByTopicParameter as $voteTopic)
                @foreach( $parametersOptions as $option)
                    {"position": "{{ $loop->parent->iteration }}", "{!! trans('privateCbsVoteAnalysis.topic_name') !!}":"{{ $voteTopic->title }}", "type":"{!! $option !!} - {!! trans('privateCbsVoteAnalysis.positive_votes') !!}","{!! trans('privateCbsVoteAnalysis.total_votes') !!}": {{ $voteTopic->parameter_options->{$option}->positive ?? 0 }}},
                    {"position": "{{ $loop->parent->iteration }}", "{!! trans('privateCbsVoteAnalysis.topic_name') !!}":"{{ $voteTopic->title }}", "type":"{!! $option !!} - {!! trans('privateCbsVoteAnalysis.negative_votes') !!}","{!! trans('privateCbsVoteAnalysis.total_votes') !!}": {{ (isset($voteTopic->parameter_options->{$option}->negative) ? $voteTopic->parameter_options->{$option}->negative * -1  : 0) }}},
                    @php $j++; @endphp
                @endforeach
            @endforeach
        ];

        // Update chart height
        $("#statistics_by_topic_parameter_{{$parameterKey}}").css("height","{{ ($j == 0) ? 100 : $j*8 }}px");

        var visualization = d3plus.viz()
            .container("#statistics_by_topic_parameter_{{$parameterKey}}")  // container DIV to hold the visualization
            .data(statistics_by_topic_data)  // data to use with the visualization
            .type("bar")// visualization type
            .id("type")
            .y("{!! trans('privateCbsVoteAnalysis.topic_name') !!}")         // key to use for y-axis
            .y({"scale": "discrete"}) // Manually set Y-axis to be discrete
            .x({"stacked": true}) // Manually set Y-axis to be discrete
            .x( "{!! trans('privateCbsVoteAnalysis.total_votes') !!}")// key to use for x-axis
            .order("position")
            .font({"family": "Helvetica, Arial, sans-serif", "color": "#000"})
            .draw();
    </script>
@endif

@if(!empty($parameterCode) && $parameterCode == 'birthday')

    <script>
        var data = [
                @foreach($votePopulation as $population => $value)
            {"value": {{$value}}, "name": "{{$population}}"},
            @endforeach
        ]
        d3plus.viz()
            .container("#statistics_total_pie_by_{{$parameterKey}}")
            .data(data)
            .type("pie")
            .id("name")
            .size("value")
            .font({"family": "Helvetica, Arial, sans-serif", "color": "#000"}).resize(true)
            .draw()
    </script>

@endif


@if(!empty($parameterCode) && $parameterCode != 'birthday')
    <script>
        // sample data array
        var data_Proposals_Uptown = [
                @foreach( $votesByTopicParameter as $topic)
                @foreach($topic->parameter_options as $key => $option)
            {"value": {{$option->balance}}, "name": "{{$topic->title}}", "group": "{{$key}}"},
            @endforeach
            @endforeach
        ];
        var visualization = d3plus.viz()
            .container("#proposals_votes_{{$parameterKey}}")     // container DIV to hold the visualization
            .data(data_Proposals_Uptown)     // data to use with the visualization
            .type("bubbles")       // visualization type
            .id(["group", "name"]) // nesting keys
            .depth(1)              // 0-based depth
            .size("value")         // key name to size bubbles
            .color("group")        // color by each group
            .font({"family": "Helvetica, Arial, sans-serif", "color": "#000"}).resize(true)
            .draw();
    </script>

@endif


<script>
    @php $k = 0; @endphp
    var statistics_by_parameter = [
        @foreach($votesByParameter->total as $option => $vote)
            { "{!! trans('privateCbsVoteAnalysis.option_name') !!}":"{{ $option }}","{!! trans('privateCbsVoteAnalysis.total_votes') !!}": {{ $vote }}},
            @php $k++; @endphp
        @endforeach

    ];

    $("#statistics_by_{{$parameterKey}}").css("height", "{{ ($k <= 15) ? "400" : $k*20 }}px");

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
        .font({"family": "Helvetica, Arial, sans-serif", "color": "#000"}).resize(true)
        .draw();
</script>


<script>

    $(document).ready(function() {
        var table = $('#proposals_list_{{$parameterKey}}').DataTable({
            "paging":   false,
            "info":     false,
            "bFilter" :  false,
            "order" : [1,'desc']
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