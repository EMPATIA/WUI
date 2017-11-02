@extends('private._private.index')

@section('content')


    <div class="row">
        @if(ONE::actionType('discussion') == 'show')
            <div class="col-md-9">
                @else
                    <div class="col-md-12">
                        @endif

                        <?php $form = ONE::form('discussion')
                                ->settings(["model" => isset($discussion) ? $discussion : null])
                                ->show('DiscussionController@edit', 'DiscussionController@delete', ['id' => isset($discussion) ? $discussion->id : null], 'DiscussionController@index', ['id' => isset($discussion) ? $discussion->id : null])
                                ->create('DiscussionController@store', 'DiscussionController@index', ['id' => isset($discussion) ? $discussion->id : null])
                                ->edit('DiscussionController@update', 'DiscussionController@show', ['id' => isset($discussion) ? $discussion->id : null])
                                ->open();
                        ?>

                        {!! Form::oneText('title', trans('privateDiscussion.title'), isset($discussion) ? $discussion->title : null, ['class' => 'form-control', 'id' => 'title', 'required' => 'required']) !!}
                        {!! Form::oneText('contents', trans('privateDiscussion.description'), isset($discussion) ? $discussion->contents : null, ['class' => 'form-control', 'id' => 'contents']) !!}

                        {!! Form::hidden('cb_id', isset($discussion) ? $discussion->id : 0, ['id' => 'cb_id']) !!}


                        <div class="card flat">
                            <div class="card-header">{{trans('privateDiscussion.configurations')}}</div>
                            <div class="card-block">
                                @foreach($configurations as $configuration)
                                    <div class="col-md-6" style="padding-left: 0px;">
                                        <div class="card flat">
                                            <div class="card-header">{{$configuration->title}}</div>
                                            <div class="card-block">
                                                <div class="btn-group-vertical" data-toggle="buttons"
                                                     style="width: 100%;">
                                                    @foreach($configuration->configurations as $option)
                                                        <label class="btn btn-secondary btn-flat {{in_array($option->id, (isset($discussionConfigurations) ? $discussionConfigurations : [])) ?  'active': ''}}">
                                                            <input type="checkbox" autocomplete="off" class="active"
                                                                   name="configuration_{{$option->id}}" {{in_array($option->id, (isset($discussionConfigurations) ? $discussionConfigurations : [])) ?  'checked': ''}}> {{$option->title}}
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
                                    <h3 class="box-title">{{trans('privateDiscussion.moderators')}}</h3>

                                    <div class="box-tools pull-right" style="top: 13px;">
                                        <span class="label label-danger">{{count(isset($moderators)?$moderators:[])}}
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
                                                <a href="javascript:oneDelete('{!! action('DiscussionController@deleteModeratorConfirm', ['cbId'=> $discussion->id, 'id' => $moderator['user_key']]) !!}')">
                                                    <i style="color:red;" class="fa fa-remove"></i>
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <!-- /.box-body -->
                                <div class="box-footer text-center">
                                    <a href="" class="uppercase" data-toggle="modal" data-target="#managersModal">{{trans('privateDiscussion.addModerator')}}</a>
                                </div>
                                <!-- /.box-footer -->
                            </div>
                            <!--/.box -->
                        </div>
                    @endif
            </div>
            @endsection

            @if(ONE::actionType('discussion') == 'show')

                <div class="modal fade" tabindex="-1" role="dialog" id="managersModal" >
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="card-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title">{{trans('privateDiscussion.addModerator')}}<</h4>
                            </div>
                            <div class="modal-body">

                                <div class="row">
                                    <div class="col-md-12">
                                        <!-- Custom Tabs -->
                                        <div class="nav-tabs-custom">
                                            <ul class="nav nav-tabs">
                                                <li class="nav-item"><a class="nav-link active" href="#tab_1" data-toggle="tab" aria-expanded="true">{{trans('privateDiscussion.moderator')}}</a></li>
                                                <li class="nav-item"><a class="nav-link" href="#tab_2" data-toggle="tab" aria-expanded="false">{{trans('privateDiscussion.users')}}</a></li>

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
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{trans('privateDiscussion.close')}}</button>
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <button type="button" class="btn btn-primary" id="buttonSubmit">{{trans('privateDiscussion.saveChanges')}}</button>
                            </div>
                        </div><!-- /.modal-content -->
                    </div><!-- /.modal-dialog -->
                </div><!-- /.modal -->

            </

        @section('scripts')
            <script>
                $(function () {
                    $('.btn-group-vertical').prop('disabled', true);
                    $('.btn-group-vertical').css('pointer-events', 'none');

                });

                $('#managersModal').on('show.bs.modal', function (event) {
                    $.get('{{ URL::action('DiscussionController@allUsers', $discussion->id)}}',
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


                    $.get('{{ URL::action('DiscussionController@allManagers', $discussion->id)}}',
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
                                url: "{{action('DiscussionController@addModerator')}}", // This is the url we gave in the route
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


            </script>
@endsection

@endif
