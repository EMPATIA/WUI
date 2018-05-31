
@extends('private._private.index')

@section('content')
    @include('private.cbs.tabs')

    <div class="box box-primary box-default-margin">
        <div class="box-header">
            <h3 class="box-title"> {{ trans('privateVote.voteList') }}
            </h3>
        </div>
        <div class="box-body" style="overflow-x:auto;">
            <div class="row">
                @if(Session::get('user_role') == 'admin')
                    <div class="col-2">
                        <label for="userId">
                            {{ trans("privateVote.user_id") }}
                        </label>
                        <div class="form-group">
                            <input type="number" name="userId" class="form-control" id="userId" placeholder={{ trans("privateVote.user_id_placeholder") }} onchange="reloadDataTable()">
                        </div>
                    </div>
                @endif
                @if(Session::get('user_role') == 'admin')
                    <div class="col-3">
                        <label for="voteKey">
                            {{ trans("privateVote.voteKey") }}
                        </label>
                        <div class="form-group">
                            <input type="text" name="voteKey" class="form-control" id="voteKey" placeholder={{ trans("privateVote.vote_key_placeholder") }} onchange="reloadDataTable()">
                        </div>
                    </div>
                @endif
                <div class="col-3">
                    <label for="sources">
                        {{ trans("privateVote.source") }}
                    </label>
                    <select id="sources" style="width:100%" class="form-control filters filters_select" name="sources" onchange="reloadDataTable()">
                        <option value=""> {{ trans("privateVote.select_an_option") }}</option>
                        <option value="pc"> {{ trans("privateVote.pc") }}</option>
                        <option value="mobile"> {{ trans("privateVote.mobile") }}</option>
                        <option value="in_person"> {{ trans("privateVote.in_person") }}</option>
                    </select>
                </div>
                <div class="col-2">
                    <label for="deleted">
                        {{ trans("privateVote.deleted") }}
                    </label>
                    <div class="onoffswitch">
                        <input type="checkbox" name="deleted" class="onoffswitch-checkbox" id="deleted" value="1" checked onchange="reloadDataTable()">
                        <label class="onoffswitch-label" for="deleted">
                            <span class="onoffswitch-inner"></span>
                            <span class="onoffswitch-switch"></span>
                        </label>
                    </div>
                </div>
                <div class="col-2">
                    <label for="submitted">
                        {{ trans("privateVote.submitted") }}
                    </label>
                    <div class="onoffswitch">
                        <input type="checkbox" name="submitted" class="onoffswitch-checkbox" id="submitted" value="1" checked onchange="reloadDataTable()">
                        <label class="onoffswitch-label" for="submitted">
                            <span class="onoffswitch-inner"></span>
                            <span class="onoffswitch-switch"></span>
                        </label>
                    </div>
                </div>



            </div>
            {{-- <div style="margin-bottom:50px">
                <div class="pull-right">
                    @if(Session::get('user_role') == 'admin')
                        <a href="{{ action('CbsVoteController@create', ['type'=>$type,'cbKey'=>$cb->cb_key]) }}" class="btn btn-flat empatia">
                            <i class="fa fa-plus"></i>
                            {{ trans('privateCbs.create') }}
                        </a>
                    @endif
                </div>
                <div class="card-title">{{ trans('privateCbs.list') }}</div>

            </div> --}}



           <table id="votes_list" class="table table-hover table-striped dataTable no-footer table-responsive">
               <thead>
                    <tr>
                        <th><input type="checkbox" id="checkAll"/></th>
                        <th>{{ trans('privateCbs.vote_id') }}</th>
                        @if(Session::get('user_role') == 'admin')
                            <th>{{ trans('privateCbs.user_id') }}</th>
                            <th>{{ trans('privateCbs.user_key') }}</th>
                            <th>{{ trans('privateCbs.user_name') }}</th>
                        @endif
                        <th>{{ trans('privateCbs.value') }}</th>
                        @if(Session::get('user_role') == 'admin')
                            <th>{{ trans('privateCbs.topic_key') }}</th>
                            <th>{{ trans('privateCbs.topic_name') }}</th>
                        @endif
                        <th>{{ trans('privateCbs.created_at') }}</th>
                        <th>{{ trans('privateCbs.deleted_at') }}</th>
                        <th>{{ trans('privateCbs.submitted') }}</th>
                        <th>{{ trans('privateCbs.source') }}</th>
                    </tr>
                </thead>
            </table>

            @if(Session::get('user_role') == 'admin')
            <div id="deleteModal" class="btn btn-flat btn-submit" style="margin-top:20px">
                <i class="fa fa-remove" aria-hidden="true"></i>
                {{ trans('privateCbs.delete') }}
            </div>
            @endif
            @if(Session::get('user_role') == 'admin')
            <div id="submitModal" class="btn btn-flat btn-submit" style="margin-top:20px">
                {{ trans('privateCbs.submit_votes') }}
            </div>
            <div id="unsubmitModal" class="btn btn-flat btn-submit" style="margin-top:20px">
                {{ trans('privateCbs.unsubmit_votes') }}
            </div>
            @endif
        </div>
    </div>

    <div class="modal fade" id="modalDelete" role="dialog" aria-labelledby="modalDelete">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="card-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title"> {{trans('privateCbs.delete')}}</h4>
                </div>
                <div class="modal-body">
                    <p>{{trans('privateCbs.are_you_sure_you_want_to_delete_these_votes')}}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-flat btn-preview" id="deleteVote"> {{trans('privateCbs.delete')}}</button>
                    <button type="button" class="btn empatia" data-dismiss="modal">{{ trans('privateCbs.cancel') }}</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalSubmit" role="dialog" aria-labelledby="modalSubmit">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="card-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title"> {{trans('privateCbs.submit_votes')}}</h4>
                </div>
                <div class="modal-body">
                    <p>{{trans('privateCbs.are_you_sure_you_want_to_submit_these_votes')}}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-flat btn-preview" id="submitVote"> {{trans('privateCbs.submit')}}</button>
                    <button type="button" class="btn empatia" data-dismiss="modal">{{ trans('privateCbs.cancel') }}</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalUnsubmit" role="dialog" aria-labelledby="modalUnsubmit">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="card-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title"> {{trans('privateCbs.unsubmit_votes')}}</h4>
                </div>
                <div class="modal-body">
                    <p>{{trans('privateCbs.are_you_sure_you_want_to_unsubmit_these_votes')}}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-flat btn-preview" id="unsubmitVote"> {{trans('privateCbs.unsubmit')}}</button>
                    <button type="button" class="btn empatia" data-dismiss="modal">{{ trans('privateCbs.cancel') }}</button>
                </div>
            </div>
        </div>
    </div>


@endsection

@section('scripts')
    <script>
        
        $('#checkAll').click(function () {
            var teste = $('input:checkbox').prop('checked', this.checked);
        });

        $("#deleteModal").click(function () {
            $("#modalDelete").modal('show');
        });

        $("#deleteVote").click(function() {
            $("#modalDelete").modal('hide');

            var type = [];
            var cbKey = [];
            var voteKey = [];
            var voteId = [];

            $('.votes_id:checked').each( function(i, obj){
                type.push(($(this).attr('cbType')));
                cbKey.push(($(this).attr('cbKey')));
                voteKey.push(($(this).attr('voteKey')));
                voteId.push(($(this).attr('voteId')));
            });

            $.ajax({
                type: "delete",
                url: '{{action("CbsVoteController@deleteUserVote", [0, 0,0,0]) }}',
                data: {
                    "_token"   : "{{ csrf_token() }}",
                    "type"     :"{{$type}}",
                    "cbKey"    :"{{$cbKey}}",
                    "voteKey"  :"{{$voteKey}}",
                    "voteId"   :voteId,
                    "multipleVotes" : 1,
                },
                success: function (response) {
                    location.reload();
                },
                error: function (response) {

                }
            });
        });

        $("#submitModal").click(function () {
            $("#modalSubmit").modal('show');
        });

        $("#submitVote").click(function() {
            $("#modalSubmit").modal('hide');

            var type = [];
            var cbKey = [];
            var voteKey = [];
            var voteId = [];

            $('.votes_id:checked').each( function(i, obj){
                type.push(($(this).attr('cbType')));
                cbKey.push(($(this).attr('cbKey')));
                voteKey.push(($(this).attr('voteKey')));
                voteId.push(($(this).attr('voteId')));
            });

            $.ajax({
                type: "post",
                url: '{{action("CbsVoteController@submitUserVote", [0, 0,0,0]) }}',
                data: {
                    "_token"   : "{{ csrf_token() }}",
                    "type"     :"{{$type}}",
                    "cbKey"    :"{{$cbKey}}",
                    "voteKey"  :"{{$voteKey}}",
                    "voteId"   :voteId,
                    "submit" : "submit",
                },
                success: function (response) {
                    location.reload();
                },
                error: function (response) {

                }
            });
        });

        $("#unsubmitModal").click(function () {
            $("#modalUnsubmit").modal('show');
        });

        $("#unsubmitVote").click(function() {
            $("#modalUnsubmit").modal('hide');

            var type = [];
            var cbKey = [];
            var voteKey = [];
            var voteId = [];

            $('.votes_id:checked').each( function(i, obj){
                type.push(($(this).attr('cbType')));
                cbKey.push(($(this).attr('cbKey')));
                voteKey.push(($(this).attr('voteKey')));
                voteId.push(($(this).attr('voteId')));
            });

            $.ajax({
                type: "post",
                url: '{{action("CbsVoteController@submitUserVote", [0, 0,0,0]) }}',
                data: {
                    "_token"   : "{{ csrf_token() }}",
                    "type"     :"{{$type}}",
                    "cbKey"    :"{{$cbKey}}",
                    "voteKey"  :"{{$voteKey}}",
                    "voteId"   :voteId,
                    "submit" : "unsubmit",
                },
                success: function (response) {
                    location.reload();
                },
                error: function (response) {

                }
            });
        });

        function reloadDataTable() {
            $('#votes_list').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },
                processing: true,
                serverSide: true,
                bDestroy: true,
                ajax: {
                    url: '{!! action('CbsVoteController@getIndexTableVoteList',['type'=>$type,'cbKey'=>$cbKey,'voteKey'=>$voteKey]) !!}',
                    type:"get",
                    data:function(d) {
                        d.userId = $("#userId").val();
                        d.eventKey = $("#voteKey").val();
                        d.deleted = $("#deleted").is(":checked") ? 1 : 0,
                        d.submitted = $("#submitted").is(":checked") ? 1 : 0,
                        d.sources = $("#sources").select2().val();
                    }
                },
                columns: [
                    { data: 'select_votes', name: 'select_votes', searchable: false, orderable: false, width: "5px"},
                    { data: 'id', name: 'id', searchable: true, orderable: true},
                    @if(Session::get('user_role') == 'admin')
                    { data: 'user_id', name: 'user_id', searchable: false, orderable: false},
                    { data: 'user_key', name: 'user_key', searchable: true, orderable: true },
                    { data: 'user_name', name: 'user_name', searchable: true, orderable: true },
                    @endif
                    { data: 'value', name: 'value' },
                    @if(Session::get('user_role') == 'admin' )
                    { data: 'vote_key', name: 'vote_key', searchable: true, orderable: true },
                    { data: 'topic_name', name: 'topic_name', searchable: true, orderable: true },
                    @endif
                    { data: 'created_at', name: 'created_at', searchable: false, orderable: true },
                    { data: 'deleted_at', name: 'deleted_at', searchable: false, orderable: true },
                    { data: 'submitted', name: 'submitted' },
                    { data: 'source', name: 'source', searchable: true, orderable: true },
                    @if(Session::get('user_role') == 'admin')
                    { data: 'action', name: 'action', searchable: false, orderable: false, width: "50px" },
                    @endif
                ],
                order: [[ 1, 'asc' ]]
            });
        }

        $(document).ready(function() {
            reloadDataTable();
        });

    </script>
    
@endsection
