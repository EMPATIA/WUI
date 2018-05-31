@extends('private._private.index')
@section('header_styles')

@endsection
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


    <div class="text-right margin-top-20 margin-bottom-10">
        <div class="colors btn-group" data-toggle="buttons" style="pointer-events: none;cursor: default;opacity:0.8;">
            <label class="btn btn-primary">
                <input type="radio" name="view_submitted" value="1" autocomplete="off" disabled > {{ trans('privateUserAnalysis.view_submitted') }}
            </label>
            <label id="default-view-all" class="btn btn-primary btn-selected">
                <input type="radio" name="view_submitted" value="0" autocomplete="off" checked> {{ trans('privateUserAnalysis.all') }}
            </label>
        </div>
    </div>

    <div class="row">
        <div class="col-12 tabs-left" id="nav-analysis" hidden="hidden">
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="nav-item"><a class="nav-link active" href="#tab_vote_analysis_total" role="tab" id="vote_analysis_total" data-toggle="tab" aria-expanded="true">{{ trans('privateCbsVoteAnalysis.vote_analysis_total') }}</a></li>
                <li role="presentation" class="nav-item"><a class="nav-link" href="#tab_vote_analysis_by_channel" role="tab" id="vote_analysis_by_channel" data-toggle="tab" aria-expanded="true">{{ trans('privateCbsVoteAnalysis.vote_analysis_by_channel') }}</a></li>
                <li role="presentation" class="nav-item"><a class="nav-link" href="#tab_voter_analysis_by_channel" role="tab" id="voter_analysis_by_channel" data-toggle="tab" aria-expanded="true">{{ trans('privateCbsVoteAnalysis.voter_analysis_by_channel') }}</a></li>
            </ul>
            <!-- Tab panes -->
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="tab_vote_analysis_total">
                    @if(!empty($voteEventKey))
                        @if(empty($statisticsTotalData) && empty($statisticsTotalSummary))
                            <div class="row">
                                <div class="col-12 text-center">
                                    <h4>{{trans('privateCbsVoteAnalysis.no_data_available')}}</h4>
                                </div>
                            </div>
                        @else
                            <div class="row">
                                {{--TOTAL VOTES INFORMATION--}}
                                <div class="col-md-12">
                                    <div class="box-info">
                                        <div class="box-header voteAnalysis-total">
                                            <div class="row">
                                                <div class="col-12 col-xl-12">
                                                    <h3 class="box-title"><i class="fa"></i> {{trans('privateCbsVoteAnalysis.count_total_votes')}}</h3>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="box-body">
                                            <div class="row">
                                                <div class="col-sm-3 text-center">
                                                    <div>
                                                        <img src="{{asset('/images/total_voters.png')}}" style="width: 5em">
                                                    </div>
                                                    <div>
                                                        <strong>{{trans('privateCbsVoteAnalysis.total_voters')}}</strong>
                                                    </div>
                                                    <div>
                                                        {{ $statisticsTotalSummary->total_users_voted ?? null }}
                                                    </div>
                                                </div>
                                                <div class="col-sm-3 text-center">
                                                    <div>
                                                        <img src="{{asset('/images/total_votes.png')}}" style="width: 5em">
                                                    </div>
                                                    <div>
                                                        <strong>{{trans('privateCbsVoteAnalysis.total_votes')}}</strong>
                                                    </div>
                                                    <div>
                                                        {{$statisticsTotalSummary->total ?? null}}
                                                    </div>
                                                </div>
                                                <div class="col-sm-3 text-center">
                                                    <div>
                                                        <img src="{{asset('/images/positive_votes.png')}}" style="width: 5em">
                                                    </div>
                                                    <div>
                                                        <strong>{{trans('privateCbsVoteAnalysis.total_positive_votes')}}</strong>
                                                    </div>
                                                    <div>
                                                        {{$statisticsTotalSummary->total_positives ?? null}}
                                                    </div>
                                                </div>
                                                <div class="col-sm-3 text-center">
                                                    <div>
                                                        <img src="{{asset('/images/negative_votes.png')}}" style="width: 5em">
                                                    </div>
                                                    <div>
                                                        <strong>{{trans('privateCbsVoteAnalysis.total_negative_votes')}}</strong>
                                                    </div>
                                                    <div>
                                                        {{$statisticsTotalSummary->total_negatives ?? null}}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{--END TOTAL VOTES INFORMATION--}}
                            </div>
                            <div class="row">
                                {{--TOP 10--}}
                                <div class="col-md-12">
                                    <div class="box-info">
                                        <div class="box-header">
                                            <h3 class="box-title">{{trans('privateCbsVoteAnalysis.top_topics')}}</h3>
                                        </div>
                                        <div class="box-body">
                                            <table class="table table-responsive  table-striped">
                                                <tbody>
                                                <tr>
                                                    <th style="width: 10px">#</th>
                                                    <th>{{trans('privateCbsVoteAnalysis.topic_name')}}</th>
                                                    <th class="text-center" style="width: 50px;">{{trans('privateCbsVoteAnalysis.total_budget')}}</th>
                                                    <th class="text-center" style="width: 50px;">{{trans('privateCbsVoteAnalysis.total_balance')}}</th>
                                                    <th class="text-center" style="width: 50px;">{{trans('privateCbsVoteAnalysis.total_positives')}}</th>
                                                    <th class="text-center" style="width: 50px;">{{trans('privateCbsVoteAnalysis.total_negatives')}}</th>
                                                </tr>
                                                @php
                                                    $j = 1;
                                                @endphp
                                                @if(!empty($statisticsTotalData))
                                                    @foreach($statisticsTotalData as $topTopic)
                                                        <tr style="{{$topTopic->winner ? "background-color: #c2dcf1;": ""}}">
                                                            <td>{{ $j++  }} </td>
                                                            <td>{{$topTopic->title}}</td>
                                                            <td>{{$topTopic->budget}}</td>
                                                            <td class=" text-center">
                                                                @if($topTopic->balance >= 0 )
                                                                    <span> {{$topTopic->balance}}</span>
                                                                @else
                                                                    <span> {{$topTopic->balance}}</span>
                                                                @endif
                                                            </td>
                                                            <td class=" text-center">{{$topTopic->positives}}</td>
                                                            <td class=" text-center">{{$topTopic->negatives}}</td>

                                                        </tr>
                                                    @endforeach
                                                @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                {{--TOP 10 END--}}
                            </div>


                            <div class="box-info">
                                {{--
                                <div class="box-header">
                                    <h3 class="box-title">{{trans('privateCbsVoteAnalysis.balance_votes_by_topic')}}</h3>
                                </div>
                                --}}
                                <div class="box-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <!-- Download CSV -->
                                            <div id="chart_by_topic_download_wrapper_1" class="chart-download-wrapper">
                                                <a id="statisticsByTopicDownloadCSV"  class="btn btn-flat btn-blue pull-right">
                                                    <i class="fa fa-file-excel-o" aria-hidden="true"></i> {{ trans('privateCbsVoteAnalysis.download_csv') }}
                                                </a>
                                            </div>
                                            <!-- Chart -->
                                            <div id="statistics_by_topic" style="min-height: 300px;" class="default-padding"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                    @endif
                </div>
                <div role="tabpanel" class="tab-pane" id="tab_vote_analysis_by_channel">

                </div>
                <div role="tabpanel" class="tab-pane" id="tab_voter_analysis_by_channel">

                </div>
            </div>
        </div>
    </div>

    @php
        $voteEvent = Session::get("voteEvent");
        // dd($voteEvent);
    @endphp

@endsection

@section('scripts')

    <script>
        $(function() {
            var array = ["{{ $type }}", "{{$cbKey}}"];
            getSidebar('{{ action("OneController@getSidebar") }}', 'total_votes', array, 'voteAnalysis' );
        });

        $("#voteEventSelect").select2();
        $("#userParameterSelect").select2();

        $('a[data-toggle="tab"]').on('shown.bs.tab', function () {
            $("#tab_vote_analysis_total").hide();
            $("#tab_vote_analysis_by_channel").hide();
            $("#tab_voters_analysis_by_channel").hide();

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
            var keySelected = $(this).val();
            if(keySelected.length == 0){
                $('#nav-analysis').attr('hidden','hidden');
                $('#userParameterSelectDiv').hide();
            }else{
                var navActive = $("#nav-analysis").find("li > a.active").attr('id');
                getVoteAnalysis(navActive,keySelected);
                $('#nav-analysis').removeAttr('hidden');
            }
        });


        $( "#userParameterSelect" ).change(function() {
            renderSelectedOptions();
        });



        function getVoteAnalysis(id,voteEventKey){
            var parameterKey = $( "#userParameterSelect" ).val();
            $.ajax({
                method: 'POST', // Type of response and matches what we said in the route
                url: "{{action('CbsController@getVoteAnalysis')}}", // This is the url we gave in the route
                data: {
                    statistics_type: id,
                    vote_event_key: voteEventKey,
                    parameter_key: parameterKey,
                    cb_key: '{{$cbKey}}'
                }, beforeSend: function () {
                    var ajaxLoader = '<div class="chartLoader"><div><i class="fa fa-circle-o-notch fa-spin fa-3x fa-fw default-color"></i><span class="sr-only">Loading...</span></div></div>';
                    $('#tab_'+id).html(ajaxLoader);
                },
                success: function (response) { // What to do if we succeed
                    if (response != 'false') {
                        $('#tab_'+id).empty();
                        $('#tab_'+id).append(response);
                        $('#tab_'+id).show();
                        $('.chartLoader').remove();
                    } else {
                        $(".chartLoader").remove();
                        var errorMessage = "{!! trim(preg_replace('/\s\s+/', ' ', trans('privateCbsVoteAnalysis.something_went_wrong'))) !!}";
                        toastr.error("Error: "+errorMessage);
                        $('#tab_'+id).html('<div class="chartMessage"><div><i class="fa fa-eye-slash" aria-hidden="true"></i> '+errorMessage+'</div></div>');
                    }
                },
                error: function () { // What to do if we fail
                    $(".chartLoader").remove();
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
                var navActive = $("#nav-analysis").find("li > a.active").attr('id');
                getVoteAnalysis(navActive,keySelected);
                $('#nav-analysis').removeAttr('hidden');
            }
        }
    </script>


    @if(!empty($statisticsTotalData))
        <script>
            $( document ).ready(function() {
                        @php $k = 0; @endphp
                var statistics_by_topic_data = [
                                @foreach($statisticsTotalData as $topTopic)
                        {
                            "position": "{{ $loop->iteration }}",
                            "{!! trans('privateCbsVoteAnalysis.topic_name') !!}": "{{ $topTopic->title }}",
                            "type": "{!! trans('privateCbsVoteAnalysis.positives_votes') !!}",
                            "{!! trans('privateCbsVoteAnalysis.total_votes') !!}": {{ $topTopic->positives ?? 0 }},
                            "total_votes": {{ $topTopic->positives ?? 0 }}},
                        {
                            "position": "{{ $loop->iteration }}",
                            "{!! trans('privateCbsVoteAnalysis.topic_name') !!}": "{{ $topTopic->title }}",
                            "type": "{!! trans('privateCbsVoteAnalysis.negatives_votes') !!}",
                            "{!! trans('privateCbsVoteAnalysis.total_votes') !!}": {{ $topTopic->negatives * -1 ?? 0 }},
                            "total_votes": {{ $topTopic->negatives * -1 ?? 0 }}},

                            @php $k++; @endphp
                            @endforeach
                    ];

                $("#statistics_by_topic").css("height", "{{ ($k <= 15) ? "400" : $k*20 }}px");

                var visualization = d3plus.viz()
                    .container("#statistics_by_topic")  // container DIV to hold the visualization
                    .data(statistics_by_topic_data)  // data to use with the visualization
                    .type("bar")// visualization type
                    .id("type")
                    .y("{!! trans('privateCbsVoteAnalysis.topic_name') !!}")         // key to use for y-axis
                    .y({"scale": "discrete"}) // Manually set Y-axis to be discrete
                    .x({"stacked": true}) // Manually set Y-axis to be discrete
                    .x("{!! trans('privateCbsVoteAnalysis.total_votes') !!}")// key to use for x-axis
                    .order("position")
                    .color(function (d) {
                        return d.total_votes > 0 ? "#07A614" : "#A61106";
                    })
                    .font({"family": "Helvetica, Arial, sans-serif", "color": "#000"})
                    .resize(true)
                    .draw();

                // Error message if there are no results in Vote Analysis  :'(
                if(statistics_by_topic_data.length == 0){
                    var warningMessage = "{!! trim(preg_replace('/\s\s+/', ' ', trans('privateCbsVoteAnalysis.no_data_Available'))) !!}";
                    $('#statistics_by_topic').html('<div class="chartMessage"><div><i class="fa fa-eye-slash" aria-hidden="true"></i> '+warningMessage+'</div></div>');
                    // toastr.warning(warningMessage);
                    $("#chart_by_topic_download_wrapper_1").remove();
                } else {
                    // Export data for CSV (javascript)
                    $( "#statisticsByTopicDownloadCSV" ).click(function() {
                        var d = new Date();
                        var suffix_name = d.getFullYear()+"_"+(1+d.getMonth())+"_"+d.getDate()+"_"+d.getHours()+"_"+d.getMinutes()+"_"+d.getSeconds();
                        var filename = "statistics_by_topic_"+suffix_name+".csv";
                        downloadCSV(statistics_by_topic_data,filename);
                    });
                }

            });

            @include('private.cbs.cbVoteAnalysis.cbDetailsScript')

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
