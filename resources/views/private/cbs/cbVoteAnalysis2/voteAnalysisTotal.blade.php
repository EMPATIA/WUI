@extends('private._private.index')
@section('header_styles')
 <style>
    .margin-bottom-40{
        margin-bottom: 40px;
     }
</style>
@endsection
@section('content')
    @include('private.cbs.cbVoteAnalysis2.details.cbDetails')
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

    @include('private.cbs.cbVoteAnalysis2.details.cbMoreDetails')

    <div class="row">
        <div class="col-12 tabs-left" id="nav-analysis" hidden="hidden">
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="nav-item">
                    <a class="nav-link active" href="#tab_vote_analysis_total2" role="tab" id="vote_analysis_total2" data-toggle="tab" aria-expanded="true">
                        {{ trans('privateCbsVoteAnalysis.total') }}
                    </a>
                </li>
                <li role="presentation" class="nav-item">
                    <a class="nav-link" href="#tab_vote_analysis_by_channel2" role="tab" id="vote_analysis_by_channel2" data-toggle="tab" aria-expanded="true">
                        {{ trans('privateCbsVoteAnalysis.channel') }}
                    </a>
                </li>
            </ul>
            <!-- Tab panes -->
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="tab_vote_analysis_total2">
                    @if(!empty($voteEventKey))
                        @if(empty($statisticsTotalData) && empty($statisticsTotalSummary))
                            <div class="row">
                                <div class="col-12 text-center">
                                    <h4>{{trans('privateCbsVoteAnalysis.no_data_available')}}</h4>
                                </div>
                            </div>
                        @else
                            @include('private.cbs.cbVoteAnalysis2.tabVoteAnalysisTotal.voteAnalysisByTotal')
                        @endif
                    @endif
                </div>
                <div role="tabpanel" class="tab-pane" id="tab_vote_analysis_by_channel2">

                </div>
            </div>

            <div id="tabOptionLoader" class="tabOptionLoader default-color" style="height: 100%; width: 100%; position: absolute; top: 0px; left: 0px; background-color: rgba(250, 250, 250, 0.35); z-index: 100; display: none;">
                <div class="text-center" style="display:flex;justify-content:center;align-items:center;height:100%;width:100%;">
                    <i class="fa fa-circle-o-notch fa-spin fa-3x fa-fw"></i>
                    <span class="sr-only">Loading...</span>
                </div>
            </div>
        </div>
    </div>

    @php
        $voteEvent = Session::get("voteEvent");
    @endphp

@endsection

@section('scripts')

    <script>

        $("#voteEventSelect").select2();
        $("#userParameterSelect").select2();

        $('a[data-toggle="tab"]').on('shown.bs.tab', function () {
            $("#tab_vote_analysis_total2").hide();
            $("#tab_vote_analysis_by_channel2").hide();
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
                $('#moreDetails').hide();
            }else{
                var navActive = $("#nav-analysis").find("li > a.active").attr('id');
                getVoteAnalysis(navActive,keySelected);
                $('#nav-analysis').removeAttr('hidden');
                $('#moreDetails').show();
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
                    cb_key: '{{$cbKey}}',
                    view_submitted: $('input[name=view_submitted]:checked').val()
                }, beforeSend: function () {
                    $("#tabOptionLoader").show();
                },
                success: function (response) { // What to do if we succeed
                    console.log(response);
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
                    $("#tabOptionLoader").hide();
                },
                error: function () { // What to do if we fail
                    $(".chartLoader").remove();
                    var errorMessage = "{!! trim(preg_replace('/\s\s+/', ' ', trans('privateCbsVoteAnalysis.something_went_wrong'))) !!}";
                    toastr.error(errorMessage);
                    $('#tab_'+id).html('<div class="chartMessage"><div><i class="fa fa-eye-slash" aria-hidden="true"></i> '+errorMessage+'</div></div>');
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
                $('#userParameterSelectDiv').hide();
            }else{
                var navActive = $("#nav-analysis").find("li > a.active").attr('id');
                getVoteAnalysis(navActive,keySelected);
                $('#nav-analysis').removeAttr('hidden');
            }
        }
    </script>


    @if(!empty(Session::get("voteEventKey")) && !empty($voteEvents) && array_key_exists(Session::get("voteEventKey"),$voteEvents))
        <script>
            $( document ).ready(function() {
                renderSelectedOptions();
            });
        </script>
    @endif

@endsection
