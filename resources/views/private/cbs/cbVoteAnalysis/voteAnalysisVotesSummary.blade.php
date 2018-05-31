@extends('private._private.index')
@section('header_styles')

@endsection
@section('content')
    @include('private.cbs.cbVoteAnalysis.cbDetails')

    @if(!empty($voteEvents))
        <div class="row">
            <div class="col-12" style="padding-bottom: 20px">
                <div><label>{{ trans('privateCbsVoteAnalysis.vote_event') }}</label></div>
                <select id="voteEventSelect" name="voteEventSelect" class="voteEventSelect" onchange="selectSiteFilter()" style="width: 50%;" required>
                    @foreach($voteEvents as $key => $voteEvent)
                        <option value="{!! $key !!}" @if(Session::get("voteEventKey") == $key) selected @endif>{!! $voteEvent !!}</option>
                    @endforeach
                </select>
            </div>
        </div>
    @endif

    <div class="box-private">
        <div class="row">
            <div class="col-12">
                <div class="box-body">
                    <table id="votes_summary_table" class="table table-hover table-responsive">
                        <thead>
                        <tr>
                            <th>{{trans('privateCbsVoteAnalysis.topic_number')}}</th>
                            <th>{{trans('privateCbsVoteAnalysis.topic_name')}}</th>
                            {{-- <th>{{trans('privateCbsVoteAnalysis.total_budget')}}</th> --}}
                            <th>{{trans('privateCbsVoteAnalysis.total_balance_submitted')}}</th>
                            <th>{{trans('privateCbsVoteAnalysis.total_positives_submitted')}}</th>
                            <th>{{trans('privateCbsVoteAnalysis.total_negatives_submitted')}}</th>
                            <th>{{trans('privateCbsVoteAnalysis.total_balance')}}</th>
                            <th>{{trans('privateCbsVoteAnalysis.total_positives')}}</th>
                            <th>{{trans('privateCbsVoteAnalysis.total_negatives')}}</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>

        $("#voteEventSelect").select2();

        $( document ).ready(function() {
            selectSiteFilter();
        });

        function selectSiteFilter(){
            var voteKey = '';
            if( typeof $("#voteEventSelect").val() != "undefined" && $('#voteEventSelect').val() != '') {
                voteKey = $("#voteEventSelect").val();
            } else {
                voteKey = "{{$voteEventKey ?? null}}";
            }

            $('#votes_summary_table').DataTable({
                "bDestroy": true,
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{!! action('CbsController@getVotesSummaryTable',['type'=>$type,'cbKey'=>$cbKey]) !!}',
                    data: {
                        vote_event_key: voteKey,
                        view_submitted: $('input[name=view_submitted]:checked').val(),
                    }
                },
                columns: [
                    {data: 'number', name: 'number'},
                    {data: 'title', name: 'title'},
                    {data: 'balance_submitted', name: 'balance_submitted'},
                    {data: 'positives_submitted', name: 'positives_submitted'},
                    {data: 'negatives_submitted', name: 'negatives_submitted'},
                    {data: 'balance', name: 'balance'},
                    {data: 'positives', name: 'positives'},
                    {data: 'negatives', name: 'negatives'}
                ],
                "lengthChange": false,
                "paging": false
            });
        }

        @if(empty($voteEvents))
            $('#votes_summary_table').DataTable({
                "bDestroy": true,
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{!! action('CbsController@getVotesSummaryTable',['type'=>$type,'cbKey'=>$cbKey]) !!}',
                    data: {
                        vote_event_key: '{{$voteEventKey}}',
                        view_submitted: $('input[name=view_submitted]:checked').val(),
                    }
                },
                columns: [
                    {data: 'number', name: 'number'},
                    {data: 'title', name: 'title'},
                    {data: 'balance_submitted', name: 'balance_submitted'},
                    {data: 'positives_submitted', name: 'positives_submitted'},
                    {data: 'negatives_submitted', name: 'negatives_submitted'},
                    {data: 'balance', name: 'balance'},
                    {data: 'positives', name: 'positives'},
                    {data: 'negatives', name: 'negatives'}
                ],
                "lengthChange": false,
                "paging": false
            });
        @endif


    </script>

    @if(!empty(Session::get("voteEventKey")) && !empty($voteEvents) && array_key_exists(Session::get("voteEventKey"),$voteEvents))
        <script>
            $( document ).ready(function() {
                selectSiteFilter();
            });
        </script>
    @endif

@endsection