@extends('private._private.index')

@section('content')
    <div class="box box-primary">
        <div class="box-body">
            <div class="margin-bottom-20">
                <div class="row">
                    <div class="col-12">
                        <h5 class="filterBy-title">{!! trans("privateCbs.filter_by") !!}</h5>
                    </div>
                    <div class="col-12 col-sm-6 col-md-3">
                        <label for="start_date">{!! trans("privateCbs.filterDateMin") !!}</label>
                        <div class="input-group date">
                            <span class="input-group-addon">
                                <i class="glyphicon glyphicon-th"></i>
                            </span>
                            <input class="form-control oneDatePicker" style="width:40%" id="start_date" placeholder="yyyy-mm-dd" data-date-format="yyyy-mm-dd" name="start_date" type="text" value="" required>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-3">
                        <label for="end_date">{!! trans("privateCbs.filterDateMax") !!}</label>
                        <div class="input-group date">
                            <span class="input-group-addon">
                                <i class="glyphicon glyphicon-th"></i>
                            </span>
                            <input class="form-control oneDatePicker" style="width:40%" id="end_date" placeholder="yyyy-mm-dd" data-date-format="yyyy-mm-dd" name="end_date" type="text" value="" onchange="selectDateFilter()" required>
                        </div>
                    </div>
                    @if(Session::has('user_role') == 'admin' || ONE::verifyUserPermissionsCreate('cb', $typeFilter))
                        <div class="col-12 col-sm-12 col-md-6 text-right">
                            <br>
                            <a type="" class="btn btn-flat empatia" href="{{action('CbsController@create',$typeFilter)}}" style="margin: 5px"><i class="fa fa-plus"></i> {!! trans("privateCbs.create_".$typeFilter) !!}</a>
                            <a type="" class="btn btn-flat empatia pull-right" onclick="waitingModal('{{Session::get('X-ENTITY-KEY')}}')" style="margin: 5px"><i class="fa fa-refresh"></i> {!! trans("privateCbs.update_vote_count") !!}</a>
                        </div>
                    @endif
                </div>
            </div>
            <table id="all_cbs" class="table table-hover table-striped dataTable no-footer table-responsive">
                <thead>
                <tr>
                    <th>{{ trans('privateCbs.name') }}</th>
                    <th>{{ trans('privateCbs.start_date') }}</th>
                    <th>{{ trans('privateCbs.end_date') }}</th>
                    <th>{{ trans('privateCbs.person') }}</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>

    <!-- swaiting modal -->
    <div class="modal fade" tabindex="-1" role="dialog" id="waitingModal" >
        <div class="modal-dialog">
            <div class="modal-content">
                {{--<div class="modal-header">--}}

                {{--</div>--}}
                <div class="modal-body" style="color: black;">
                    <div class="text-center">
                        {{ trans("privateCbs.please_wait") }}
                        <br>
                        <img src="{{ asset('images/bluePreLoader.gif') }}" alt="Loading" class="label-ajax-info-register-loader" style="width: 20px; padding-top:2px;"/>
                    </div>
                </div>
                {{--<div class="modal-footer">--}}

                {{--</div>--}}
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
@endsection


@section('scripts')

    <script>
        showCbsDataTable();

        function showCbsDataTable(){

//            var filterTypes = $("#advancedFilter").val();
            var filterTypes = ['{{ $typeFilter ?? null}}'];


            $('#all_cbs').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },
                responsive: true,
                processing: true,
                serverSide: true,
                bDestroy: true,
                ajax: {
                    'url': '{!! action('CbsController@getActivePads', $start_date ?? null) !!}',
                    "type": "POST",
                    "data": {
                        "filter_types": '{{$typeFilter}}',
                    }
                },
                columns: [
                    {data: 'title', name: 'title'},
                    {data: 'start_date', name: 'start_date'},
                    {data: 'end_date', name: 'end_date'},
                    {data: 'name', name: 'name'},
                ],
                order: [['1', 'desc']]
            });

        }

        function selectDateFilter() {
            var url = "{{action('CbsController@getActivePads')}}";

            var start_date = '';
            var end_date = '';

            if ($('#start_date').val() != '' && $('#end_date').val() != '' ){

                start_date = $('#start_date').val();
                end_date=$('#end_date').val();

                if ($('#start_date').val()< $('#end_date').val() ){

                    url = url+'?start_date='+start_date+'?end_date='+end_date;

                    var tableUsers = $('#all_cbs').DataTable({
                        language: {
                            url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}'
                        },
                        processing: true,
                        serverSide: true,
                        bDestroy: true,
                        ajax:
                            {
                                'url': url,
                                "type": "POST",
                                "data": {
                                    "filter_types": '{{$typeFilter}}',
                                }
                            },
                        columns: [
                            {data: 'title', name: 'title'},
                            {data: 'start_date', name: 'start_date'},
                            {data: 'end_date', name: 'end_date'},
                            {data: 'name', name: 'name'},
                        ],
                        order: [['1', 'asc']],
                    });
                }
            }
        }

        function waitingModal(entityKey) {

            $('#waitingModal').modal({backdrop: 'static', keyboard: false});
            $('#waitingModal').modal('show');

            $.ajax({
                method: 'POST',
                url: '{{action('EntitiesController@manualUpdateTopicVotesInfo')}}',
                data: {
                    entity_key: entityKey
                },
                success: function (response) { // What to do if we succeed
                    $('#waitingModal').modal('hide');
                },
                error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                    location.reload();
                    console.log(JSON.stringify(jqXHR));
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                }
            });
        }
    </script>
@endsection
