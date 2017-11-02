@extends('private._private.index')

@section('header_styles')
    <link href="{{ asset("css/private/checkbox-solstice.css")}}" rel="stylesheet" type="text/css"/>
@endsection
@section('content')
    {{ Form::open(array('url' => action("CbsController@exportTopics",['type'=>$type,'padKey'=>$cbKey]), 'method' => 'post','onkeypress' => 'return event.keyCode != 13;' )) }}
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title">{{ trans('privateCbs.export_topics') }}</h3>
        </div>
        <div class="box-body">
            @if(!empty($voteEvents))
                <div class="row">
                    <div class="col-6" style="padding-bottom: 20px">
                        <label for="voteEventSelect">{{ trans("privateCbs.select_the_vote_event") }}</label>
                        <span class="form-text oneform-help-block" style="margin:-4px 0px 5px;font-size:10px;">{{ trans("privateCbs.select_the_vote_event_description") }}</span>
                        <select id="voteEventSelect" name="voteEventSelect" class="voteEventSelect" style="width: 100%;" required>
                            <option value=""></option>
                            @foreach($voteEvents as $key => $voteEvent)
                                <option value="{!! $key !!}">{!! $voteEvent !!}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            @endif
            <div class="row">
                <div class="col-sm-6 col-12">
                    <div class="form-group input-group">
                        <span class="input-group-addon"><i class="fa fa-arrow-up" aria-hidden="true"></i> {{ trans('privateCbsVote.total_topics') }}</span>
                        <input id="top_topics" type="number" class="form-control" name="top_topics" value="10">
                    </div>
                </div>
                <div class="col-sm-6 col-12">
                    <div class="form-group input-group">
                        <span class="input-group-addon"><i class="fa fa-thumbs-up" aria-hidden="true"></i> {{ trans('privateCbsVote.min_votes') }}</span>
                        <input id="min_votes" type="number" class="form-control" name="min_votes" value="0">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <label for="pad_type">{{ trans("privateCbs.select_the_pad_type") }}</label>
                        <span class="form-text oneform-help-block" style="margin:-4px 0px 5px;font-size:10px;">{{ trans("privateCbs.select_the_pad_type_description") }}</span>
                        <select name="pad_type" id="pad_type" style="width:100%;" required>
                            <option value=""></option>
                            @if(ONE::verifyModuleAccess('cb','idea'))
                                <option value="idea">{{ trans("privateCbs.idea") }}</option>
                            @endif
                            @if(ONE::verifyModuleAccess('cb','forum'))
                                <option value="forum">{{ trans("privateCbs.forum") }}</option>
                            @endif
                            @if(ONE::verifyModuleAccess('cb','discussion'))
                                <option value="discussion">{{ trans("privateCbs.discussion") }}</option>
                            @endif
                            @if(ONE::verifyModuleAccess('cb','proposal'))
                                <option value="proposal">{{ trans("privateCbs.proposal") }}</option>
                            @endif
                            @if(ONE::verifyModuleAccess('cb','public_consultation'))
                                <option value="public_consultation">{{ trans("privateCbs.public_consultation") }}</option>
                            @endif
                            @if(ONE::verifyModuleAccess('cb','tematic_consultation'))
                                <option value="tematic_consultation">{{ trans("privateCbs.tematic_consultation") }}</option>
                            @endif
                            @if(ONE::verifyModuleAccess('cb','survey'))
                                <option value="survey">{{ trans("privateCbs.survey") }}</option>
                            @endif
                            @if(ONE::verifyModuleAccess('cb','project'))
                                <option value="project">{{ trans("privateCbs.project") }}</option>
                            @endif
                            @if(ONE::verifyModuleAccess('cb','phase1'))
                                <option value="phase1">{{ trans("privateCbs.phase1") }}</option>
                            @endif
                            @if(ONE::verifyModuleAccess('cb','phase2'))
                                <option value="phase2">{{ trans("privateCbs.phase2") }}</option>
                            @endif
                            @if(ONE::verifyModuleAccess('cb','phase3'))
                                <option value="phase3">{{ trans("privateCbs.phase3") }}</option>
                            @endif
                        </select>
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-group">
                        <label for="pad_selected">{{ trans("privateCbs.select_the_pad") }}</label>
                        <span class="form-text oneform-help-block" style="margin:-4px 0px 5px;font-size:10px;">{{ trans("privateCbs.select_the_pad_description") }}</span>
                        <select name="pad_selected" id="pad_selected" style="width:100%;" required>
                            <option value=""></option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <a data-toggle="collapse" href="#collapse1"><h4> {{ trans("privateCbs.mapping_params") }} </h4></a>
                    <span class="form-text oneform-help-block" style="margin:-4px 0px 5px;font-size:10px;">{{ trans("privateCbs.mapping_params_description") }}</span>
                </div>
                <div id="collapse1" class="panel-collapse collapse show">
                    <td class="card-body">
                        <div id="mapping_params">
                        </div>
                    </td>
                </div>

            </div>

            <table id="topics_list" class="table table-bordered table-hover table-striped ">
                <thead>
                <tr>
                    <th>{{ trans('privateCbs.topic_title') }}</th>
                    <th>{{ trans('privateCbs.topic_created_by') }}</th>
                    <th>{{ trans('privateCbs.total_votes') }}</th>
                    <th></th>
                </tr>
                </thead>
            </table>
        </div>
        <div class="box-footer">
            <input class="btn btn-flat empatia" type="submit" value="{{ trans('privateCbs.export') }}">
        </div>
    </div>
    {{ Form::close() }}
@endsection

@section('scripts')
    <script>
        $( document ).ready(function() {

            getTopTopics();
            $("#pad_type").select2({
                placeholder: '{{ trans("privateCbs.select_the_pad_type") }}'
            });

            $("#pad_selected").select2({
                placeholder: '{{ trans("privateCbs.select_the_pad") }}',
                ajax: {
                    "url" : '{!! action('CbsController@getListOfCbsByType') !!}',
                    "type": "POST",
                    "data": function () {
                        return {
                            "_token": "{{ csrf_token() }}",
                            "type":  $("#pad_type").val(), // search term
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: $.map(data, function(item) {
                                return {
                                    text: item.title,
                                    id: item.cb_key
                                }
                            })
                        };
                    }
                }
            });
            $( "#pad_selected" ).change(function() {

                $.ajax({
                    url: '{{action("CbsController@mappingParams")}}',
                    method: 'post',
                    data: {
                        cb_key: '{{$cbKey}}',
                        cb_key_export: $("#pad_selected").val(),
                        _token: "{{ csrf_token()}}"
                    },
                    success: function(response){
                        $('#mapping_params').html(response);
                    },
                    error: function(msg){
                    }
                });

            });


        });


        $("#voteEventSelect").select2({
            placeholder: '{{ trans("privateCbs.select_the_vote_event") }}'
        });

        $( "#top_topics,#min_votes,#voteEventSelect" ).change(function() {
            getTopTopics();
        });


        function getTopTopics(){
            // Topics List
            var voteEventKey = '{{$voteEventKey ?? null}}';
            if(voteEventKey.length == 0){
                voteEventKey = $( "#voteEventSelect" ).val();
            }
            var topTopics = $( "#top_topics" ).val();
            var minVotes = $( "#min_votes" ).val();

            $('#topics_list').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },
                responsive: true,
                processing: true,
                serverSide: true,
                bDestroy: true,
                paging: false,
                bFilter: false,
                ajax: {
                    "url" : '{!! action('CbsController@topicsToExport',['type'=>$type,'padKey'=>$cbKey]) !!}',
                    "data" : {
                        "vote_event_key" : voteEventKey,
                        "top_topics": topTopics,
                        "min_votes": minVotes
                    }
                },
                columns: [
                    { data: 'title', name: 'title', width: "65%" },
                    { data: 'created_by', name: 'created_by' },
                    { data: 'total_votes', name: 'total_votes',className: "text-center" },
                    { data: 'action', name: 'action', searchable: false, orderable: false, className: "text-center"},
                ],
                order: [['2', 'desc']]
            });

        }
    </script>
@endsection