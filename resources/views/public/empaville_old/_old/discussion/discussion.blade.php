@extends('public._layouts.index')

@section('content')


    <div class="row">
        @if(ONE::actionType('discussion') == 'show')
            <div class="col-md-9">
                @else
                    <div class="col-md-12">
                        @endif

                        <?php $form = ONE::form('discussion')
                                ->settings(["model" => isset($discussion) ? $discussion : null,'id' =>isset($discussion) ? $discussion->id : null])
                                ->show('PublicDiscussionController@edit', 'PublicDiscussionController@delete', ['id' => isset($discussion) ? $discussion->id : null], 'PublicDiscussionController@index')
                                ->create('PublicDiscussionController@store', 'PublicDiscussionController@index', ['id' => isset($discussion) ? $discussion->id : null])
                                ->edit('PublicDiscussionController@update', 'PublicDiscussionController@show', ['id' => isset($discussion) ? $discussion->id : null])
                                ->open()

                        ?>
                        {!! Form::oneText('title', trans('discussion.title'), isset($discussion) ? $discussion->title : null, ['class' => 'form-control', 'id' => 'title']) !!}
                        {!! Form::oneTextArea('contents', trans('discussion.contents'), isset($discussion) ? $discussion->contents : null, ['class' => 'form-control', 'id' => 'contents', 'size' => '30x2', 'style' => 'resize: vertical']) !!}

                        {!! Form::hidden('cb_id', isset($discussion) ? $discussion->id : 0, ['id' => 'cb_id']) !!}


                        <div class="panel panel-default flat">
                            <div class="panel-heading">Configurations</div>
                            <div class="panel-body">
                                @foreach($configurations as $configuration)

                                    <div class="col-md-6" style="padding-left: 0px;">
                                        <div class="panel panel-default flat">
                                            <div class="panel-heading">{{$configuration->title}}</div>
                                            <div class="panel-body">
                                                <div class="btn-group-vertical" data-toggle="buttons" style="width: 100%;">
                                                    @foreach($configuration->configurations as $option)
                                                        <label class="btn btn-default btn-flat {{in_array($option->id, (isset($discussionConfigurations) ? $discussionConfigurations : [])) ?  'active': ''}}">
                                                            <input type="checkbox" autocomplete="off" class="active" name="configuration_{{$option->id}}" {{in_array($option->id, (isset($discussionConfigurations) ? $discussionConfigurations : [])) ?  'checked': ''}}> {{$option->title}}
                                                        </label>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        {!! $form->make() !!}
                    </div>

                    @if(ONE::actionType('discussion') == 'show')
                        <div class="col-md-3">
                            <!-- USERS LIST -->
                            <div class="box box-primary">
                                <div class="box-header with-border">
                                    <h3 class="box-title">Moderators</h3>

                                    <div class="box-tools pull-right" style="top: 13px;">
                                        <span class="label label-danger">{{count(isset($moderators)?$moderators:[])}} Moderators</span>
                                    </div>
                                </div>
                                <!-- /.box-header -->
                                <div class="box-body no-padding">

                                    @foreach((isset($moderators)?$moderators:[]) as $moderator)
                                        <div class="user-panel">
                                            <div class="image" style="float: left">
                                                @if($moderator['photo_id'] > 0)
                                                    <img src="https://empatia-test.onesource.pt:5005/file/download/{{$moderator['photo_id']}}/{{$moderator['photo_code']}}/1" class="img-circle" alt="User Image">
                                                @else
                                                    <img src="/images/icon-user-default-160x160.png" class="img-circle" alt="User Image">
                                                @endif
                                            </div>
                                            <div style="padding: 5px; margin-left: 60px;">
                                                <b>{{$moderator['name']}}</b><br>
                                                <small>Added at: 2016-02-15</small>
                                            </div>
                                            <div style="position: absolute; right: 10px; top: 10px">
                                                <a href="javascript:oneDelete('{!! action('PublicDiscussionController@deleteModeratorConfirm', ['cbId'=> $discussion->id, 'id' => $moderator['user_key']]) !!}')">
                                                    <i style="color:red;" class="fa fa-remove"></i>
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <!-- /.box-body -->
                                <div class="box-footer text-center">
                                    <a href="" class="uppercase" data-toggle="modal" data-target="#managersModal">Add Moderator</a>
                                </div>
                                <!-- /.box-footer -->
                            </div>
                            <!--/.box -->
                        </div>
                    @endif

            </div>
    </div>

@endsection



@if(ONE::actionType('discussion') == 'show')


    <div class="modal fade" tabindex="-1" role="dialog" id="managersModal" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Add Moderators</h4>
                </div>
                <div class="modal-body">

                    <div class="row">
                        <div class="col-md-12">
                            <!-- Custom Tabs -->
                            <div class="nav-tabs-custom">
                                <ul class="nav nav-tabs">
                                    <li class="active"><a href="#tab_1" data-toggle="tab" aria-expanded="true">Managers</a></li>
                                    <li class=""><a href="#tab_2" data-toggle="tab" aria-expanded="false">Users</a></li>

                                </ul>
                                <div class="tab-content" style="min-height: 100px">
                                    <div class="tab-pane active" id="tab_1">

                                    </div>
                                    <!-- /.tab-pane -->
                                    <div class="tab-pane" id="tab_2">

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
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <button type="button" class="btn btn-primary" id="buttonSubmit">Save changes</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->


@section('scripts')
    <script>
        $(function () {
            $('.btn-group-vertical').prop('disabled', true);
            $('.btn-group-vertical').css('pointer-events', 'none');

        });

        $('#managersModal').on('show.bs.modal', function (event) {
            $.get('{{ URL::action('PublicDiscussionController@allUsers', $discussion->id)}}',
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



            $.get('{{ URL::action('PublicDiscussionController@allManagers', $discussion->id)}}',
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
                $('[type=checkbox]:checked').each(function() {
                    allVals.push($(this).val());
                });

                if(allVals.length > 0){
                    $.ajax({
                        method: 'POST', // Type of response and matches what we said in the route
                        url: '/public/discussion/addModerator', // This is the url we gave in the route
                        data: {idCb: $('#cb_id').val(), moderatorsKey: JSON.stringify(allVals), _token: $('input[name=_token]').val()}, // a JSON object to send back
                        success: function (response) { // What to do if we succeed
                            if(response != 'false'){

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
                }else{
                    $('#managersModal').modal('hide');
                }
            });
        });



    </script>
@endsection

@endif