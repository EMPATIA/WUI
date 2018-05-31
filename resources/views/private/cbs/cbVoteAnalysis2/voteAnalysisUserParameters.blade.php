@extends('private._private.index')

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
                @foreach($userParameters as $key => $parameter)
                    <li role="presentation" class="nav-item"><a class="nav-link {{($loop->iteration == 1 ? 'active' : null)}}" href="#tab_{{$key}}" role="tab" id="{{$key}}" data-toggle="tab" aria-expanded="true">{{ trans('privateCbsVoteAnalysis.vote_analysis_by') }} {{$parameter}}</a></li>
                @endforeach
            </ul>
            <!-- Tab panes -->
            <div id="tab-content" class="tab-content background-white">
                @forelse($userParameters as $key => $parameter)
                    <div role="tabpanel" class="tab-pane {{($loop->iteration == 1 ? 'active' : null)}}" id="tab_{{$key}}">

                    </div>
                @empty
                    <div class="chartMessage"><div><i class="fa fa-eye-slash" aria-hidden="true"></i> {{ trans('privateCbsVoteAnalysis.no_data_Available_for_this_user_parameter') }}</div></div>
                @endforelse
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
                    statistics_type: 'votes_by_user_parameters2', //   vote_analysis_by_parameter
                    vote_event_key: voteEventKey,
                    parameter_key: id,
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
                var navActive = $("#nav-analysis").find(".active").attr('id');
                getVoteAnalysis(navActive,keySelected);
                $('#nav-analysis').removeAttr('hidden');
            }
        }
    </script>

    @if(!empty($voteEventKey))
        <script>
            setTimeout(function(){
                var navActive = $("#nav-analysis").find(".active").attr('id');
                getVoteAnalysis(navActive,"<?php echo $voteEventKey; ?>");
            }, 50);
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