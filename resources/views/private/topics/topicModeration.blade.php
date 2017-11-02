@extends('private._private.index')

@section('header_scripts')
    <!-- Maps -->
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCn-K_QLK1mNPM6SjCjnUl2e3neuQ9FX6Q&libraries=places" type="text/javascript"></script>
@endsection

@section('content')
        <!-- Form -->
        @php
        $form = ONE::form('topic', trans('privatePropositionModeration.posts'))
                ->settings(["model" => isset($topic) ? $topic : null, 'id'=>isset($topic) ? $topic->topic_key : null])

                ->show(null, null, null, null)
                ->create('CbsController@index', 'CbsController@index' , ['type'=> $type])
                ->edit('CbsController@index', 'CbsController@index' , ['type'=> $type])
                ->open();
        @endphp

        <div class="margin-bottom-20">
            <a href="javascript:updateStatus('{{$topic->topic_key}}','accepted')" class="btn btn-flat empatia">{{trans('privatePropositionModeration.accept_all')}}</a>
            <a href="javascript:updateStatus('{{$topic->topic_key}}','not_accepted')" class="btn btn-flat btn-preview">{{trans('privatePropositionModeration.reject_all')}}</a>
        </div>

        <!-- Topic Details -->
        @if(ONE::actionType('topic') == 'show' && isset($configurations) && (ONE::checkCBsOption($configurations, 'ALLOW-COMMENTS')))
        <!-- Topic Posts -->
{{--            <div class="card flat">
                <div class="card-header">{{ trans('privateCbs.posts') }}</div>
                <div class="box-body">--}}
                    <table id="posts_list" class="table table-responsive table-hover">
                        <thead>
                        <tr>
                            <th>{{ trans('privateTopics.id') }}</th>
                            <th>{{ trans('privateTopics.approve') }}</th>
                            <th>{{ trans('privateTopics.message') }}</th>
                            <th>{{ trans('privateTopics.parent_id') }}</th>
                            <th>{{ trans('privateTopics.postsAbuses') }}</th>
                            <th>{{ trans('privateTopics.postsCreated') }}</th>
                            <th></th>
                        </tr>
                        </thead>
                    </table>
{{--                </div>
            </div>--}}
        @endif
        {!! $form->make() !!}

    <div class="modal fade" tabindex="-1" role="dialog" id="updateStatusModal" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="card-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">{{trans("privatePropositionModeration.update_status")}}</h4>
                </div>
                <div class="modal-body">
                    <div class="card flat">
                        {!! Form::hidden('topicKeyStatus','', ['id' => 'topicKeyStatus']) !!}
                        <div class="card-header">{{trans('privatePropositionModeration.add_comments')}}</div>
                        <div class="card-body">

                            <input type="text" id="status_type_code" class="form-control hidden" name="status_type_code" value="">
                            </input>

                            <div class="form-group">
                                <label for="contentStatusComment">{{trans('privatePropositionModeration.private_comment')}}</label>
                                <textarea class="form-control" rows="5" id="contentStatusComment" name="contentStatusComment" style="resize: none;"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="contentStatusPublicComment">{{trans('privatePropositionModeration.public_comment')}}</label>
                                <textarea class="form-control" rows="5" id="contentStatusPublicComment" name="contentStatusPublicComment" style="resize: none;"></textarea>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="closeUpdateStatus">{{trans("privatePropositionModeration.close")}}</button>
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <button type="button" class="btn btn-primary" id="updateStatus">{{trans("privatePropositionModeration.save_changes")}}</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <div class="modal fade" tabindex="-1" role="dialog" id="showAbuses" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="card-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">{{trans("privatePropositionModeration.show_abuses")}}</h4>
                </div>
                <div class="modal-body" id="abuses-body" style='overflow-y:auto'>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="closeShowAbuses">{{trans("privatePropositionModeration.close")}}</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
@endsection

@section('scripts')

    <script>
        var post;
        function showAbuses(postKey){
            post = postKey;
            $('#showAbuses').modal('show');
        }

        $('#showAbuses').on('show.bs.modal', function (event) {
            $.ajax({
                'url': '{{ action('TopicController@getAbuses', [$type, $cbKey, $topic->topic_key]) }}',
                'method': 'get',
                'data': { postKey: post },
                success: function(response){
                    $("#abuses-body").html(response);
                },
                error: function(){
                    console.log("erro");
                }
            })

            $('#closeShowAbuses').on('click', function (evt) {

                $('#showAbuses').modal('hide');
            });

        });

        function updateStatus(topicKey,status){
            $('#topicKeyStatus').val(topicKey);
            $('#status_type_code').val(status);
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


                //all values ok to update
                if (isValid) {
                    $('#updateStatusModal input:text').each(function () {
                        $(this).val('');
                    });
                    $('#updateStatusModal textarea').each(function () {
                        $(this).val('');
                    });
                    $.ajax({
                        method: 'POST', // Type of response and matches what we said in the route
                        url: "{{action('TopicController@updateStatusTopic')}}", // This is the url we gave in the route
                        data: allVals, // a JSON object to send back
                        success: function (response) { // What to do if we succeed
                            console.log(response)
                            if (response != 'false') {
                                window.location.href = response;
                                toastr.success('{{ trans('privatePropositionModeration.update_topic_status_ok') }}', '', {timeOut: 3000,positionClass: "toast-bottom-right"});
                            }else{
                                toastr.error('{{ trans('privatePropositionModeration.error_updating_state_or_sending_email_to_user') }}', '', {timeOut: 3000,positionClass: "toast-bottom-right"});
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
    </script>

    @if(ONE::actionType('topic') == 'show' && isset($configurations) && (ONE::checkCBsOption($configurations, 'ALLOW-COMMENTS')))
        <script>
            $(function () {
                // Posts List
                $('#posts_list').DataTable({
                    language: {
                        url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                        search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                    },
                    responsive: true,
                    processing: true,
                    serverSide: true,
                    ajax: '{!! action('TopicController@getIndexTablePosts', ['type '=> $type, 'cbKey' => $cbKey, 'topicKey' => $topic->topic_key]) !!}',
                    columns: [
                        { data: 'id', name: 'id' },
                        { data: 'approve', name: 'approve' , width: '70px', searchable: false, orderable: false},
                        { data: 'message', name: 'message' },
                        { data: 'parent_id', name: 'parent_id' },
                        { data: 'abuses', name: 'abuses' },
                        { data: 'created_by', name: 'created_by', width: '15%' },
                        { data: 'action', name: 'action', searchable: false, orderable: false, width: '15px' }
                    ],
                    order: [['5', 'desc']]
                });
            });
        </script>
    @endif
@endsection
