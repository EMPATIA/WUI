@extends('private._private.index')

@section('content')


    <div class="row">
        @if(ONE::actionType('ideas') == 'show')
            <div class="col-md-9">

                @else
                    <div class="col-md-12">
                        @endif
                        @php $form = ONE::form('ideas')
                                ->settings(["model" => isset($idea) ? $idea : null])
                                ->show('IdeasController@edit', 'IdeasController@delete', ['id' => isset($idea) ? $idea->id : null], 'IdeasController@index', ['id' => isset($idea) ? $idea->id : null])
                                ->create('IdeasController@store', 'IdeasController@index', ['id' => isset($idea) ? $idea->id : null])
                                ->edit('IdeasController@update', 'IdeasController@show', ['id' => isset($idea) ? $idea->id : null])
                                ->open();
                        @endphp

                        {!! Form::oneText('title', trans('form.title'), isset($idea) ? $idea->title : null, ['class' => 'form-control', 'id' => 'title', 'required' => 'required']) !!}
                        {!! Form::oneText('contents', trans('form.description'), isset($idea) ? $idea->contents : null, ['class' => 'form-control', 'id' => 'contents']) !!}

                        {!! Form::hidden('cb_id', isset($idea) ? $idea->id : 0, ['id' => 'cb_id']) !!}


                        <div class="card flat">
                            <div class="card-header">Configurations</div>
                            <div class="card-body" id="btnGroupOne">
                                @foreach($configurations as $configuration)

                                    <div class="col-md-6" style="padding-left: 0px;">
                                        <div class="card flat">
                                            <div class="card-header">{{$configuration->title}}</div>
                                            <div class="card-body">
                                                <div class="btn-group-vertical" data-toggle="buttons"
                                                     style="width: 100%;">
                                                    @foreach($configuration->configurations as $option)
                                                        <label class="btn btn-secondary btn-flat {{in_array($option->id, (isset($ideaConfigurations) ? $ideaConfigurations : [])) ?  'active': ''}}">
                                                            <input type="checkbox" autocomplete="off" class="active"
                                                                   name="configuration_{{$option->id}}" {{in_array($option->id, (isset($ideaConfigurations) ? $ideaConfigurations : [])) ?  'checked': ''}}> {{$option->title}}
                                                        </label>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                        </div>
                        @if(ONE::actionType('ideas') == 'show')
                        <div class="card flat">
                            <div class="card-header">Parameters</div>
                            <div class="box-body">
                                        <table id="parameters_list" class="table table-hover table-striped dataTable no-footer table-responsive">
                                            <thead>
                                            <tr>
                                                <th>{{ trans('parameter.id') }}</th>
                                                <th>{{ trans('parameter.title') }}</th>
                                                <th>{!! ONE::actionButtons($idea->id, ['create' => 'IdeaParametersController@create']) !!}</th>
                                            </tr>
                                            </thead>
                                        </table>
                            </div>
                        </div>
                        @endif

                        @if(ONE::actionType('ideas') == 'show')
                            <div class="card flat">
                                <div class="card-header">Votes</div>
                                <div class="box-body">
                                    <table id="votes_list" class="table table-hover table-striped dataTable no-footer table-responsive">
                                        <thead>
                                        <tr>
                                            <th>{{ trans('voteEvent.key') }}</th>
                                            <th>{{ trans('method.title') }}</th>
                                            <th>{!! ONE::actionButtons($idea->id, ['create' => 'IdeaVoteController@create']) !!}</th>
                                        </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        @endif

                        {!! $form->make() !!}
                    </div>

                    @if(ONE::actionType('ideas') == 'show')
                        <div class="col-md-3">
                            <!-- USERS LIST -->
                            <div class="box box-primary">
                                <div class="box-header">
                                    <h3 class="box-title">Moderators</h3>

                                    <div class="box-tools pull-right" style="top: 13px;">
                                        <span class="badge badge-danger">{{count(isset($moderators)?$moderators:[])}}
                                            Moderators</span>
                                    </div>
                                </div>

                                <!-- /.box-header -->
                                <div class="box-body no-padding">

                                    @foreach((isset($moderators)?$moderators:[]) as $moderator)
                                        <div class="user-panel">
                                            <div class="image" style="float: left">
                                                @if($moderator['photo_id'] > 0)
                                                    <img src="https://empatia-test.onesource.pt:5005/file/download/{{$moderator['photo_id']}}/{{$moderator['photo_code']}}/1"
                                                         class="rounded-circle" alt="User Image">
                                                @else
                                                    <img src="https://empatia-test.onesource.pt:5005/file/download/193/Sqde8hUVIhfvthfxu6yD/1"
                                                         class="rounded-circle" alt="User Image">
                                                @endif
                                            </div>
                                            <div style="padding: 5px; margin-left: 60px;">
                                                <b>{{$moderator['name']}}</b><br>
                                                <small>Added at: 2016-02-15</small>
                                            </div>
                                            <div style="position: absolute; right: 10px; top: 10px">
                                                <a href="javascript:oneDelete('{!! action('IdeasController@deleteModeratorConfirm', ['cbId'=> $idea->id, 'id' => $moderator['user_key']]) !!}')">
                                                    <i style="color:red;" class="fa fa-remove"></i>
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <!-- /.box-body -->
                                <div class="box-footer text-center">
                                    <a href="" class="uppercase" data-toggle="modal" data-target="#managersModal">Add
                                        Moderator</a>
                                </div>
                                <!-- /.box-footer -->
                            </div>
                            <!--/.box -->
                        </div>
                    @endif
            </div>
            @endsection

            @if(ONE::actionType('ideas') == 'show')

                <div class="modal fade" tabindex="-1" role="dialog" id="managersModal">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="card-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                            aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title">Add Moderators</h4>
                            </div>
                            <div class="modal-body">

                                <div class="row">
                                    <div class="col-md-12">
                                        <!-- Custom Tabs -->
                                        <div id="tab_1">

                                        </div>

                                        <!-- /.tab-pane -->
                                    </div>
                                    <!-- nav-tabs-custom -->
                                </div>
                                <!-- /.col -->
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <button type="button" class="btn btn-primary" id="buttonSubmit">Save changes</button>
                            </div>
                        </div>
                        <!-- /.modal-content -->
                    </div>
                    <!-- /.modal-dialog -->
                </div><!-- /.modal -->
    </div>

@section('scripts')
    <script>
        $(function () {
            $('#btnGroupOne').prop('disabled', false);
            $('#btnGroupOne').css('pointer-events', 'none');
            $('.checkbox-group').css('pointer-events', 'none');
        });

        $('#managersModal').on('show.bs.modal', function (event) {
            $.get('{{ URL::action('IdeasController@allUsers', $idea->id)}}',
                    {
                        _token: "{{ csrf_token() }}",
                    },
                    function (data) {
                        // console.log('data '+data);
                    })
                    .done(function ($result) {
                        $("#tab_2").html($result);
                    })
                    .fail(function () {
                    })

                    .always(function () {
                    });


            $.get('{{ URL::action('IdeasController@allManagers', $idea->id)}}',
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
                $('[type=checkbox]:checked').each(function () {
                    allVals.push($(this).val());
                });

                if (allVals.length > 0) {
                    $.ajax({
                        method: 'POST', // Type of response and matches what we said in the route
                        url: "{{action('IdeasController@addModerator')}}", // This is the url we gave in the route
                        data: {
                            idCb: $('#cb_id').val(),
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

        $(function () {
            $('#parameters_list').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },
                processing: true,
                serverSide: true,
                ajax: '{!! action('IdeaParametersController@getIndexTableParameters',['cbId'=>$idea->id]) !!}',
                columns: [
                    { data: 'id', name: 'id', width: "20px" },
                    { data: 'title', name: 'title' },
                    { data: 'action', name: 'action', searchable: false, orderable: false, width: "30px" },
                ],
                order: [['1', 'asc']]
            });
            $('#votes_list').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },
                processing: true,
                serverSide: true,
                ajax: '{!! action('IdeaVoteController@getIndexTableVote',['cbId'=>$idea->id]) !!}',
                columns: [
                    { data: 'voteKey', name: 'voteKey', width: "20px" },
                    { data: 'name', name: 'name' },
                    { data: 'methodName', name: 'methodName'},
                    { data: 'action', name: 'action', searchable: false, orderable: false, width: "30px" },
                ],
                order: [['1', 'asc']]
            });

        });

    </script>


@endsection

@endif
