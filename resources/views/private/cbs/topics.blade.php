@extends('private._private.index')



@section('content')
    @include('private.cbs.tabs')

    <div class="card flat topic-data-header" >
        <p><label for="contentStatusComment"> {{trans('privateCbs.pad')}}</label>{{$cb->title}}</p>
        @if(!empty($cbAuthor))
            <p><label for="contentStatusComment"> {{trans('privateCbs.author')}}</label>
                <a href="{{action('UsersController@show', ['userKey' => $cbAuthor->user_key, 'role' => $cbAuthor->role ?? null])}}">{{$cbAuthor->name}}</a>
            </p>
        @endif
        <p><label for="contentStatusComment"> {{trans('privateCbs.start_date')}}</label>{{$cb->start_date}}</p>
    </div>



    <div class="box box-primary" style="margin-top: 10px">

        <form action="javascript:reloadTable()" id="search_form" class="box-body">
            <label class="filterBy-title">{!! trans("privateCbs.filter_by") !!}</label><br>
            <div class="row">
                @if(!empty($cbVotes))
                    <div class="col-6 col-md-3 col-lg-3">
                        <label for="vote_event">
                            {{ trans("privateCbs.filter_by_vote_event") }}
                        </label><br>
                        <select id="vote_event" style="width:100%;" class="form-control parameters parameters_select" name="vote_event">
                            <option selected="selected" value="">
                                {{trans('privateCbs.select_value')}}
                            </option>
                            @foreach($cbVotes as $key => $voteEvent)
                                <option value="{{ $voteEvent->vote_key }}">
                                    {{ $voteEvent->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif


                <div class="col-6 col-md-3 col-lg-2">
                    <label for="start_date">{!! trans("privateCbs.filterDateMin") !!}</label>
                    <div class="input-group date">
                        <span class="input-group-addon"><i class="glyphicon glyphicon-th"></i></span> <input class="form-control oneDatePicker filters filters_date" style="width:100%" id="start_date" placeholder="yyyy-mm-dd" data-date-format="yyyy-mm-dd" name="start_date" type="text" value="">

                    </div>
                </div>

                <div class="col-6 col-md-3 col-lg-2">
                    <label for="end_date">{!! trans("privateCbs.filterDateMax") !!}</label>
                    <div class="input-group date"><span class="input-group-addon"><i class="glyphicon glyphicon-th"></i></span> <input class="form-control oneDatePicker filters filters_date" style="width:100%" id="end_date" placeholder="yyyy-mm-dd" data-date-format="yyyy-mm-dd" name="end_date" type="text" value="">

                    </div>
                </div>

                <div class="col-6 col-sm-3 col-lg-3">
                    <label for="status_type_code">{{trans('privateCbs.topic_status')}}</label><br>
                    <select id="status" style="width:100%" class="form-control filters filters_select" name="status">
                        <option selected="selected" value="">{{trans('privateCbs.select_value')}}</option>

                        @foreach($statusTypes as $key => $statusType)
                            <option value="{{$key}}">{{$statusType}}</option>
                        @endforeach
                    </select>

                </div>
                <div class="col-6 col-md-3 col-lg-3" @if(!empty($cbVotes)) style="margin-top:20px;" @endif>
                    <label for="author_name">{{trans('privateCbs.author')}}</label><br>
                    <select id="author" style="width:100%;" class="form-control filters filters_select" name="author">
                        <option selected="selected" value="">{{trans('privateCbs.select_value')}}</option>

                        @foreach($authors as $key => $author)
                            <option value="{{$author->user_key}}">{{$author->name}}</option>
                        @endforeach
                    </select>
                </div>

                @if(!empty($cb->flags))
                    <div class="col-6 col-md-3 col-lg-3" style="margin-top:20px;">
                        <label for="flags_filter">{{trans('privateCbs.filter_by_flags')}}</label><br>
                        <select id="flags_filter" style="width:100%;" class="form-control filters filters_select" name="flags_filter">
                            <option selected="selected" value="">{{trans('privateCbs.select_value')}}</option>

                            @foreach($cb->flags as $key => $flag)
                                @if($loop->first)
                                    <option value="-1">{{ trans("privateCbs.without_flags") }}</option>
                                @endif
                                <option value="{{$flag->id}}">{{$flag->title}}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                @if((!ONE::checkCBsOption($configurations, 'TOPIC-AS-PRIV-QUESTIONNAIRE')) && (!ONE::checkCBsOption($configurations, 'TOPIC-AS-PUBLIC-QUESTIONNAIRE')))

                    @foreach ($parameters as $key=> $value)
                        @if($value['code']=="text")
                            <div class="col-6 col-md-3" style="margin-top:20px;">
                                <label for="info_name">{{$value['name']}}</label><br>
                                <input type="text" name="{{$value['id']}}" id="{{$value['id']}}" class="form-control parameters parameters_text" style="width:46%;"  placeholder="{{trans('privateCbs.write_value')}}"></input>
                            </div>
                        @elseif($value['code']=="category")
                            <div class="col-6 col-md-3" style="margin-top:20px;">
                                <label for="category_name">{{$value['name']}}</label><br>
                                <select id="category" style="width:100%;" class="form-control parameters parameters_select" name="{{$value['id']}}">
                                    <option selected="selected" value="">{{trans('privateCbs.select_value')}}</option>
                                    @foreach($value['options'] as $key => $option)
                                        <option value="{{$key}}">{{$option}}</option>
                                    @endforeach
                                </select>
                            </div>
                        @elseif($value['code']=="check_box")
                            <div class="col-6 col-md-3" style="margin-top:20px;">
                                <label for="checkbox_name">{{$value['name']}}</label><br>
                                <select id="checkbox" style="width:100%;" class="form-control parameters parameters_select" name="{{$value['id']}}">
                                    <option selected="selected" value="">{{trans('privateCbs.select_value')}}</option>
                                    @foreach($value['options'] as $key => $option)
                                        <option value="{{$key}}">{{$option}}</option>
                                    @endforeach
                                </select>
                            </div>
                        @elseif($value['code']=="radio_buttons")
                            <div class="col-6 col-md-3" style="margin-top:20px;">
                                <label for="radioButtons_name">{{$value['name']}}</label><br>
                                <select id="radioButtons" style="width:100%;" class="form-control parameters parameters_select" name="{{$value['id']}}">
                                    <option selected="selected" value="">{{trans('privateCbs.select_value')}}</option>
                                    @foreach($value['options'] as $key => $option)
                                        <option value="{{$key}}">{{$option}}</option>
                                    @endforeach
                                </select>
                            </div>
                        @elseif($value['code']=="topic_checkpoint_phase")
                            <div class="col-6 col-md-3" style="margin-top:20px;">
                                <label for="radioButtons_name">Status (phases)</label><br>
                                <select id="radioButtons" style="width:100%;" class="form-control parameters parameters_select" name="phases">
                                    <option selected="selected" value="">{{trans('privateCbs.select_value')}}</option>
                                    @php $i = 0; @endphp
                                    @foreach($value['options'] as $option)
                                        <option value="{{ $option['id'] }}">{{$option['name']}}</option>
                                        @php $i++; @endphp
                                    @endforeach
                                </select>
                            </div>
                        @endif
                    @endforeach
                @endif
                <div class="col-6 col-md-3 col-lg-2" @if(!empty($cbVotes)) style="margin-top:20px;" @endif>
                    <br>
                    <input type="submit" form="search_form" value="{{ trans('privateCbs.search') }}" class="btn-submit" style="float: right; margin-top:13px">
                </div>
            </div>
        </form>

        <div class="box-body">
            <div class="card-title">{{ trans('privateCbs.topics') }}</div>

            @if($type == 'tematicConsultation' || $type == 'publicConsultation')
                <table id="topics_list" class="table table-responsive  table-hover table-striped ">
                    <thead>
                    <tr>
                        <th><input type="checkbox" id="checkAll"/></th>
                        <th>{{ trans('privateCbs.topic_number') }}</th>
                        @if((ONE::checkCBsOption($configurations, 'TOPIC-AS-PRIV-QUESTIONNAIRE')) || (ONE::checkCBsOption($configurations, 'TOPIC-AS-PUBLIC-QUESTIONNAIRE')))
                            <th></th>
                        @else
                            <th>{{ trans('privateCbs.topic_title') }}</th>
                        @endif
                        <th>{{ trans('privateCbs.topic_created_at') }}</th>
                        <th>{{ trans('privateCbs.topic_author') }}</th>
                        <th>
                            @if(Session::get('user_role') == 'admin')
                                {!! ONE::actionButtons(['type'=>$type,'cbKey'=>$cb->cb_key], ['create' => 'TopicController@create']) !!}
                                <a href="{{ action("TopicController@createWithUser",['type'=>$type,'cbKey'=>$cb->cb_key]) }}" class="btn btn-flat btn-create btn-xs">
                                    <i class="fa fa-user-plus"></i>
                                </a>
                            @endif
                        </th>
                    </tr>
                    </thead>
                </table>
            @else
                <div class="dataTables_wrapper dt-bootstrap no-footer table-responsive ">
                    <table id="topics_list_status"
                           class="table table-responsive table-hover table-striped ">
                        <thead>
                        <tr>
                            <th><input type="checkbox" id="checkAll"/></th>
                            <th>{{ trans('privateCbs.topic_number') }}</th>
                            <th>{{ trans('privateCbs.topic_title') }}</th>
                            <th>{{ trans('privateCbs.topic_created') }}</th>
                            <th>{{ trans('privateCbs.topic_status') }}</th>
                            <th>{{ trans('privateCbs.topic_author') }}</th>
                            <th class="votes_count">{{ trans('privateCbs.topic_votes') }}</th>
                            <th>{{ trans('privateCbs.technical_analysis') }}</th>
                            <th>{{ trans('privateCbs.topic_status') }}</th>
                            <th>
                                @if(Session::get('user_role') == 'admin')
                                    @if(ONE::checkCBPermissions($cb->cb_key, "topic_create"))
                                        {!! ONE::actionButtons(['type'=>$type,'cbKey'=>$cb->cb_key], ['create' => 'TopicController@create']) !!}
                                    @endif
                                    @if(ONE::checkCBPermissions($cb->cb_key, "topic_create_with_user"))
                                        <a href="{{ action("TopicController@createWithUser",['type'=>$type,'cbKey'=>$cb->cb_key]) }}" class="btn btn-flat btn-create btn-xs">
                                            <i class="fa fa-user-plus"></i>
                                        </a>
                                    @endif
                                @endif
                            </th>
                        </tr>
                        </thead>
                    </table>
                </div>
            @endif

            <form id="exportExcelList"
                  action="{{ action("TopicController@excel",['type'=>$type,'cbKey'=>$cb->cb_key]) }}" method="POST">
                <input type="hidden" name="_token" value="@php echo csrf_token(); @endphp"/>
                <input type="hidden" id="export_analysis" name="export_analysis" value="0"/>
                <input type="hidden" id="exportIds" name="exportIds"/>
            </form>

            <form id="exportPdfList"
                  action="{{ action("TopicController@pdfList",['type'=>$type,'cbKey'=>$cb->cb_key]) }}" method="POST">
                <input type="hidden" name="_token" value="@php echo csrf_token(); @endphp"/>
                <input type="hidden" id="export_analysis_pdf" name="export_analysis_pdf" value="0"/>
                <input type="hidden" id="exportIdsPdf" name="exportIds"/>
            </form>

            {{--Exportation Data Model--}}
            <div class="modal fade" id="modalExport" role="dialog" aria-labelledby="modalExport">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="card-header">
                            <h3 class="modal-title">{{trans('privateCbs.exportTopicData')}}</h3>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-12 text-center">
                                    <div id="excel_list" class="btn btn-flat btn-success btn-sm">
                                        <i class="fa fa-file-excel-o" aria-hidden="true"></i>
                                        {{ trans('privateCbs.download_excel') }}
                                    </div>
                                    <div id="pdf_list" class="btn btn-flat btn-success btn-sm">
                                        <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                                        {{ trans('privateCbs.download_pdf') }}
                                    </div>
                                    <hr>
                                </div>
                                <div class="col-12">
                                    <label for="export_vote_analysis">
                                        Export Vote Analytics
                                    </label>
                                    <div class="onoffswitch">
                                        <input type="checkbox" name="export_vote_analysis" class="onoffswitch-checkbox" id="export_vote_analysis" value="1">
                                        <label class="onoffswitch-label" for="export_vote_analysis">
                                            <span class="onoffswitch-inner"></span>
                                            <span class="onoffswitch-switch"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" data-dismiss="modal">{{ trans('privateCbs.cancel') }}</button>
                        </div>
                    </div>
                </div>
            </div>

            {{--Delete Checkbox--}}
            <div class="modal fade" id="modalDelete" role="dialog" aria-labelledby="modalDelete">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="card-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title"> {{trans('privateCbs.delete')}}</h4>
                        </div>
                        <div class="modal-body">
                            <p>{{trans('privateCbs.are_you_sure_you_want_to_delete_this_topic')}}</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-flat btn-preview" id="deleteTopic"> {{trans('privateCbs.delete')}}</button>
                            <button type="button" class="btn empatia" data-dismiss="modal">{{ trans('privateCbs.cancel') }}</button>
                        </div>
                    </div>
                </div>
            </div>

            {{--Change State Data Button--}}
            <button type="button" class="btn btn-flat btn-submit dropdown-toggle dropdown-toggle-split " data-toggle="dropdown" aria-haspopup="true" name="none" aria-expanded="false"  style="margin-top:20px">
                {{trans("privateCbs.state")}}
            </button>
            <div class="dropdown-menu" >
                @foreach($statusTypes as $key => $statusType)
                    <a class="dropdown-item" onclick="updateChecklistItem('{{$key}}')" >{{$statusType}}</a>
                @endforeach
            </div>

            {{--Delete Data Button--}}
            <div id="deleteModal" class="btn btn-flat btn-submit" style="margin-top:20px">
                <i class="fa fa-remove" aria-hidden="true"></i>
                {{ trans('privateCbs.delete') }}
            </div>

            {{--Exportation Data Button--}}
            <div id="exportModal" class="btn btn-flat btn-submit">
                <i class="fa fa-download" aria-hidden="true"></i>
                {{ trans('privateCbs.export') }}
            </div>
        </div>
    </div>


    <!-- update status modal -->
    <div class="modal fade" tabindex="-1" role="dialog" id="updateStatusModal" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="card-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">{{trans("privateCbs.update_status")}}</h4>
                </div>
                <div style="margin-left:20px;">
                    <h5>{{trans('privateCbs.pad')}} : {{$cb->title}}</h5>
                </div>
                <div class="modal-body">
                    <div class="card flat">
                        {!! Form::hidden('topicKeyStatus','', ['id' => 'topicKeyStatus']) !!}
                        <div class="card-header">{{trans('privateCbs.select_option')}}</div>
                        <div class="card-body">
                            <div class="form-group ">
                                <label for="status_type_code">{{trans('privateCbs.status_types')}}</label>
                                <div for="status_type_code"  style="font-size:x-small">{{trans('privateCbs.status_typesDescription')}}</div>
                                <select id="status_type_code" class="form-control" name="status_type_code">
                                    <option selected="selected" value="">{{trans('privateCbs.select_value')}}</option>
{{--                                    <option  value="0">{{trans('privateCbs.withoutstatus')}}</option>--}}
                                    @foreach($statusTypes as $key => $statusType)
                                        <option value="{{$key}}">{{$statusType}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="contentStatusComment">{{trans('privateCbs.private_comment')}}</label>
                                <div for="contentStatusComment" style="font-size:x-small">{{trans('privateCbs.private_commentDescription')}}</div>
                                <textarea class="form-control" rows="3" id="contentStatusComment" name="contentStatusComment" style="resize: none;"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="contentStatusPublicComment">{{trans('privateCbs.public_comment')}}</label>
                                <div for="contentStatusPublicComment" style="font-size:x-small">{{trans('privateCbs.public_commentDescription')}}</div>
                                <textarea class="form-control" rows="3" id="contentStatusPublicComment" name="contentStatusPublicComment" style="resize: none;"></textarea>
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

    @if(!empty($cb->flags))
        <!-- attach flag modal -->
        <div class="modal fade" tabindex="-1" role="dialog" id="flagAttachmentModal" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="card-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">{{trans("privateCbs.flag_attachment")}}</h4>
                    </div>
                    <div class="modal-body">
                        {!! Form::hidden('topicKey','', ['id' => 'topicKey']) !!}
                        <div class="row">
                            @foreach($cb->flags as $key => $flag)
                                <div class="col-12 col-md-8">
                                    {{ $flag->title }}
                                </div>
                                <div class="col-12 col-md-4">
                                    {!! Form::oneSwitch("flag[".$flag->id."][status]",null, null,["readonly"=>false]) !!}
                                </div>
                                <div class="col-12" id="flag-translations-{{ $flag->id }}">
                                    <ul class="nav nav-tabs" role="tablist">
                                        @foreach($languages as $language)
                                            <li role="presentation @if($loop->first) active @endif" class="@if($loop->first) active @endif">
                                                <a href="#tab-translation-{{ $flag->id }}-{{ $language->code }}" aria-controls="affa" role="tab" data-toggle="tab" class="@if($loop->first) active @endif">
                                                    {{ $language->name }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                    <div class="tab-content" style="min-height:auto;">
                                        @foreach($languages as $language)
                                            <div role="tabpanel" class="tab-pane @if($loop->first) active @endif" id="tab-translation-{{ $flag->id }}-{{ $language->code }}">
                                                <div class="form-group">
                                                    <label for="flag[{{ $flag->id }}][translation][{{ $language->code }}]">
                                                        {{trans('privateCbs.flag_attachment_description')}}
                                                    </label>
                                                    <input class="form-control" type="text" name="flag[{{ $flag->id }}][translation][{{ $language->code }}]" id="flag[{{ $flag->id }}][translation][{{ $language->code }}]">
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" id="closeFlagAttachmentModal">{{trans("privateCbs.close")}}</button>
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <button type="button" class="btn btn-primary" id="attachFlagSave">{{trans("privateCbs.save_changes")}}</button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->

        <!-- flag history modal -->
        <div class="modal fade" tabindex="-1" role="dialog" id="flagHistoryModal" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="card-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">{{trans('privateCbs.flag_history')}}</h4>
                    </div>
                    <div class="modal-body" style="overflow-y: scroll;max-height: 50vh;">
                        <div id="flagHistory">

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

                    allVals.type = '{{$type ?? null}}';
                    allVals.cbKey = '{{$cb->cb_key ?? null}}';

                    $.ajax({
                        method: 'POST', // Type of response and matches what we said in the route
                        url: "{{action('TopicController@updateStatusTopic',['type'=> $type,'cbKey'=>$cb->cb_key])}}", // This is the url we gave in the route
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

        var table;
        $(function () {
            // Topics List
            $('#topics_list').DataTable({
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: '{!! action('TopicController@getIndexTable', ['type'=>$type, 'cbKey'=>$cb->cb_key]) !!}',
                columns: [
                    { data: 'select_topics', name: 'select_topics', searchable: false, orderable: false, width: "5px" },
                    { data: 'topic_number', name: 'topic_number', width: "5px" },
                    { data: 'title', name: 'title' },
                    { data: 'created_at', name: 'created_at', width: "50px" },
                    { data: 'created_by', name: 'created_by', width: "20px" },
                    { data: 'action', name: 'action', searchable: false, orderable: false, width: "30px" }
                ],
                order: [['1', 'desc']]
            });

            // Topics List Status
            table = $('#topics_list_status').DataTable({
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: '{!! action('TopicController@getIndexTableStatus', ['type'=>$type,'cbKey'=>$cb->cb_key, 'hasFlags' => !empty($cb->flags)]) !!}',
                    type:"post",
                    data:function(d){
                        d.parameters=buildSearchDataRoles();
                        d.filters_static=buildSearchData();
                    },
                    dataFilter: function(data){
                        var json = jQuery.parseJSON(data);
                        json.recordsFiltered = json.filtered;
                        return JSON.stringify( json ); // return JSON string
                    }
                },
                columns: [
                    { data: 'select_topics', name: 'select_topics', searchable: false, orderable: false, width: "5px"},
                    { data: 'topic_number', name: 'topic_number', width: "5px"},
                    { data: 'title', name: 'title', width: "5px"},
                    { data: 'created_at', name: 'created_at', width: "5px" },
                    { data: 'status', name: 'status', searchable: false, orderable: false, width: "5px" },
                    { data: 'name', name: 'name', orderable: false, width: "5px"},
                    { data: 'votes', name: 'votes', width: "50px"},
                    { data: 'technical_analysis', name: 'technical_analysis', searchable: false, orderable: false, width: "5px"},
                    { data: 'update_status', name: 'update_status', searchable: false, orderable: false, width: "35px" },
                    { data: 'action', name: 'action', searchable: false, orderable: false, width: "35px" }

                ],
                "drawCallback": function() {
                    table.column( 6 ).visible(!($("#vote_event").val() == ""));
                },
                order: [['1', 'desc']]
            });
            table.column( 6 ).visible(!($("#vote_event").val() == ""));
            // Parameters List
        });

        function reloadTable(){
            table.ajax.reload();
        }

        function buildSearchDataRoles(){
            var allValues = {};
            $('.parameters').each(function () {
                if(this.classList.contains("parameters_select")){
                    if($(this).find(":selected").val()!=""){
                        allValues[$(this).attr('name')] = $(this).find(":selected").val();
                    }
                }else if (this.classList.contains("parameters_text")) {
                    if($(this).val()!=""){
                        allValues[$(this).attr('name')] = $(this).val();
                    }
                }
            });
            return allValues;
        }
        function buildSearchData(){
            var allValues = {};
            $('.filters').each(function () {
                if(this.classList.contains("filters_select")){
                    if($(this).find(":selected").val()!=""){
                        //console.log($(this).find(":selected").val());
                        allValues[$(this).attr('name')] = $(this).find(":selected").val();
                    }
                }else if(this.classList.contains("filters_date")){
                    if($(this).val()!=""){
                        allValues[$(this).attr('name')] = $(this).val();
                    }
                }
            });
            return allValues;
        }

        $('#checkAll').click(function () {
            var teste = $('input:checkbox').prop('checked', this.checked);
        });

        $(".filters_select").select2();
        $(".parameters_select").select2();

        $("#pdf_list").click(function() {

            var topicIds = [];
            $('.topic_id:checked').each( function(i, obj){
                topicIds.push($(obj).val());
            });

            $("#export_vote_analysis_pdf").val($("#export_vote_analysis").is(":checked") ? 1 : 0);
            $('#exportIdsPdf').val(JSON.stringify(topicIds));
            $('#exportPdfList').submit();
        });

        $("#excel_list").click(function() {
            var topicIds = [];
            $('.topic_id:checked').each( function(i, obj){
                topicIds.push($(obj).val());
            });

            $("#export_analysis").val($("#export_vote_analysis").is(":checked") ? 1 : 0);
            $('#exportIds').val(JSON.stringify(topicIds));
            $('#exportExcelList').submit();
        });

        $("#exportModal").click(function () {
            $("#modalExport").modal('show')
        });

        function attachFlag(topicKey){
            $('#topicKey').val(topicKey);
            $('#flagAttachmentModal').modal('show');
        }

        $('#flagAttachmentModal').on('show.bs.modal', function (event) {
            $('#attachFlagSave').off();
            $('#attachFlagSave').on('click', function (evt) {
                var allVals = {};
                var isValid = true;

                $.each($('#flagAttachmentModal :input').serializeArray(), function (key, value) {
                    allVals[value.name] = value.value;
                });
                
                allVals.attachmentCode = 'TOPIC';
                $.ajax({
                    method: 'POST', // Type of response and matches what we said in the route
                    url: "{{action('FlagsController@attachFlag')}}", // This is the url we gave in the route
                    data: allVals, // a JSON object to send back
                    success: function (response) { // What to do if we succeed

                        $('#flagAttachmentModal').modal('hide');
                        reloadTable();
                    },
                    error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                        $('#flagAttachmentModal').modal('hide');
                        toastr.error('{{ trans('privateCbs.error_updating_state_or_sending_email_to_user') }}', '', {timeOut: 3000,positionClass: "toast-bottom-right"});
                    }
                });
            });
            //clear inputs and close update status modal
            $('#closeFlagAttachmentModal').on('click', function (evt) {
                $('#flagAttachmentModal input:text').each(function () {
                    $(this).val('');
                });
                $('#flagAttachmentModal input:checkbox').each(function () {
                    $(this).prop("checked","");
                });
                $('#flagAttachmentModal').modal('hide');
            });
            {{--{!! session()->get('LANG_CODE').'json' !!}--}}
        });

        //function to get status history
        function seeFlagHistory(topicKey){
            $.ajax({
                method: 'POST', // Type of response and matches what we said in the route
                url: '{{action("FlagsController@getElementFlagHistory")}}', // This is the url we gave in the route
                data: {
                    attachmentCode: "TOPIC",
                    elementKey: topicKey
                }, // a JSON object to send back
                success: function (response) { // What to do if we succeed
                    if(response != 'false'){
                        $('#flagHistory').html(response);
                        if (!$('#flagHistoryModal').is(":visible"))
                            $('#flagHistoryModal').modal('show');
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                }
            });
        }

        //Checkbox - Delete

        $("#deleteModal").click(function () {
            $("#modalDelete").modal('show');
        });


        $("#deleteTopic").click(function() {
            $("#modalDelete").modal('hide');

            var cbType = [];
            var cbKey = [];
            var topicKey = [];

            $('.topic_id:checked').each( function(i, obj){
                cbType.push(($(this).attr('cbType')));
                cbKey.push(($(this).attr('cbKey')));
                topicKey.push(($(this).attr('topicKey')));
            });

            $.ajax({
                type: "delete",
                url: '{{action("TopicController@destroy", [0, 0,0]) }}',
                data: {
                    "_token"   : "{{ csrf_token() }}",
                    "cbType"   :cbType,
                    "cbKey"    :cbKey,
                    "topicKey" :topicKey,
                    "btnValidate" : 1,
                },
                success: function (response) {
                    location.reload();
                },
                error: function (response) {

                }
            });
        });

        function updateChecklistItem(key){

            var type = [];
            var cbKey = [];
            var topicKey = [];
            
            $('.topic_id:checked').each( function(i, obj){
                type.push(($(this).attr('cbType')));
                cbKey.push(($(this).attr('cbKey')));
                topicKey.push(($(this).attr('topicKey')));
            });

            if( jQuery.isEmptyObject(type)){
                alert("{!!trans('privateCbs.please_select_first_in_the_table') !!}");
            }
            else{
                $.ajax({
                    method: 'POST', // Type of response and matches what we said in the route
                    url: "{{action('TopicController@updateStatusTopic',[0,0])}}", // This is the url we gave in the route
                    data: {
                        "_token"   : "{{ csrf_token() }}",
                        "type"     :type,
                        "cbKey"    :cbKey,
                        "topicKey" :topicKey,
                        "status_type_code" :key,
                    },
                    success: function (response) { // What to do if we succeed
                        location.reload();
                    },
                    error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                    }
                });
            }
        }

    </script>
@endsection
