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
                <li role="presentation" class="nav-item"><a class="nav-link active" href="#tab_voters_per_day" role="tab" id="voters_per_day" data-toggle="tab" aria-expanded="true">{{ trans('privateCbsVoteAnalysis.voters_per_date') }}</a></li>
                <li role="presentation" class="nav-item"><a class="nav-link" href="#tab_vote_analysis_last_day" role="tab" id="vote_analysis_last_day" data-toggle="tab" aria-expanded="true">{{ trans('privateCbsVoteAnalysis.vote_analysis_last_day') }}</a></li>
            </ul>
            <!-- Tab panes -->
            <div class="tab-content background-white">
                <div role="tabpanel" class="tab-pane active default-padding" id="tab_voters_per_day">
                    @if(!empty($voteEventKey))
                        {{--<h3> {{ trans('privateCbsVoteAnalysis.total_voters_per_date') }}</h3>--}}
                        <div id="statistics_voters_per_day" style="height: 300px">
                        </div>
                    @endif
                </div>
                <div role="tabpanel" class="tab-pane default-padding" id="tab_vote_analysis_last_day">

                </div>
            </div>
        </div>
    </div>


@endsection

@section('scripts')
    <script>

        $(function() {
            var array = ["{{ $type }}", "{{$cbKey}}"];
            getSidebar('{{ action("OneController@getSidebar") }}', 'voters_per_day', array, 'voteAnalysis' );
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

        $( "#userParameterSelect" ).change(function() {
            var keySelected = $('#voteEventSelect').val();
            var navActive = $("#nav-analysis").find(".active").attr('id');
            getVoteAnalysis(navActive,keySelected);
        });

        function getVoteAnalysis(id,voteEventKey){
            $.ajax({
                method: 'POST', // Type of response and matches what we said in the route
                url: "{{action('CbsController@getVoteAnalysis')}}", // This is the url we gave in the route
                data: {
                    statistics_type: id,
                    vote_event_key: voteEventKey,
                    parameter_key: ''
                },beforeSend: function () {
                    var ajaxLoader = '<div class="chartLoader"><div><i class="fa fa-spinner fa-pulse fa-3x fa-fw default-color"></i><span class="sr-only">Loading...</span></div></div>';
                    $('#tab_'+id).html(ajaxLoader);
                },
                success: function (response) { // What to do if we succeed
                    if (response != 'false') {
                        $('#tab_'+id).empty();
                        $('#tab_'+id).append(response);
                    } else {
                        var errorMessage = "{!! trim(preg_replace('/\s\s+/', ' ', trans('privateCbsVoteAnalysis.something_went_wrong'))) !!}";
                        $('#tab_'+id).html('<div class="chartMessage"><div><i class="fa fa-eye-slash" aria-hidden="true"></i> '+errorMessage+'</div></div>');
                        toastr.error(errorMessage);
                        $(".chartLoader").remove();
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
    </script>
    @if(!empty($voteEventKey))
        <script>
            $( document ).ready(function() {
                var statistics_voters_per_day_data = [
                        @foreach($votersPerDate->total->all_votes as $date => $voteValue)
                    {
                        '{!! trans('privateCbsVoteAnalysis.date') !!}': "{{ $date }}",
                        "name": '{!! trans('privateCbsVoteAnalysis.voters_per_day') !!}',
                        '{!! trans('privateCbsVoteAnalysis.votes') !!}': {{ number_format($voteValue, 3, '.', ',') }} },
                    @endforeach

                ];
                var visualization = d3plus.viz()
                    .container("#statistics_voters_per_day")
                    .data(statistics_voters_per_day_data)
                    .type("line")
                    .id("name")
                    .y('{!! trans('privateCbsVoteAnalysis.votes') !!}')
                    .x('{!! trans('privateCbsVoteAnalysis.date') !!}')
                    .font({"family": "Helvetica, Arial, sans-serif", "color": "#000"}).resize(true)
                    .draw();

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