@extends('private._private.index')

@section('content')

    @include('private.cbs.tabs')

    <div class="box box-primary background-white">
        <div class="box-body">
            @if(!empty($voteEvents))
                <div class="col-12" style="padding-bottom: 20px">
                    <select id="voteEventSelect" name="voteEventSelect" class="voteEventSelect" style="width: 50%;">
                        <option value="">{{ trans('privateCbsVote.select_vote_event') }}</option>
                        @foreach($voteEvents as $key => $voteEvent)
                            <option value="{!! $key !!}" @if(Session::get("voteEventKey") == $key) selected @endif >{!! $voteEvent !!}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            @if(!empty($userParameters))
                <div class="col-12" id="userParameterSelectDiv" style="padding-bottom: 20px" hidden>
                    <select id="userParameterSelect" name="userParameterSelect" class="userParameterSelect" style="width: 50%;">
                        @foreach($userParameters as $key => $parameter)
                            <option value="{!! $key !!}">{!! $parameter !!}</option>
                        @endforeach
                    </select>
                </div>
            @endif


            <div class="col-12" id="nav-analysis" hidden="hidden">
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="nav-item"><a class="nav-link active" href="#tab_vote_analysis_date" role="tab" id="vote_analysis_date" data-toggle="tab" aria-expanded="true">{{ trans('privateCbsVote.vote_analysis_by_date') }}</a></li>
                    <li role="presentation" class="nav-item"><a class="nav-link" href="#tab_vote_analysis_last_day" role="tab" id="vote_analysis_last_day" data-toggle="tab" aria-expanded="true">{{ trans('privateCbsVote.vote_analysis_last_day') }}</a></li>
                    <li role="presentation" class="nav-item"><a class="nav-link" href="#tab_vote_analysis_top_ten" id="vote_analysis_top_ten" role="tab" data-toggle="tab" aria-expanded="false">{{ trans('privateCbsVote.vote_top_ten') }}</a></li>
                    <li role="presentation" class="nav-item"><a class="nav-link" href="#tab_vote_analysis_top_tree_by_date" id="vote_analysis_top_tree_by_date" role="tab" data-toggle="tab" aria-expanded="false">{{ trans('privateCbsVote.vote_top_tree_by_date') }}</a></li>
                    @if(!empty($userParameters))
                        <li role="presentation" class="nav-item"><a class="nav-link" href="#tab_vote_analysis_by_parameter" id="vote_analysis_by_parameter" role="tab" data-toggle="tab" aria-expanded="false">{{ trans('privateCbsVote.vote_by_parameter') }}</a></li>
                    @endif
                </ul>
                <!-- Tab panes -->
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="tab_vote_analysis_date">
                        @if(!empty($voteEventKey))
                            <h3> {{ trans('privateCbsVote.total_vote_statistics_by_date') }}</h3>
                            <div id="statistics_by_date" style="">
                            </div>
                        @endif
                    </div>
                    <div role="tabpanel" class="tab-pane" id="tab_vote_analysis_last_day">

                    </div>
                    <div role="tabpanel" class="tab-pane" id="tab_vote_analysis_top_ten">

                    </div>
                    <div role="tabpanel" class="tab-pane" id="tab_vote_analysis_top_tree_by_date">

                    </div>
                    @if(!empty($userParameters))
                        <div role="tabpanel" class="tab-pane" id="tab_vote_analysis_by_parameter">

                        </div>
                    @endif
                </div>
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
                }, // a JSON object to send back
                success: function (response) { // What to do if we succeed
                    if (response != 'false') {
                        $('#tab_'+id).empty();
                        $('#tab_'+id).append(response);
                        if( id == 'vote_analysis_by_parameter' )
                        {
                            $('#userParameterSelectDiv').show();
                        }else{
                            $('#userParameterSelectDiv').hide();
                        }

                    }
                },
                error: function () { // What to do if we fail

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

    {{--vote statistics chart by date--}}
    @if(!empty($voteEventKey))
        <script>
            var statistics_by_date_data = [

            ];
            var visualization = d3plus.viz()
                .container("#statistics_by_date")
                .data(statistics_by_date_data)
                .type("line")
                .id("name")
                .y('{!! trans('privateCbs.votes') !!}')
                .x('{!! trans('privateCbs.date') !!}')
                .font({"family": "Helvetica, Arial, sans-serif", "color": "#000"}).resize(true)
                .draw();

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