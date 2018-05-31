@extends('private._private.index')

@section('content')
    <div class="box box-primary">
        <div class="box-body">
            <div class="margin-bottom-20">
                <div class="row">
                    <div class="col-12 text-right" style="margin-bottom: 10px">
                        <a type="" class="btn btn-flat empatia" href="{{action('CbsController@stepType')}}" style="margin: 5px"><i class="fa fa-plus"></i> {!! trans("privateCbs.create_cb") !!}</a>
                        @if((Session::has('user_role') == 'admin'))
                            <a type="" class="btn btn-flat empatia pull-right" onclick="waitingModal('{{Session::get('X-ENTITY-KEY')}}')" style="margin: 5px"><i class="fa fa-refresh"></i> {!! trans("privateCbs.update_vote_count") !!}</a>
                        @endif
                    </div>
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
                            <input class="form-control oneDatePicker" style="width:40%" id="end_date" placeholder="yyyy-mm-dd" data-date-format="yyyy-mm-dd" name="end_date" type="text" value="" required>
                        </div>
                    </div>
                    @if(empty($typeFilter))
                        <div class="col-12 col-sm-6 col-md-3 form-group">
                            <label for="cbType">{!! trans("privateCbs.filter_by_cb_type") !!}</label>
                            <select name="cbType" id="cbType" class="form-control">
                                <option value="">{{ trans("privateCbs.select_cb_type") }}</option>
                                @foreach($allCbTypes as $cbType)
                                    @if(ONE::verifyModuleAccess('cb',$cbType->code))
                                        <option value="{{ $cbType->code }}">
                                            {{ trans('privateCbs.' . $cbType->code) }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    @else
                        <input type="hidden" name="cbType" id="cbType" value="{{ $typeFilter }}"/>
                    @endif

                </div>
            </div>
            <table id="all_cbs" class="table table-hover table-striped dataTable no-footer table-responsive">
                <thead>
                <tr>
                    @if(empty($typeFilter))
                        <th>{{ trans('privateCbs.cb_ype') }}</th>
                    @endif
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
                        <img src="{{ asset('images/default/bluePreLoader.gif') }}" alt="Loading" class="label-ajax-info-register-loader" style="width: 20px; padding-top:2px;"/>
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
        var dataTable;

        $(document).ready(function() {
            dataTable = $('#all_cbs')
                .DataTable({
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
                        "data": function(d) {
                            d.filter_types = $("#cbType").val();

                            startDate = $('#start_date').val();
                            endDate = $('#end_date').val();
                            if (startDate !== "" && endDate !== "") {
                                d.start_date = startDate;
                                d.end_date = endDate;
                            }
                        }
                    },
                    columns: [
                        @if(empty($typeFilter))
                            {data: 'type', name: 'type'},
                        @endif
                        {data: 'title', name: 'title', "sType": "html"},
                        {data: 'start_date', name: 'start_date'},
                        {data: 'end_date', name: 'end_date'},
                        {data: 'name', name: 'name'},
                    ],
                    order: [['1', 'desc']]
                });


            $("#start_date, #end_date").on("change",function() {
                dataTable.ajax.reload();
            });
            @if(empty($typeFilter))
                $("#cbType").on("change",function() {
                    dataTable.ajax.reload();
                });
            @endif
        });

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
