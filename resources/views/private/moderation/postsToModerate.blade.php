@extends('private._private.index')

@section('header_styles')
    <style>
        .comments-modal{
            display: block !important;
        }
        /* Important part */
        .comments-modal-dialog{
            overflow-y: initial !important
        }
        .comments-modal-body{
            height: 250px;
            overflow-y: auto;
        }

        #commentsModal hr{
            display: block;
            height: 1px;
            border: 0;
            border-top: 1px solid #737173;
            margin: 1em 0;
            padding: 0;
        }
    </style>
    @endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="box box-info">
            <div class="box-header">
                <h3 class="box-title">{{ trans('private.posts_moderation') }}</h3>

                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <table class="table table-responsive no-margin" id="moderation_posts" style="margin-top: 0px !important;margin-bottom: 0px !important;">
                    <thead>
                    <tr>
                        <th>{{ trans('private.topic') }}</th>
                        <th>{{ trans('private.created_by') }}</th>
                        <th>{{ trans('private.content') }}</th>
                        <th>{{ trans('private.created_at') }}</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
            <!-- /.box-body -->
        </div>
    </div>
</div>

<!-- update status modal -->
<div class="modal fade" tabindex="-1" role="dialog" id="updateStatusModal" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="card-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">{{trans("private.update_status")}}</h4>
            </div>
            <div class="modal-body">
                <div class="card flat">
                    {!! Form::hidden('topicKeyStatus','', ['id' => 'topicKeyStatus']) !!}
                    <div class="card-header">{{trans('private.add_comments')}}</div>
                    <div class="card-body">

                        <input type="text" id="status_type_code" class="form-control hidden" name="status_type_code" value="">

                        <input id="cb_key_hidden" type="hidden" name="cb_key_hidden" value="">
                        <input id="type_hidden" type="hidden" name="type_hidden" value="">

                        <div class="form-group">
                            <label for="contentStatusComment">{{trans('private.private_comment')}}</label>
                            <textarea class="form-control" rows="5" id="contentStatusComment" name="contentStatusComment" style="resize: none;"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="contentStatusPublicComment">{{trans('private.public_comment')}}</label>
                            <textarea class="form-control" rows="5" id="contentStatusPublicComment" name="contentStatusPublicComment" style="resize: none;"></textarea>
                        </div>

                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="closeUpdateStatus">{{trans("private.close")}}</button>
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <button type="button" class="btn btn-primary" id="updateStatus">{{trans("private.save_changes")}}</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->



<!-- Update Password Modal -->
<div class="modal fade" tabindex="-1" role="" id="topicsCommentsModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="card">
                <div class="card-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4>
                        {{trans('privateModeration.comment_line')}}
                    </h4>
                </div>
                <div class="card-body" style="height: 550px; overflow-y: auto;  background-color: #f7f7f7;">
                    <div id="commentsModal"></div>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


@endsection

@section('scripts')
    <script>

        $( document ).ready(function() {

            //  LOAD TOPICS TO MODERATE DATATABLE
            datatablePostsToModerate();


            // remove horizontal scrollbar from datatables
            $('.table-responsive-container').css({
                "overflow-x": "hidden",
            });

            // ------------------- Handle update status modal

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

                    //all values ok to update
                    if (isValid) {
                        $('#updateStatusModal input:text').each(function () {
                            $(this).val('');
                        });
                        $('#updateStatusModal textarea').each(function () {
                            $(this).val('');
                        });

                        allVals.type =  $('#type_hidden').val();
                        allVals.cbKey = $('#cb_key_hidden').val();

                        $.ajax({
                            method: 'POST', // Type of response and matches what we said in the route
                            url: "{{action('TopicController@updateStatusTopic')}}", // This is the url we gave in the route
                            data: allVals, // a JSON object to send back
                            success: function (response) { // What to do if we succeed

                                if (response != 'false') {
                                    window.location.reload();
                                    toastr.success('{{ trans('private.update_topic_status_ok') }}', '', {timeOut: 3000,positionClass: "toast-bottom-right"});
                                }else{
                                    toastr.error('{{ trans('private.error_updating_state_or_sending_email_to_user') }}', '', {timeOut: 3000,positionClass: "toast-bottom-right"});
                                }

                                $('#updateStatusModal').modal('hide');
                            },
                            error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                                $('#updateStatusModal').modal('hide');
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

                    $('#updateStatusModal').modal('hide');
                });

            });

        });

        var getComments = function (type, cbKey, topicKey) {

            $.ajax({
                method: 'POST', // Type of response and matches what we said in the route
                url: "{{action('ModerationController@ajaxShowComments')}}", // This is the url we gave in the route
                data: {
                    'type': type,
                    'cbKey': cbKey,
                    'topicKey': topicKey,
                },
                success: function (response) { // What to do if we succeed
                    if (response.success) {

                        //show modal

                        $('#topicsCommentsModal').modal('show');
                        $('#commentsModal').html(response.html);

                    }else{
                        toastr.error('{{ trans('private.error_updating_state_or_sending_email_to_user') }}', '', {timeOut: 3000,positionClass: "toast-bottom-right"});
                    }

//                        $('#updateStatusModal').modal('hide');
                },
                error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
//                        $('#updateStatusModal').modal('hide');
                }
            });


        }

        // ------------------- Modal related function
        function updateStatus(topicKey,status,cbKey,type){

            $('#topicKeyStatus').val(topicKey);
            $('#status_type_code').val(status);
            $('#cb_key_hidden').val(cbKey);
            $('#type_hidden').val(type);

            $('#updateStatusModal').modal('show');
        }

        function datatablePostsToModerate(){
            $('#moderation_posts').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },
                responsive: true,
                processing: true,
                serverSide: true,
                paging: true,
                bFilter: true,
                bInfo: false,
                bSort: true,
                aaSorting: [[]],
                ajax: '{!! action('ModerationController@getPostsToModerate') !!}',
                columns: [
                    {data: 'topic', name: 'topic'},
                    {data: 'created_by', name: 'created_by'},
                    {data: 'content', name: 'content'},
//                    {data: 'abuses', name: 'abuses'},
                    {data: 'created_at', name: 'createdAt'},

                    { data: 'action', name: 'action', width: '10%' }
                ],
                order: [['3', 'desc']],
                pageLength: 10
            });
        }
    </script>
    @endsection