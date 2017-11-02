@extends('private._private.index')
@section('header_styles')

@endsection
@section('content')
    @if(!empty($voteEvents))
        <div class="row">
            <div class="col-12" style="padding-bottom: 20px">
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
                <li role="presentation" class="nav-item"><a class="nav-link active" href="#tab_vote_analysis_top_ten" id="vote_analysis_top_ten" role="tab" data-toggle="tab" aria-expanded="false">{{ trans('privateCbsVoteAnalysis.vote_top_ten') }}</a></li>
                <li role="presentation" class="nav-item"><a class="nav-link" href="#tab_vote_analysis_top_tree_by_date" id="vote_analysis_top_tree_by_date" role="tab" data-toggle="tab" aria-expanded="false">{{ trans('privateCbsVoteAnalysis.vote_top_tree_by_date') }}</a></li>
            </ul>
            <!-- Tab panes -->
            <div class="tab-content background-white">
                <div role="tabpanel" class="tab-pane active default-padding" id="tab_vote_analysis_top_ten">
                    @if(!empty($voteEventKey))
{{--
                        <h3> {{ trans('privateCbsVoteAnalysis.vote_statistics_by_Top_ten') }}</h3>
--}}
                        <!-- Download -->
                        <div id="statistics_by_top_chart-download-wrapper" class="chart-download-wrapper">
                            <a id="statistics_by_top_DownloadCSV" class="btn btn-flat btn-blue pull-right">
                                <i class="fa fa-file-excel-o" aria-hidden="true"></i> {{ trans('privateCbsVoteAnalysis.download_csv') }}
                            </a>
                        </div>
                        <!-- D3 Chart -->
                        <div id="statistics_by_top" style="height: 300px;">
                        </div>
                    @endif
                </div>
                <div role="tabpanel" class="tab-pane default-padding" id="tab_vote_analysis_top_tree_by_date">

                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script>

        $(function() {
            var array = ["{{ $type }}", "{{$cbKey}}"];

            getSidebar('{{ action("OneController@getSidebar") }}', 'top_votes', array, 'voteAnalysis' );
        });

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


        $( "#userParameterSelect" ).change(function() {
            var keySelected = $('#voteEventSelect').val();
            var navActive = $("#nav-analysis").find(".active").attr('id');
            getVoteAnalysis(navActive,keySelected);
        });



        function getVoteAnalysis(id,voteEventKey){
            var parameterKey = $( "#userParameterSelect" ).val();
            $.ajax({
                method: 'POST', // Type of response and matches what we said in the route
                url: "{{action('CbsController@getVoteAnalysis')}}", // This is the url we gave in the route
                data: {
                    statistics_type: id,
                    vote_event_key: voteEventKey,
                    parameter_key: parameterKey
                },
                beforeSend: function () {
                    var ajaxLoader = '<div class="chartLoader"><div><i class="fa fa-spinner fa-pulse fa-3x fa-fw default-color"></i><span class="sr-only">Loading...</span></div></div>';
                    $('#tab_'+id).html(ajaxLoader);
                },
                success: function (response) { // What to do if we succeed
                    if (response != 'false') {
                        $('#tab_'+id).empty();
                        $('#tab_'+id).append(response);
                    } else {
                        var errorMessage = "{!! trim(preg_replace('/\s\s+/', ' ', trans('privateCbsVoteAnalysis.something_went_wrong'))) !!}";
                        toastr.error(errorMessage);
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
            }else{
                var navActive = $("#nav-analysis").find(".active").attr('id');
                getVoteAnalysis(navActive,keySelected);
                $('#nav-analysis').removeAttr('hidden');
            }
        }
    </script>

    @if(!empty($voteEventKey))
        <script>
            var statistics_by_top_data = [
                    @foreach($votesByTop->balance as $vote)
                {"position": "{{ $vote->position }}", "{!! trans('privateCbsVoteAnalysis.topic_name') !!}":"{{ $vote->topic_name }}","{!! trans('privateCbsVoteAnalysis.total_votes') !!}": {{ $vote->total_votes }}},
                @endforeach

            ];

            var visualization = d3plus.viz()
                .container("#statistics_by_top")  // container DIV to hold the visualization
                .data(statistics_by_top_data)  // data to use with the visualization
                .type("bar")// visualization type
                .id("{!! trans('privateCbsVoteAnalysis.topic_name') !!}")
                .y("{!! trans('privateCbsVoteAnalysis.topic_name') !!}")         // key to use for y-axis
                .y({"scale": "discrete"}) // Manually set Y-axis to be discrete
                .x( "{!! trans('privateCbsVoteAnalysis.total_votes') !!}")// key to use for x-axis
                .order("position")
                .legend({
                    "value": true,
                    "size": 35
                })
                .font({"family": "Helvetica, Arial, sans-serif", "color": "#000"}).resize(true)
                .draw();

                // Error message if there are no results in Vote Analysis  :'(
                if(statistics_by_top_data.length == 0){
                    var warningMessage = "{!! trim(preg_replace('/\s\s+/', ' ', trans('privateCbsVoteAnalysis.no_data_Available'))) !!}";
                    $('#statistics_by_top').html('<div class="chartMessage"><div><i class="fa fa-eye-slash" aria-hidden="true"></i> '+warningMessage+'</div></div>');
                    toastr.warning(warningMessage);
                    $("#statistics_by_top_chart-download-wrapper").remove();
                } else {
                    // Export data for CSV (javascript)
                    $( "#statistics_by_top_DownloadCSV" ).click(function() {
                        var d = new Date();
                        var suffix_name = d.getFullYear()+"_"+(1+d.getMonth())+"_"+d.getDate()+"_"+d.getHours()+"_"+d.getMinutes()+"_"+d.getSeconds();
                        var filename = "statistics_by_top_"+suffix_name+".csv";
                        downloadCSV(statistics_by_top_data, filename);
                    });
                }
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