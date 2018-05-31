@extends('private._private.index')

@section('content')
    @include('private.cbs.tabs')

    <div class="card flat topic-data-header" >
        <p><label for="contentStatusComment" style="margin-left:5px; margin-top:5px;">{{trans('privateCbs.pad')}}</label>  {{$cb->title}}</p>
        @if(!empty($cbAuthor))
        <p><label for="contentStatusComment" style="margin-left:5px;">{{trans('privateCbs.author')}}</label>
            <a href="{{action('UsersController@show', ['userKey' => $cbAuthor->user_key, 'role' => $cbAuthor->role ?? null])}}">{{$cbAuthor->name}}</a>
        </p>
        @endif
        <p><label for="contentStatusComment" style="margin-left:5px; margin-bottom:5px;">{{trans('privateCbs.start_date')}}</label>  {{$cb->start_date}}</p>
    </div>
    <div class="box box-primary" style="margin-top: 25px">
        <div class="box-header" style="padding-left: 20px">
            <h3 class="box-title">{{ trans('privateCbs.moderators') }}</h3>
            <div class="box-tools pull-right" style="top: 13px;">
                            <span class="badge badge-danger">{{count(isset($moderators)?$moderators:[])}}
                                {{ trans('privateCbs.moderators') }}</span>
            </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <div class="margin-bottom-10">
                <a href="" data-toggle="modal" data-target="#managersModal" class="btn btn-flat empatia">{{trans("privateCbs.add_moderator")}}</a>
            </div>
            @foreach((isset($moderators)?$moderators:[]) as $moderator)
                <div class="user-panel">
                    <div class="image" style="float: left">
                        @if($moderator['photo_id'] > 0)
                            <img src="{{URL::action('FilesController@download',[$moderator['photo_id'], $moderator['photo_code']])}}/1"
                                 class="rounded-circle" alt="User Image">
                        @else
                            <img src="{{asset('images/icon-user-default-160x160.png')}}"
                                 class="rounded-circle" alt="User Image">
                        @endif
                    </div>
                    <div style="padding: 5px; margin-left: 60px;">
                        <b>{{$moderator['name']}}</b><br>
                        <small>{{ trans('privateCbs.addedAt') }}: {{$moderator['date_added']}}</small>
                    </div>
                    <div style="position: absolute; right: 10px; top: 10px">
                        <a href="javascript:oneDelete('{!! action('CbsController@deleteModeratorConfirm', ['type'=>$type,'cbKey'=> $cb->cb_key, 'id' => $moderator['user_key']]) !!}')">
                            <i style="color:red;" class="fa fa-remove"></i>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
        <!-- /.box-body -->

    <!-- /.box-footer -->

        @if(isset($step))
            <a class="btn btn-flat btn-preview pull-left" href="{{action('CbsController@create',['type'=>$type,'cbKey' => $cbKey, 'step' => $step])}}">
                {!! trans("privateCbs.back") !!}
            </a>
        @endif
    </div>

    <div class="modal fade" tabindex="-1" role="dialog" id="managersModal" >
        <div class="modal-dialog">
            <div class="modal-body">
                <div class="card">
                    <div class="card-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4>{{trans("privateCbs.add_moderator")}}</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <!-- Custom Tabs -->
                                <div role="tablist">
                                    <ul class="nav nav-tabs">
                                        <li class="nav-item"><a class="nav-link active" href="#tab_1" data-toggle="tab" aria-expanded="true">{{trans("privateCbs.moderator")}}</a></li>
                                        <li class="nav-item"><a class="nav-link" href="#tab_2" data-toggle="tab" aria-expanded="false">{{trans("privateCbs.users")}}</a></li>
                                    </ul>
                                    <div class="tab-content" style="min-height: 100px" role="tabpanel">
                                        <div class="tab-pane active default-padding" id="tab_1">

                                        </div>
                                        <!-- /.tab-pane -->
                                        <div class="tab-pane default-padding" id="tab_2" role="tabpanel">
                                            <table id="users_list" class="table table-hover table-striped dataTable no-footer table-responsive">
                                                <thead>
                                                <tr>
                                                    <th style="width:10%;"></th>
                                                    <th>{{ trans('user.name') }}</th>
                                                </tr>
                                                </thead>
                                            </table>
                                        </div>
                                        <!-- /.tab-pane -->
                                    </div>
                                    <!-- /.tab-content -->
                                </div>
                                <!-- nav-tabs-custom -->
                            </div>
                            <!-- /.col -->
                        </div>
                    </div>
                <div class="modal-footer">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <button type="button" class="btn btn-primary" id="buttonSubmit">{{trans("privateCbs.save_changes")}}</button>
                    <button type="button" class="btn btn-preview" data-dismiss="modal">{{trans("privateCbs.close")}}</button>
                </div>
            </div><!-- /.modal-content -->
            </div>
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

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
            background-color: #62A351; color: #FFFFFF;
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

        .moderatorDivItem:hover {
            background: #e8e8e8;
        }
    </style>

@endsection

@section('scripts')
    <script>

        $(function () {
            // Topics List
            $('#topics_list').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: '{!! action('TopicController@getIndexTable',['type'=>$type,'cbKey'=>$cb->cb_key]) !!}',
                columns: [
                    { data: 'id', name: 'id', width: "20px" },
                    { data: 'title', name: 'title' },
                    { data: 'action', name: 'action', searchable: false, orderable: false, width: "30px" },
                ],
                order: [['1', 'asc']]
            });

            // Topics List Status
            $('#topics_list_status').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: '{!! action('TopicController@getIndexTableStatus',['type'=>$type, 'cbKey'=>$cb->cb_key, 'hasFlags' => !empty($cb->flags)]) !!}',
                columns: [
                    { data: 'title', name: 'title', width: "20px" },
                    { data: 'created_at', name: 'created_at' },
//                    { data: 'created_by', name: 'created_by' },
                    { data: 'status', name: 'status' },
//                    { data: 'votes', name: 'votes' },
                    { data: 'action', name: 'action', searchable: false, orderable: false, width: "30px" }
                ],
                order: [['1', 'asc']]
            });
            // Parameters List


        });

        $('#managersModal').on('show.bs.modal', function (event) {
            {{--$.get('{{ URL::action('CbsController@allUsers', ['type'=> $type,'cbKey'=>$cb->cb_key])}}',--}}
                    {{--{--}}
                        {{--_token: "{{ csrf_token() }}",--}}
                    {{--},--}}
                    {{--function (data) {--}}
                        {{--// console.log('data '+data);--}}
                    {{--})--}}
                    {{--.done(function (result) {--}}
                        {{--console.log(result);--}}

                        {{--$("#tab_2").html(result);--}}
                    {{--})--}}
                    {{--.fail(function () {--}}
                    {{--})--}}

                    {{--.always(function () {--}}
                    {{--});--}}


                    $('#users_list').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },
                processing: false,
                serverSide: false,
                bDestroy: true,
                bInfo: false,
                ajax: '{!!  URL::action('CbsController@allUsers', ['type'=> $type,'cbKey' => !empty($cb) ? $cb->cb_key : "" ]) !!}',
                columns: [
                    { data: 'moderadorCheckbox', name: 'moderadorCheckbox', searchable: false, orderable: false, width: "30px" },
                    { data: 'name', name: 'name' }
                ],
                order: [['1', 'asc']]
            }).on( 'draw.dt', function () {
                $("#users_list_paginate").parent().attr("class","col-md-12");
            });




            $.get('{{ URL::action('CbsController@allManagers', ['type'=> $type,'cbKey'=>$cb->cb_key])}}',
                    {
                        _token: "{{ csrf_token() }}",
                    },
                    function (data) {
                        // console.log('data '+data);
                    })
                    .done(function ($result) {
                        $("#tab_1").html($result);
                    })
                    .fail(function (jqXHR, textStatus, errorThrown) {

                    });


            $('#buttonSubmit').on('click', function (evt) {
                $('#buttonSubmit').off();

                var allVals = [];
                $('#managersModal input:checked').each(function () {
                    allVals.push($(this).val());
                });

                if (allVals.length > 0) {
                    $.ajax({
                        method: 'POST', // Type of response and matches what we said in the route
                        @if(!empty($step))
                            url: "{{action('CbsController@addModerator',['type'=> $type,'cbKey'=>$cb->cb_key, "step" => $step])}}", // This is the url we gave in the route
                        @else
                            url: "{{action('CbsController@addModerator',['type'=> $type,'cbKey'=>$cb->cb_key])}}", // This is the url we gave in the route
                        @endif
                        data: {
                            cbKey: $('#cb_key').val(),
                            moderatorsKey: JSON.stringify(allVals),
                            _token: $('input[name=_token]').val()
                        }, // a JSON object to send back
                        success: function (response) { // What to do if we succeed
                            if (response != 'false') {

                                window.location.href = response;
                            }
                            $('#managersModal').modal('hide');
                        },
                        error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                            $('#managersModal').modal('hide');
                            console.log(JSON.stringify(jqXHR));
                            console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                        }
                    });
                } else {
                    $('#managersModal').modal('hide');
                }
            });
        });
    </script>
    <script>


        function toggleModeratorItem(obj,name,userImage){
            if ($(obj).is(":checked")){
                html = "<div id='moderatorDivItem_"+$(obj).val()+"' class='user-card moderatorDivItem'>";
                html += "<div class='image' style='float: left'>";
                html += "<img src='"+userImage+"' class='rounded-circle' alt='User Image' />";
                html += "</div>";
                html += "<div style='padding: 5px; margin-left: 60px;'>";
                html += "<b>"+name+"</b>";
                html += "</div>";
                html += "<div style='position: absolute; right: 10px; top: 10px'>";
                html += "<a href='javascript:uncheckModerator(\""+$(obj).val()+"\")'><i style='color:red;' class='fa fa-remove'></i></a>";
                html += "</div>";
                html += "</div>";
                $("#moderatorsGroup").append(html);
            } else {
                $("#moderatorDivItem_"+$(obj).val()).detach();
            }
        }

        function uncheckModerator(userKey){
            $("#moderatorCheckbox_"+userKey).prop( "checked", false );
            $("#moderatorDivItem_"+userKey).detach();
        }
    </script>

@endsection