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
        <div class="col-12 tabs-left" id="nav-analysis" hidden = "hidden">
            <ul class="nav nav-tabs" role="tablist">
                @foreach($parametersFiltered as $key => $parameter)
                    <li class="nav-item" role="presentation"><a class="nav-link {{($loop->iteration == 1 ? 'active' : null)}}" href="#tab_{{$key}}" role="tab" id="{{$key}}" data-toggle="tab" aria-expanded="true">{{ trans('privateCbsVoteAnalysis.vote_analysis_by') }} {{$parameter}}</a></li>
                @endforeach
            </ul>
            <!-- Tab panes -->
            <div class="tab-content" >
                @foreach($parametersFiltered as $key => $parameter)
                    <div role="tabpanel" class="tab-pane {{($loop->iteration == 1 ? 'active' : null)}}" id="tab_{{$key}}">
                        @if(!empty($voteEventKey) && $loop->iteration == 1)
                            <div class="row">
                                <div class="col-12" style="padding-bottom: 20px;padding-top: 20px">
                                    <!-- Download -->
                                    <div class="chart-download-wrapper_{{$parameterKey}}">
                                        <a id="downloadCSV_{{$parameterKey}}"  class="btn btn-flat btn-blue pull-right">
                                            <i class="fa fa-file-excel-o" aria-hidden="true"></i> {{ trans('privateCbsVoteAnalysis.download_csv') }}
                                        </a>
                                    </div>
                                    <!-- Chart -->
                                    <div id="statistics_by_{{$parameterKey}}" style="height: 300px">
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

        var id = '';
        $('a[data-toggle="tab"]').on('shown.bs.tab', function () {
            var id = $(this).attr('id');
            var voteEventKey = '{{$voteEventKey ?? null}}';
            var cbKey = '{{$cbKey ?? null}}';
            if(voteEventKey.length == 0){
                voteEventKey = $( "#voteEventSelect" ).val();
            }
            getVoteAnalysis(id, cbKey, voteEventKey);
        });

        @if(empty($voteEvents))
             $('#nav-analysis').removeAttr('hidden');
        @endif

        $( "#voteEventSelect" ).change(function() {
            renderSelectedOptions();
        });

        function getVoteAnalysis(id, cbKey, voteEventKey){
            $.ajax({
                method: 'POST', // Type of response and matches what we said in the route
                url: "{{action('CbsController@getVoteAnalysis')}}", // This is the url we gave in the route
                data: {
                    statistics_type: 'vote_analysis_topic_parameters',
                    vote_event_key: voteEventKey,
                    parameter_key: id,
                    cb_key: cbKey,
                }, beforeSend: function () {
                    var ajaxLoader = '<div class="chartLoader"><div><i class="fa fa-spinner fa-pulse fa-3x fa-fw default-color"></i><span class="sr-only">Loading...</span></div></div>';
                    $('#tab_'+id).html(ajaxLoader);
                },
                success: function (response) { // What to do if we succeed
                    if (response != 'false') {
                        $('#tab_'+id).empty();
                        $('#tab_'+id).append(response);
                    } else {
                        var errorMessage = "{!! trim(preg_replace('/\s\s+/', ' ', trans('privateCbsVoteAnalysis.something_went_wrong'))) !!}";
                        $('#tab_' + id).html('<div class="chartMessage"><div><i class="fa fa-eye-slash" aria-hidden="true"></i> ' + errorMessage + '</div></div>');
                        toastr.error(errorMessage);
                    }
                },
                error: function () { // What to do if we fail
                    var errorMessage = "{!! trim(preg_replace('/\s\s+/', ' ', trans('privateCbsVoteAnalysis.something_went_wrong'))) !!}";
                    $('#tab_'+id).html('<div class="chartMessage"><div><i class="fa fa-eye-slash" aria-hidden="true"></i> '+errorMessage+'</div></div>');
                    toastr.error(errorMessage);
                    $(".chartLoader").remove();
                }
            });
        }

        function renderSelectedOptions(){
            var keySelected = $("#voteEventSelect").val();
            var cbKey = '{{$cbKey ?? null}}';
            if(keySelected.length == 0){
                $('#nav-analysis').attr('hidden','hidden');
                $('#userParameterSelectDiv').hide();
            }else{
                var navActive = $("#nav-analysis").find(".active").attr('id');
                getVoteAnalysis(navActive,cbKey,keySelected);
                $('#nav-analysis').removeAttr('hidden');
            }
        }
    </script>

    @if(!empty($voteEventKey))
        <script>
            $( document ).ready(function() {
                // Data
                var statistics_by_topic_parameters = [
                    @foreach($topicParameters->total->all_votes as $date => $voteValue)
                    {   '{!! trans('privateCbsVoteAnalysis.date') !!}': "{{ $date }}",
                        "name": '{!! trans('privateCbsVoteAnalysis.votes') !!}',
                        '{!! trans('privateCbsVoteAnalysis.votes') !!}': {{ number_format($voteValue, 3, '.', ',') }} },
                    @endforeach
                ];

                // Setting Y range - bug fix when values are 0's
                var chartRange = [0, 10];
                for(var i = 0 ; i< statistics_by_topic_parameters.length; i++ ){
                    if(statistics_by_topic_parameters[i].{!! trim(preg_replace('/\s\s+/', ' ',trans('privateCbsVoteAnalysis.votes'))) !!} != 0){
                        chartRange = false;
                    }
                }

                // D3 plus chart rendering
                d3plus.viz()
                    .container("#statistics_by_{{$parameterKey}}")
                    .data(statistics_by_topic_parameters)
                    .type("bar")
                    .id("name")
                    .y({
                        "value": "{!! trim(preg_replace('/\s\s+/', ' ',trans('privateCbsVoteAnalysis.votes'))) !!}",
                        "range": chartRange
                    })
                    .x('{!! trans('privateCbsVoteAnalysis.date') !!}')
                    .font({"family": "Helvetica, Arial, sans-serif", "color": "#000"})
                    .resize(true)
                    .draw();

                // Error message if there are no results in Vote Analysis  :'(
                if(statistics_by_topic_parameters.length == 0){
                    var warningMessage = "{!! trim(preg_replace('/\s\s+/', ' ', trans('privateCbsVoteAnalysis.no_data_Available'))) !!}";
                    $('#statistics_by_last_day').html('<div class="chartMessage"><div><i class="fa fa-eye-slash" aria-hidden="true"></i> '+warningMessage+'</div></div>');
                    toastr.warning(warningMessage);
                    $(".chart-download-wrapper_{{$parameterKey}}").remove();
                } else {
                    // Export data for CSV (javascript)
                    $( "#downloadCSV_{{$parameterKey}}" ).click(function() {
                        var d = new Date();
                        var suffix_name = d.getFullYear()+"_"+(1+d.getMonth())+"_"+d.getDate()+"_"+d.getHours()+"_"+d.getMinutes()+"_"+d.getSeconds();
                        var filename = "statistics_by_last_day_"+suffix_name+".csv";
                        downloadCSV(statistics_by_topic_parameters,filename);
                    });
                }

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