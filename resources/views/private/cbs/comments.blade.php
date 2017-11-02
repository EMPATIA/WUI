@extends('private._private.index')

@section('content')

    @if(!empty($cb->flags))
        <form action="javascript:reloadTable()">
            <div class="row ">
                <div class="col-12 col-md-6">
                    <label for="flags_filter">{{trans('privateCbs.filter_by_flags')}}</label><br>
                    <select id="flags_filter" style="width:100%;" class="form-control filters filters_select"
                            name="flags_filter">
                        <option selected="selected" value="">{{trans('privateCbs.select_value')}}</option>

                        @foreach($cb->flags as $key => $flag)
                            <option value="{{$flag->id}}">{{$flag->title}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-6">
                    <input type="submit" class="btn btn-flat btn-submit">
                </div>
            </div>
        </form>
        <br><br><br><br>
    @endif

    <div class="box-private">
        <div class="box-header">
            <h3 class="box-title">{{ trans('privateCbs.comments') }}</h3>
        </div>
        <div class="box-body">

            <table id="moderation_posts" class="table table-responsive  table-hover table-striped ">
                <thead>
                <tr>
                    <th>{{ trans('private.topic') }}</th>
                    <th>{{ trans('private.created_by') }}</th>
                    <th>{{ trans('private.created_at') }}</th>
                    <th>{{ trans('private.content') }}</th>
                    <th>{{ trans('private.flag') }}</th>
                </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
    </div>

    <!-- attach flag modal -->
    <div class="modal fade" tabindex="-1" role="dialog" id="flagAttachmentModal" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="card-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">{{trans("privateCbs.flag_attachment")}}</h4>
                </div>
                <div style="margin-left:20px;">

                    <h5>{{trans('privateCbs.pad')}} : {{$cb->title}}</h5>

                </div>
                <div class="modal-body">
                    <div class="card flat">
                        {!! Form::hidden('postKey','', ['id' => 'postKey']) !!}
                        <div class="card-header">{{trans('privateCbs.select_option')}}</div>
                        <div class="card-body">
                            <div class="form-group ">
                                <label for="flags">{{trans('privateCbs.flags')}}</label>
                                <div for="flags"  style="font-size:x-small">{{trans('privateCbs.flags_description')}}</div>

                                <select id="flag_id" class="form-control" name="flag_id">
                                    <option selected="selected" value="">{{trans('privateCbs.select_value')}}</option>
                                    @foreach($cb->flags as $key => $flag)
                                        <option value="{{$flag->id}}">{{$flag->title}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="card" style="border-radius: 0">
                                <div class="card-body">
                                    @php $i = 0; @endphp
                                    <ul class="nav nav-tabs" role="tablist">
                                        @foreach($languages as $language)
                                            <li role="presentation {{ $i==0 ? 'active' : '' }}" class="{{ $i==0 ? 'active' : '' }}"><a href="#tab-translation-{{ $language->code }}" aria-controls="affa" role="tab" data-toggle="tab" class="{{ $i==0 ? 'active' : '' }}">{{ $language->name }}</a></li>
                                            @php $i++; @endphp
                                        @endforeach
                                    </ul>
                                    <div class="tab-content">
                                    @php $i = 0; @endphp
                                    @foreach($languages as $language)
                                        <div role="tabpanel" class="tab-pane {{ $i==0 ? 'active' : '' }}" id="tab-translation-{{ $language->code }}">
                                             <div class="form-group  ">
                                                <label for="flagAttachmentDescription_{{ $language->code }}">{{trans('privateCbs.flag_attachment_description')}}</label>
                                                <textarea class="form-control" id="flagAttachmentDescription_{{ $language->code }}" name="flagAttachmentDescription_{{ $language->code }}" value=""></textarea>
                                            </div>
                                            @php $i++; @endphp
                                        </div>
                                    @endforeach
                                    </div>
                                </div>
                            </div>

                        </div>
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
@endsection

@section('scripts')
    <script>

        //function to get status history
        function seeFlagHistory(postKey){
            $.ajax({
                method: 'POST', // Type of response and matches what we said in the route
                url: '{{action("FlagsController@getElementFlagHistory")}}', // This is the url we gave in the route
                data: {
                    attachmentCode: "POST",
                    elementKey: postKey
                }, // a JSON object to send back
                success: function (response) { // What to do if we succeed
                    if(response != 'false'){
                        $('#flagHistory').html(response);
                        $('#flagHistoryModal').modal('show');
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                }
            });
        }

        var table;
        $(function () {

            // Topics List
            table=  $('#moderation_posts').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{!! action('CbsController@getAllComments',['type'=>$type,'cbKey'=>$cb->cb_key]) !!}',
                    type:"post",
                    data:function(d){
                        d.parameters=buildSearchDataRoles();
                        d.filters_static=buildSearchData();
                    }
                },
                columns: [
                    { data: 'topic.title', name: 'topic.title'},
                    { data: 'created_by', name: 'created_by','searchable': false },
                    { data: 'created_at', name: 'created_at','searchable': false },
                    { data: 'contents', name: 'contents'},
                    { data: 'flag', name: 'flag','searchable': false},
                    { data: 'action', name: 'action', searchable: false, orderable: false}
                ],
                order: [['2', 'desc']]
            });



        });
        function attachFlag(postKey){
            $('#postKey').val(postKey);
            $('#flagAttachmentModal').modal('show');
        }

        $('#flagAttachmentModal').on('show.bs.modal', function (event) {
            $('#attachFlagSave').off();
            $('#attachFlagSave').on('click', function (evt) {
                var allVals = {};
                var isValid = true;

                //get inputs to update status
                allVals['postKey'] = $('#postKey').val();

                $('#flagAttachmentModal textarea').each(function () {
                    if($(this).val().length > 0){
                        allVals[$(this).attr('name')] = $(this).val();
                    }
                });
                $('#flagAttachmentModal select').each(function () {
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
                    $('#flagAttachmentModal input:text').each(function () {
                        $(this).val('');
                    });
                    $('#flagAttachmentModal textarea').each(function () {
                        $(this).val('');
                    });
                    $('#flagAttachmentModal select').each(function () {
                        $(this).closest('.form-group').removeClass('has-error');
                        $(this).val('');
                    });

                    allVals.attachmentCode = 'POST';
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
                }
            });
            //clear inputs and close update status modal
            $('#closeFlagAttachmentModal').on('click', function (evt) {
                $('#flagAttachmentModal input:text').each(function () {
                    $(this).val('');
                });
                $('#flagAttachmentModal textarea').each(function () {
                    $(this).val('');
                });
                $('#flagAttachmentModal select').each(function () {
                    $(this).val('');
                });

                $('#flagAttachmentModal').modal('hide');
            });
            {{--{!! session()->get('LANG_CODE').'json' !!}--}}
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
                    console.log(this.classList.contains("filters_date"));
                    if($(this).val()!=""){
                        allValues[$(this).attr('name')] = $(this).val();
                    }
                }
            });
            return allValues;
        }

    </script>
@endsection
