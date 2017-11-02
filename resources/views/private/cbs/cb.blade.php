@extends('private._private.index')

@section('content')


    <div class="row">
        <div class="col-12">
            @include('private.cbs.tabs')
        </div>
    </div>

    {{--<div class="{{ONE::actionType('cbs') == 'show' ? 'col-md-9' : 'col-md-12'}}">--}}

    <!-- Form -->
        @php
        $form = ONE::form('cbs', trans('privateCbs.details'), 'cb', $type)
            ->settings(["model" => isset($cb) ? $cb : null], ["options" => ['cb' =>  $type, ONE::actionType('cbs')]])
            ->show('CbsController@edit', 'CbsController@delete', ['type' => isset($type)? $type : null,'cbKey' => isset($cb) ? $cb->cb_key : null], null, ['type' => isset($type)? $type : null,'id' => isset($cb) ? $cb->cb_key : null])
            ->create('CbsController@store', 'CbsController@index', ['type' => isset($type)?$type:null,'id' => isset($cb) ? $cb->cb_key : null])
            ->edit('CbsController@update', 'CbsController@show', ['type' => isset($type)? $type : null,'id' => isset($cb) ? $cb->cb_key : null])
            ->open();
        @endphp

        @if(ONE::verifyUserPermissionsCreate('cb', 'pad_template'))
            <button type="button" class="btn btn-flat btn-success btn-sm pull-left" data-toggle="modal" data-target="#createCbsTemplateModal" style="margin-bottom: 10px">{{ trans('privateCbs.create_template') }}</button>
        @endif

        <div class="">
            <a class="btn btn-flat btn-preview pull-right" href="{{action("PublicCbsController@show",['cbKey'=>$cb->cb_key,'type'=>$type])}}" style="margin-bottom: 10px" target="_blank">
                <i class="fa fa-eye"></i>
                {{ trans('privateCbs.preview') }}
            </a>
        </div>
        <div class="">
            <a class="btn btn-flat empatia pull-right" href="{{action("CbsController@duplicate", ['cbKey'=>$cb->cb_key,'type'=>$type])}}" style="margin-bottom: 10px; margin-right: 10px" target="_blank">
                <i class="fa fa-files-o"></i>
                {{ trans('privateCbs.new_from_this') }}
            </a>
        </div>
        @if($hasTechnicalAnalysis??false)
            <div class="">
                <a class="btn btn-warning pull-right" href="{{action("CbsController@publishTechnicalAnalysisForm", ['type'=>$type,'cbKey'=>$cb->cb_key])}}" style="margin-bottom: 10px; margin-right: 10px">
                    <i class="fa fa-files-o"></i>
                    {{ trans('privateCbs.publish_technical_analysis_result') }}
                </a>
            </div>
        @endif
        <br><br>
    <!-- CB Details -->
        {!! Form::oneText('title', array("name"=>trans('privateCbs.title'),"description"=>trans('privateCbs.titleDescription')), isset($cb) ? $cb->title : null, ['class' => 'form-control', 'id' => 'title', 'required' => 'required']) !!}
        {!! Form::oneText('description',  array("name"=>trans('privateCbs.description'),"description"=>trans('privateCbs.descriptionDescription')), isset($cb) ? $cb->contents : null, ['class' => 'form-control', 'id' => 'description']) !!}
        {!! Form::oneText('tag',  array("name"=>trans('privateCbs.tag'),"description"=>trans('privateCbs.descriptionTag')), isset($cb) ? $cb->tag : null, ['class' => 'form-control', 'id' => 'tag']) !!}
        {!! Form::oneDate('start_date', array("name"=>trans('privateCbs.start_date'),"description"=>trans('privateCbs.startDateDescription')), isset($cb) ? $cb->start_date : date('Y-m-d'), ['class' => 'form-control oneDatePicker', 'id' => 'start_date', 'required' => 'required']) !!}
        {!! Form::oneDate('end_date',array("name"=>trans('privateCbs.end_date'),"description"=>trans('privateCbs.endDateDescription')), isset($cb) && $cb->end_date!=null ? $cb->end_date  : '', ['class' => 'form-control oneDatePicker', 'id' => 'end_date']) !!}
        {!! Form::hidden('cb_key', isset($cb) ? $cb->cb_key : 0, ['id' => 'cb_key']) !!}
        {!! Form::hidden('parent_cb_id', isset($cb) ? $cb->parent_cb_id : 0, ['id' => 'parent_cb_id']) !!}
        <input type="hidden" name="configurations" value="@php echo serialize($cbConfigurations); @endphp">

        @if(ONE::actionType('cbs') == 'show')

            <br>
            {{--Statistics--}}
            <div class="row">
                <div class="col-xl-3 col-lg-4 col-sm-6 col-12">
                    <div class="info-box">
                        <span class="info-box-icon bg-blue"><i class="fa fa-file-text-o"></i></span>
                        <div class="info-box-content">
                            {{ trans('privateCbs.total_topics') }}
                            <span class="info-box-number ideas"> {{$cb->statistics->topics ?? 0}}</span>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-lg-4 col-sm-6 col-12">
                    <div class="info-box">
                        <span class="info-box-icon bg-green"><i class="glyphicon glyphicon-user"></i></span>
                        <div class="info-box-content">
                            {{ trans('privateCbs.user_participants') }}
                            <span class="info-box-number logged_users">{{$cb->statistics->user_participants ?? 0}}</span>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-sm-6 col-12">
                    <div class="info-box">
                        <span class="info-box-icon bg-yellow"><i class="fa fa-comments"></i></span>
                        <div class="info-box-content">
                            {{ trans('privateCbs.total_comments') }}
                            <span class="info-box-number comments">{{$cb->statistics->posts ?? 0}}</span>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Topics List / create  -->


            <div class="row">
                <div class="col-12">
                    <!-- Parameters List / Create -->

                </div>
                <div class="col-12">
                    <!-- Votes List / Create -->

                </div>
            </div>
            @php $type= isset($type)?$type:null; @endphp
            <a href="{{action('CbsController@indexManager','typeFilter='.$type)}}" class="btn btn-flat empatia"><i class="fa fa-arrow-left"></i> Voltar</a>
        @endif

        {!! $form->make() !!}

    @if(ONE::actionType('cbs') == 'show')
        <!-- Moderators List -->
        <div class="col-md-3">

            <!--/.box -->
        </div>

    @endif

    @if(ONE::actionType('cbs') == 'show')

        <!-- status history modal -->
        <div class="modal fade" tabindex="-1" role="dialog" id="createCbsTemplateModal" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="card-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">{{trans('privateCbs.create_template')}}</h4>
                    </div>
                    <div class="modal-body" style="min-height: 10vh;">

                        <div id="create_template">
                            <label for="templateName">{{trans('privateCbs.template_name')}}</label>
                            <input type="text" id="templateName" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" onclick="storeTemplate()" class="btn btn-success" required>{{trans("privateCbs.save")}}</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{trans("privateCbs.cancel")}}</button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->

        <!-- Add Moderator (Initial Hidden / shows on click privateCbs.addModerator) -->



        <!-- update status modal -->
        <div class="modal fade" tabindex="-1" role="dialog" id="updateStatusModal" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="card-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">{{trans("privateCbs.update_status")}}</h4>
                    </div>
                    <div class="modal-body">
                        <div class="card flat">
                            {!! Form::hidden('topicKeyStatus','', ['id' => 'topicKeyStatus']) !!}
                            <div class="card-header">{{trans('privateCbs.select_option')}}</div>
                            <div class="card-body">
                                <div class="form-group ">
                                    <label for="status_type_code">{{trans('privateCbs.status_types')}}</label>
                                    <select id="status_type_code" class="form-control" name="status_type_code">
                                        <option selected="selected" value="">{{trans('privateCbs.select_value')}}</option>
                                        @foreach($statusTypes as $key => $statusType)
                                            <option value="{{$key}}">{{$statusType}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="contentStatusComment">{{trans('privateCbs.private_comment')}}</label>
                                    <textarea class="form-control" rows="5" id="contentStatusComment" name="contentStatusComment" style="resize: none;"></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="contentStatusPublicComment">{{trans('privateCbs.public_comment')}}</label>
                                    <textarea class="form-control" rows="5" id="contentStatusPublicComment" name="contentStatusPublicComment" style="resize: none;"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" id="closeUpdateStatus">{{trans("privateCbs.close")}}</button>
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <button type="button" class="btn btn-primary" id="updateStatus">{{trans("privateCbs.save_changes")}}</button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->


        <!-- status history modal -->
        <div class="modal fade" tabindex="-1" role="dialog" id="statusHistoryModal" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="card-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">{{trans('privateQuestionOption.status_history')}}</h4>
                    </div>
                    <div class="modal-body" style="overflow-y: scroll;max-height: 50vh;">
                        <div id="statusHistory">

                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{trans("privateCbs.close")}}</button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->

    @endif

@endsection

@section('scripts')
    <script>
        $(function () {
            $('.btn-group-vertical').prop('disabled', true);
            $('.btn-group-vertical').css('pointer-events', 'none');
        });

        toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": false,
            "progressBar": false,
            "positionClass": "toast-bottom-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "0",
            "hideDuration": "0",
            "timeOut": "0",
            "extendedTimeOut": "0",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };

        //function to get status history
        function showStatusHistory(topicKey){
            $.ajax({
                method: 'POST', // Type of response and matches what we said in the route
                url: '{{action("TopicController@statusHistory")}}', // This is the url we gave in the route
                data: {
                    topicKey: topicKey
                }, // a JSON object to send back
                success: function (response) { // What to do if we succeed
                    if(response != 'false'){
                        $('#statusHistory').html(response);
                        $('#statusHistoryModal').modal('show');
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                }
            });
        }

        function updateStatus(topicKey){
            $('#topicKeyStatus').val(topicKey);
            $('#updateStatusModal').modal('show');
        }

        $('#updateStatusModal').on('show.bs.modal', function (event) {
            $('#updateStatus').off();
            $('#updateStatus').on('click', function (evt) {
                var allVals = {};
                var isValid = true;

                //get inputs to update status
                allVals['topicKey'] = $('#topicKeyStatus').val();
                $('#updateStatusModal input:text').each(function () {
                    if($(this).val().length > 0){
                        allVals[$(this).attr('name')] = $(this).val();
                    }
                });
                $('#updateStatusModal textarea').each(function () {
                    if($(this).val().length > 0){
                        allVals[$(this).attr('name')] = $(this).val();
                    }
                });
                $('#updateStatusModal select').each(function () {
                    if($(this).val().length > 0){
                        $(this).closest('.form-group').removeClass('has-error');
                        allVals[$(this).attr('name')] = $(this).val();
                    }else{
                        $(this).closest('.form-group').addClass('has-error');
                        isValid = false;
                    }
                });



                //all values ok to update
                if (isValid) {
                    $('#updateStatusModal input:text').each(function () {
                        $(this).val('');
                    });
                    $('#updateStatusModal textarea').each(function () {
                        $(this).val('');
                    });
                    $('#updateStatusModal select').each(function () {
                        $(this).closest('.form-group').removeClass('has-error');
                        $(this).val('');
                    });
                    $.ajax({
                        method: 'POST', // Type of response and matches what we said in the route
                        url: "{{action('TopicController@updateStatus',['type'=> $type,'cbKey'=>$cb->cb_key])}}", // This is the url we gave in the route
                        data: allVals, // a JSON object to send back
                        success: function (response) { // What to do if we succeed

                            if (response != 'false') {
                                window.location.href = response;
                                toastr.success('{{ trans('privateCbs.update_topic_status_ok') }}', '', {timeOut: 3000,positionClass: "toast-bottom-right"});
                            }
                            $('#updateStatusModal').modal('hide');
                        },
                        error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                            $('#updateStatusModal').modal('hide');
                            toastr.error('{{ trans('privateCbs.error_updating_state_or_sending_email_to_user') }}', '', {timeOut: 3000,positionClass: "toast-bottom-right"});

                        }
                    });
                }
            });
            //clear inputs and close update status modal
            $('#closeUpdateStatus').on('click', function (evt) {
                $('#updateStatusModal input:text').each(function () {
                    $(this).val('');
                });
                $('#updateStatusModal textarea').each(function () {
                    $(this).val('');
                });
                $('#updateStatusModal select').each(function () {
                    $(this).val('');
                });

                $('#updateStatusModal').modal('hide');
            });
            {{--{!! session()->get('LANG_CODE').'json' !!}--}}
        });

        function storeTemplate(){
            if($("#templateName").val() != ""){
                $('#createCbsTemplateModal').modal('hide');
                $.ajax({
                    'url': '{{ action('CbsController@storeCbTemplate', ['type' => isset($type)? $type : null,'cbKey' => isset($cb) ? $cb->cb_key : null]) }}',
                    'data': {'templateName': $("#templateName").val()},
                    'method': 'post',
                    'dataType': 'json',
                    error: function(){
                        console.log("error");
                    },
                    complete: function(){

                    },
                    success: function(response){
                        toastr.success('{!! trans('privateCbs.cb_template_stored') !!}');
                    }
                })
            }else{
                toastr.error("{!! preg_replace( "/\r|\n/", "", htmlentities(trans("privateCbs.name_required_on_modal"),ENT_QUOTES)) !!} #1!");
                return false;
            }
        }


    </script>
@endsection

