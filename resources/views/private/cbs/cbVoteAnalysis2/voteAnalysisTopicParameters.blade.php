@extends('private._private.index')

@section('header_scripts')
    <!-- Geolocation -->
    <script type="text/javascript"  src="{{ asset('js/js-marker-clusterer-gh-pages/src/markerclusterer.js') }}"></script>
    <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false"></script>

    <script type="text/javascript" src="/js/heatmapjs/gmaps-heatmap.js"></script>
    <script type="text/javascript" src="/js/heatmapjs/heatmap.min.js"></script>
@endsection

@section('content')
{{--    @include('private.cbs.cbVoteAnalysis.cbDetails')--}}
    @include('private.cbs.cbVoteAnalysis2.details.cbMoreDetails')

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
        <div class="col-12 tabs-left" id="nav-analysis" hidden = "hidden">
            <ul class="nav nav-tabs" role="tablist">
                @foreach($parametersFiltered as $key => $parameter)
                    <li class="nav-item" role="presentation">
                        <a class="nav-link {{($loop->iteration == 1 ? 'active' : null)}}" href="#tab_{{$key}}" role="tab" id="{{$key}}" data-toggle="tab" aria-expanded="true">
                            {{ trans('privateCbsVoteAnalysis.vote_analysis_by') }} {{$parameter}}
                        </a>
                    </li>
                @endforeach
            </ul>
            <!-- Tab panes -->
            <div class="tab-content" >
                @forelse($parametersFiltered as $parameterKey => $parameter)
                    <div role="tabpanel" class="tab-pane {{($loop->iteration == 1 ? 'active' : null)}}" id="tab_{{$parameterKey}}">
                        @if(!empty($voteEventKey) && $loop->iteration == 1)
                            {{--
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
                            </div>--}}
                        @endif
                    </div>
                @empty
                    <div class="chartMessage"><div><i class="fa fa-eye-slash" aria-hidden="true"></i> {{ trans('privateCbsVoteAnalysis.no_data_Available') }}</div></div>
                @endforelse
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
            var voteEventKey = '{{Session::get("voteEventKey") ?? null}}';
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
                    statistics_type: 'votes_by_topic_parameters2',
                    vote_event_key: voteEventKey,
                    parameter_id: id,
                    cb_key: cbKey,
                    view_submitted: $('input[name=view_submitted]:checked').val()
                }, beforeSend: function () {
                    var ajaxLoader = '<div class="chartLoader"><div><i class="fa fa-circle-o-notch fa-spin fa-3x fa-fw default-color"></i><span class="sr-only">Loading...</span></div></div>';
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
            var keySelected = "";
            if( typeof $("#voteEventSelect").val() != "undefined") {
                keySelected = $("#voteEventSelect").val();
            } else {
                keySelected = "{{$voteEventKey ?? null}}";
            }
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

    @if(!empty(Session::get("voteEventKey")) && !empty($voteEvents) && array_key_exists(Session::get("voteEventKey"),$voteEvents))
        <script>
            $( document ).ready(function() {
                setTimeout(function(){
                    renderSelectedOptions();
                }, 25);
            });
        </script>
    @elseif(!empty($voteEventKey))
        <script>
           setTimeout(function(){
                var navActive = $("#nav-analysis").find(".active").attr('id');
                getVoteAnalysis(navActive,"{{$cbKey}}","{{ $voteEventKey }}");
            }, 25);
        </script>
    @endif

@endsection