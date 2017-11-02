@extends('private._private.index')
@section('header_styles')

@endsection
@section('content')
    @if(!empty($voteEvents))
        <div class="row">
            <div class="col-12" style="padding-bottom: 20px">
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
                        <th >{{trans('privateCbsVoteAnalysis.topic_name')}}</th>
                        <th>{{trans('privateCbsVoteAnalysis.total_budget')}}</th>
                        <th >{{trans('privateCbsVoteAnalysis.total_balance')}}</th>
                        <th >{{trans('privateCbsVoteAnalysis.total_positives')}}</th>
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
        $(function() {
            var array = ["{{ $type }}", "{{$cbKey}}"];
            getSidebar('{{ action("OneController@getSidebar") }}', 'votes_summary', array, 'voteAnalysis' );
        });

        $("#voteEventSelect").select2();

        $( document ).ready(function() {
            selectSiteFilter();
        });


        function selectSiteFilter(){
            var voteKey = '';

            if ($('#voteEventSelect').val() != ''){
                voteKey = $('#voteEventSelect').val();
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

                    }
                },
                columns: [
                    {data: 'title', name: 'title'},
                    {data: 'budget', name: 'budget'},
                    {data: 'balance', name: 'balance'},
                    {data: 'positives', name: 'positives'},
                    {data: 'negatives', name: 'negatives'}
                ],
                order: [['1', 'desc']]
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
                    }
                },
                columns: [
                    {data: 'title', name: 'title'},
                    {data: 'budget', name: 'budget'},
                    {data: 'balance', name: 'balance'},
                    {data: 'positives', name: 'positives'},
                    {data: 'negatives', name: 'negatives'}
                ],
                order: [['1', 'desc']]
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