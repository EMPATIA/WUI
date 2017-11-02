@extends('private._private.index')

@section('content')

    <div class="box box-primary">
        <div class="box-header">
            @if($type == 'topic')
                <h3 class="box-title"><i class="fa"></i> {{ trans('privatePropositionModeration.proposition_title') }}</h3>

            @else
                <h3 class="box-title"><i class="fa"></i> {{ trans('privateCbs.'.$type.'_title') }}</h3>
            @endif

        </div>


        <div class="box-body">
            @if($type == 'topic')
                <table id="topics_list" class="table table-hover table-striped dataTable no-footer">
                    <thead>
                    <tr>
                        <th>{{ trans('privatePropositionModeration.proposition_key') }}</th>
                        <th>{{ trans('privatePropositionModeration.proposition_title') }}</th>
                        <th></th>

                    </tr>
                    </thead>
                </table>
            @else
                <table id="cb_list" class="table table-hover table-striped dataTable no-footer">
                    <thead>
                    <tr>
                        <th>{{ trans('privateCbs.id') }}</th>
                        <th>{{ trans('privateCbs.title') }}</th>
                        <th>
                            <!-- Button trigger modal -->
                            <button type="button" class="btn btn-flat btn-success btn-sm" data-toggle="modal" data-target="#createCbsCreateModal">
                                <i class="fa fa-plus"></i>
                            </button>
                        </th>
                    </tr>
                    </thead>
                </table>
            @endif
        </div>
    </div>

    @if(ONE::verifyModuleAccess('cb'))
        <!-- Create cb modal -->
        <div class="modal fade" tabindex="-1" role="dialog" id="createCbsCreateModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="card-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">{{trans("privateCbs.create_pad")}}</h4>
                    </div>

                    <div class="modal-body">
                        <div class="row">
                            @if(ONE::verifyModuleAccess('cb','idea'))
                                <div class="col-md-6 col-12" style="padding-top: 10px">
                                    <a class="btn btn-flat btn-block empatia" href="{{action('CbsController@create','idea')}}">
                                        <i class="fa fa-plus"></i> {!! trans("privateCbs.create_idea") !!}
                                    </a>
                                </div>
                            @endif
                            @if(ONE::verifyModuleAccess('cb','forum'))
                                <div class="col-md-6 col-12" style="padding-top: 10px">
                                    <a class="btn btn-flat btn-block empatia" href="{{action('CbsController@create','forum')}}">
                                        <i class="fa fa-plus"></i> {!! trans("privateCbs.create_forum") !!}
                                    </a>
                                </div>
                            @endif
                            @if(ONE::verifyModuleAccess('cb','discussion'))
                                <div class="col-md-6 col-12" style="padding-top: 10px">
                                    <a class="btn btn-flat btn-block empatia" href="{{action('CbsController@create','discussion')}}">
                                        <i class="fa fa-plus"></i> {!! trans("privateCbs.create_discussion") !!}
                                    </a>
                                </div>
                            @endif
                            @if(ONE::verifyModuleAccess('cb','proposal'))
                                <div class="col-md-6 col-12" style="padding-top: 10px">
                                    <a class="btn btn-flat btn-block empatia" href="{{action('CbsController@create','proposal')}}">
                                        <i class="fa fa-plus"></i> {!! trans("privateCbs.create_proposal") !!}
                                    </a>
                                </div>
                            @endif
                            @if(ONE::verifyModuleAccess('cb','public_consultation'))
                                <div class="col-md-6 col-12" style="padding-top: 10px">
                                    <a class="btn btn-flat btn-block empatia" href="{{action('CbsController@create','publicConsultation')}}">
                                        <i class="fa fa-plus"></i> {!! trans("privateCbs.create_public_consultation") !!}
                                    </a>
                                </div>
                            @endif
                            @if(ONE::verifyModuleAccess('cb','tematic_consultation'))
                                <div class="col-md-6 col-12" style="padding-top: 10px">
                                    <a class="btn btn-flat btn-block empatia" href="{{action('CbsController@create','tematicConsultation')}}">
                                        <i class="fa fa-plus"></i> {!! trans("privateCbs.create_tematic_consultation") !!}
                                    </a>
                                </div>
                            @endif
                            @if(ONE::verifyModuleAccess('cb','survey'))
                                <div class="col-md-6 col-12" style="padding-top: 10px">
                                    <a class="btn btn-flat btn-block empatia" href="{{action('CbsController@create','survey')}}">
                                        <i class="fa fa-plus"></i> {!! trans("privateCbs.create_survey") !!}
                                    </a>
                                </div>
                            @endif

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{trans("privateCbs.close")}}</button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->

    @endif

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
                        <div class="card-header">{{trans('privateCbs.add_comments')}}</div>
                        <div class="card-body">

                            <input type="text" id="status_type_code" class="form-control hidden" name="status_type_code" value="">
                            </input>


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

@endsection


@section('scripts')
    <script>
        {{--$(function() {--}}
            {{--$.ajax({--}}
                {{--url: '{{ action("CbsController@getSidebar1") }}',--}}
                {{--data: {},--}}
                {{--type: 'post',--}}
                {{--success: function(response){--}}
                    {{--$(".main-sidebar1").css('left', '+230px');--}}
                    {{--$(".main-sidebar1").css('opacity', '1');--}}
                    {{--$(".main-sidebar1").css('z-index', '100');--}}
                    {{--$(".main-sidebar1").css('width', '230px');--}}
                    {{--$(".main-sidebar").css('left', '0px');--}}
                    {{--$(".main-sidebar").html(response)--}}
                {{--},--}}
                {{--error: function(){--}}
                    {{--console.log("erro")--}}
                {{--}--}}
            {{--})--}}
            {{--$(".main-sidebar1").css('left', '0');--}}
            {{--$(".main-sidebar").css('left', '-230px');--}}
            {{--$(".main-sidebar1").append('{!! $vista !!}')--}}
        })*/
        $(function () {
            $('#cb_list').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },
                processing: true,
                serverSide: true,
                ajax: '{!! action('CbsController@getIndexTable',$type) !!}',
                columns: [
                    { data: 'id', name: 'id', width: "20px" },
                    { data: 'title', name: 'title'},
                    { data: 'action', name: 'action', searchable: false, orderable: false, width: "30px"}
                ],
                order: [['1', 'asc']]
            });

            $('#topics_list').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: '{!! action('TopicController@getFullTopicsTable',$type) !!}',
                columns: [
                    { data: 'topic_key', name: 'id', width: "20px" },
                    { data: 'title', name: 'title' },
                    { data: 'action', name: 'action', searchable: false, orderable: false, width: "30px" }
                ],
                order: [['1', 'asc']]
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

                            if (response != 'false') {
                                window.location.href = response;
                                toastr.success('{{ trans('privateCbs.update_topic_status_ok') }}', '', {timeOut: 3000,positionClass: "toast-bottom-right"});
                            }else{
                                toastr.error('{{ trans('privateCbs.error_updating_state_or_sending_email_to_user') }}', '', {timeOut: 3000,positionClass: "toast-bottom-right"});
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
@endsection
