@extends('private._private.index')
<style>
    .oneSwitch {
        position: relative; width: 60px;
        -webkit-user-select:none; -moz-user-select:none; -ms-user-select: none;
    }
    .oneSwitch-checkbox {
        display: none;
    }
    .oneSwitch-label {
        display: block; overflow: hidden; cursor: pointer;
        border: 2px solid #999999; border-radius: 20px;
    }
    .oneSwitch-inner {
        display: block; width: 200%; margin-left: -100%;
        transition: margin 0.3s ease-in 0s;
    }
    .oneSwitch-inner:before, .oneSwitch-inner:after {
        display: block; float: left; width: 50%; height: 20px; padding: 0; line-height: 20px;
        font-size: 14px; color: white; font-family: Trebuchet, Arial, sans-serif; font-weight: bold;
        box-sizing: border-box;
    }
    .oneSwitch-inner:before {
        content: "";
        padding-left: 10px;
        background-color: #2EA7DE; color: #FFFFFF;
    }
    .oneSwitch-inner:after {
        content: "";
        padding-right: 10px;
        background-color: #EEEEEE; color: #999999;
        text-align: right;
    }
    .oneSwitch-switch {
        display: block; width: 18px; margin: 1px;
        background: #FFFFFF;
        position: absolute; top: 0; bottom: 0;
        right: 36px;
        border: 2px solid #999999; border-radius: 20px;
        transition: all 0.3s ease-in 0s;
    }
    .oneSwitch-checkbox:checked + .oneSwitch-label .oneSwitch-inner {
        margin-left: 0;
    }
    .oneSwitch-checkbox:checked + .oneSwitch-label .oneSwitch-switch {
        right: 0px;
    }

    .cooperatorDivItem:hover {
        background: #e8e8e8;
    }

    #users_list_filter label::content{
        display:none;
    }
</style>

@section('content')
    <div class="card flat topic-data-header" >
        <p><label for="contentStatusComment">{{trans('privateCbs.pad')}}</label> {{$cb->title}}</p>
        @if(!empty($cbAuthor))
        <p><label for="contentStatusComment">{{trans('privateCbs.author')}}</label>
            <a href="{{action('UsersController@show', ['userKey' => $cbAuthor->user_key, 'role' => $cbAuthor->role ?? null])}}">{{$cbAuthor->name}}</a>
        </p>
        @endif
        <p><label for="contentStatusComment">{{trans('privateCbs.start_date')}}</label>  {{$cb->start_date}}</p>
    </div>

    <div class="box box-primary margin-top-20">
        <div class="box-header margin-bottom-20">
            <h3 class="box-title">{{ trans('privateCbs.cooperators') }}</h3>
        </div>
        <div class="box-body">
            <div class="dataTables_wrapper dt-bootstrap no-footer">
                <table id="cooperators_list" class="table table-responsive table-hover table-striped">
                    <thead>
                    <tr>
                        <th>{{ trans('privateCbs.name') }}</th>
                        <th>{{trans('privateCbs.permissions')}}</th>
                        <th>
                            <button class="btn btn-flat btn-sm btn-create fa fa-plus" data-toggle="modal" data-target="#cooperatorModal"></button>
                        </th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" tabindex="-1" role="dialog" id="cooperatorModal" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="card-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">{{trans("privateCbs.add_cooperator")}}</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="tab-pane" id="tab">
                                <table id="users_list" class="table table-hover table-striped">
                                    <thead>
                                    <tr>
                                        <th style="width:10%;"></th>
                                        <th>{{ trans('user.name') }}</th>
                                    </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{trans("privateCbs.close")}}</button>
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <button type="button" class="btn btn-primary" id="buttonSubmit">{{trans("privateCbs.save_changes")}}</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

@endsection

@section('scripts')
    <script>

        $('#cooperatorModal').on('show.bs.modal', function (event) {

            if (!$.fn.DataTable.isDataTable('#users_list')) {
                $('#users_list').dataTable();
            }

            $('#buttonSubmit').on('click', function (evt) {
                $('#buttonSubmit').off();

                var allVals = [];
                var cbKeyVal = '{{$cbKey ?? null}}';
                var typeVal = '{{$type ?? null}}';

                $('#cooperatorModal input:checked').each(function () {
                    allVals.push($(this).val());
                });

                if (allVals.length > 0) {
                    $.ajax({
                        method: 'post',
                        url: "{{action('TopicController@addCooperator',['topicKey'=> $topicKey])}}",
                        data: {
                            cooperatorsKey: allVals,
                            cbKey: cbKeyVal,
                            type: typeVal
                        },
                        success: function (response) {
                            if (response == 'Ok') {
                                table.ajax.reload();
                            }
                            $('#cooperatorModal').modal('hide');
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            $('#cooperatorModal').modal('hide');
                        }
                    });
                } else {
                    $('#cooperatorModal').modal('hide');
                }
            });
        });

        table = $('#cooperators_list').DataTable({
            language: {
                url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
            },
            responsive: true,
            processing: true,
            serverSide: true,
            ajax: '{!!  URL::action('TopicController@showCooperatorsTable', ['type'=> $type,'cbKey' => $cb->cb_key, 'topicKey' => $topicKey]) !!}',
            columns: [
                { data: 'name', name: 'name', orderable: true, searchable: true},
                { data: 'permissions', name: 'permissions', orderable: false, searchable: false},
                { data: 'action', name: 'action', searchable: false, orderable: false, width: "30px" }
            ],
            order: [['0', 'desc']]
        });

        $('#users_list').DataTable({
            language: {
                url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
            },
            responsive: true,
            processing: true,
            serverSide: true,
            ajax: '{!!  URL::action('TopicController@entityUsers', ['type'=> $type,'cbKey' => $cb->cb_key, 'topicKey'=> $topicKey]) !!}',
            columns: [
                { data: 'cooperatorCheckbox', name: 'cooperatorCheckbox', searchable: false, orderable: false, width: "30px" },
                { data: 'name', name: 'name', orderable: true, searchable: true}
            ],
            order: [['1', 'desc']]
        });

        function toggleCooperatorItem(obj,name){
            if ($(obj).is(":checked")){
                html = "<div id='cooperatorDivItem_"+$(obj).val()+"' class='user-card cooperatorDivItem'>";
                html += "<div style='padding: 5px; margin-left: 60px;'>";
                html += "<b>"+name+"</b>";
                html += "</div>";
                html += "<div style='position: absolute; right: 10px; top: 10px'>";
                html += "<a href='javascript:uncheckCooperator(\""+$(obj).val()+"\")'><i style='color:red;' class='fa fa-remove'></i></a>";
                html += "</div>";
                html += "</div>";
            } else {
                $("#cooperatorDivItem_"+$(obj).val()).detach();
            }
        }

        function uncheckCooperator(userKey){
            $("#cooperatorCheckbox_"+userKey).prop( "checked", false );
            $("#cooperatorDivItem_"+userKey).detach();
        }

        function changePermissions(obj, userKey, topicKey){
            $.ajax({
                method: 'PUT',
                url: "{{action('TopicController@updateCooperatorPermission',['topicKey' => $topicKey])}}",
                data: {
                    permission: $(obj).val(),
                    userKey: userKey
                }
            });
        }

    </script>

@endsection