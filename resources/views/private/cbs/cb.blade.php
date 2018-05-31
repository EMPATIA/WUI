@extends('private._private.index')
@section('header_styles')
    <link href="{!! asset(elixir('css/bootstrap-datetimepicker/bootstrap-datetimepicker.css')) !!}" rel="stylesheet"
          type="text/css"/>
@endsection
@section('header_scripts')
    <script src="{!! asset(elixir('js/bootstrap-datetimepicker/moment-with-locales.js')) !!}"></script>
    <script src="{!! asset(elixir('js/bootstrap-datetimepicker/bootstrap-datetimepicker.js')) !!}"></script>
@endsection
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
            ->show(  ((ONE::checkCBPermissions($cb->cb_key, "participation_details")) ? 'CbsController@edit' : null ) , ((ONE::checkCBPermissions($cb->cb_key, "participation_details")) ? 'CbsController@delete' : null ), ['type' => isset($type)? $type : null,'cbKey' => isset($cb) ? $cb->cb_key : null], null, ['type' => isset($type)? $type : null,'id' => isset($cb) ? $cb->cb_key : null])
            ->create('CbsController@store', 'CbsController@index', ['type' => isset($type)?$type:null,'id' => isset($cb) ? $cb->cb_key : null])
            ->edit('CbsController@update', 'CbsController@show', ['type' => isset($type)? $type : null,'id' => isset($cb) ? $cb->cb_key : null])
            ->open();
    @endphp

    <div class="row">
        <div class="col-12">
            @if(ONE::checkCBPermissions($cb->cb_key, "participation_details"))
                <button type="button" class="btn btn-flat btn-success btn-sm pull-left" data-toggle="modal"
                        data-target="#createCbsTemplateModal"
                        style="margin-bottom: 10px">{{ trans('privateCbs.create_template') }}</button>

                @if(!$subpad && $type == "project_2c")
                    <a class="btn btn-flat btn-success btn-xs pull-left"
                    href="{{action("SecondCycleController@initialize",['cbKey'=>$cb->cb_key])}}"
                    style="margin-bottom: 10px">{{ trans('secondCycle.initialize') }}</a>
                    <a class="btn btn-flat btn-info pull-right"
                    href="{{action("SecondCycleController@index",['cbKey'=>$rootCbKey])}}" style="margin-bottom: 10px"
                    target="_blank"><i class="fa fa-eye"></i> {{ trans('privateCbs.preview') }}</a>
                @elseif ($type != "project_2c")
                    <div class="">
                        <a class="btn btn-flat btn-preview pull-right"
                        href="{{action("PublicCbsController@show",['cbKey'=>$cb->cb_key,'type'=>$type])}}"
                        style="margin-bottom: 10px" target="_blank">
                            <i class="fa fa-eye"></i>
                            {{ trans('privateCbs.preview') }}
                        </a>
                    </div>
                    <div class="">
                        <a class="btn btn-flat empatia pull-right"
                        href="{{action("CbsController@duplicate", ['cbKey'=>$cb->cb_key,'type'=>$type])}}"
                        style="margin-bottom: 10px; margin-right: 10px" target="_blank">
                            <i class="fa fa-files-o"></i>
                            {{ trans('privateCbs.new_from_this') }}
                        </a>
                    </div>
                @endif
            @endif
        </div>
    </div>
    @if($hasTechnicalAnalysis??false)
        <div class="">
            <a class="btn btn-warning pull-right"
               href="{{action("CbsController@publishTechnicalAnalysisForm", ['type'=>$type,'cbKey'=>$cb->cb_key])}}"
               style="margin-bottom: 10px; margin-right: 10px">
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
    {!! Form::oneText('template',  array("name"=>trans('privateCbs.template'),"description"=>trans('privateCbs.descriptionTemplate')), isset($cb) ? $cb->template : null, ['class' => 'form-control', 'id' => 'template']) !!}
    {!! Form::oneDate('start_date', array("name"=>trans('privateCbs.start_date'),"description"=>trans('privateCbs.startDateDescription')), isset($cb) ? $cb->start_date : date('Y-m-d'), ['class' => 'form-control oneDatePicker', 'id' => 'start_date', 'required' => 'required']) !!}
    {!! Form::oneDate('end_date',array("name"=>trans('privateCbs.end_date'),"description"=>trans('privateCbs.endDateDescription')), isset($cb) && $cb->end_date!=null ? $cb->end_date  : '', ['class' => 'form-control oneDatePicker', 'id' => 'end_date']) !!}
    @if(ONE::actionType('cbs') == 'show')
        @if(!empty($cb->start_topic)) {!! Form::oneDate('start_topic',array("name"=>trans('privateCbs.start_topic'),"description"=>trans('privateCbs.startTopicDescription')), isset($cb) && $cb->start_topic!=null ? $cb->start_topic  : date('Y-m-d H:i'), ['class' => 'form-control oneDatePicker', 'id' => 'start_topic']) !!}@endif
        @if(!empty($cb->end_topic)) {!! Form::oneDate('end_topic',array("name"=>trans('privateCbs.end_topic'),"description"=>trans('privateCbs.endTopicDescription')), isset($cb) && $cb->end_topic!=null ? $cb->end_topic  : date('Y-m-d H:i'), ['class' => 'form-control oneDatePicker', 'id' => 'end_topic']) !!}@endif
        @if(!empty($cb->start_topic_edit)) {!! Form::oneDate('start_topic_edit',array("name"=>trans('privateCbs.start_topic_edit'),"description"=>trans('privateCbs.startTopicEditDescription')), isset($cb) && $cb->start_topic_edit!=null ? $cb->start_topic_edit  : date('Y-m-d H:i'), ['class' => 'form-control oneDatePicker', 'id' => 'start_topic_edit']) !!}@endif
        @if(!empty($cb->end_topic_edit)) {!! Form::oneDate('end_topic_edit',array("name"=>trans('privateCbs.end_topic_edit'),"description"=>trans('privateCbs.endTopicEditDescription')), isset($cb) && $cb->end_topic_edit!=null ? $cb->end_topic_edit  : date('Y-m-d H:i'), ['class' => 'form-control oneDatePicker', 'id' => 'end_topic_edit']) !!}@endif
    @endif
    {!! Form::oneSelect('page_key', ['name' => trans('privateCbs.page'),'description' =>trans("privateCbs.pageDescription")],
                        !empty($contentListType) ? $contentListType : [],
                        isset($cb->page_key) ? $cb->page_key:null,                 /* id da pÃ¡gina q virÃ¡... se houver id show/edit  */
                        isset($pageName)? $pageName : null,
                        ['class' => 'form-control'] ) !!}

    @if(ONE::actionType('cbs') == 'show')
        @if(!empty($cbFilter))
            <dt>{{trans('privateCbs.status_filter_title')}}</dt>
            <span class="help-block oneform-help-block-show" style="margin:1px 0px 5px;font-size:10px;">{{trans('privateCbs.status_filter_description')}}</span>
            @if(!empty($cbFilter))
                @foreach($cbFilter as $key => $filter)
                    <p><dd><ul style="list-style-type:circle"><li>{{$filter}}</li></ul></dd></p>
                @endforeach
            @endif
        @endif
    @endif

    {!! Form::hidden('cb_key', isset($cb) ? $cb->cb_key : 0, ['id' => 'cb_key']) !!}
    {!! Form::hidden('parent_cb_id', isset($cb) ? $cb->parent_cb_id : 0, ['id' => 'parent_cb_id']) !!}
    <input type="hidden" name="configurations" value="@php echo serialize($cbConfigurations); @endphp">


    @if(ONE::actionType('cbs') == 'edit')
        <?php
        $topicOptions = collect($cb->configurations)->where('code', '=', 'allow_filter_status')->first();
        ?>
        @if($topicOptions != null)
            @if(!empty($cbFilter))
                <div class="row">
                    <div class="col-sm-12 col-md-6">
                        <label for="statlbl">
                            Choose Status Filter
                            <select id="statlbl" class="statusTypes form-control" style="width: 100%;" name="filters[]" multiple="multiple">
                                @foreach($statusTypes  as $keyfilter => $filter)
                                    @if(!empty($cbFilter))
                                        <option value="{{$keyfilter}}"
                                                @foreach($cbFilter as $key =>$item)
                                                @if($key == $keyfilter)
                                                selected
                                                @endif
                                                @endforeach>
                                            {{$filter}}
                                        </option>
                                    @else
                                        <option value="{{$keyfilter}}">{{$filter}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </label>
                    </div>
                </div>
            @else
                <div class="row">

                    <div class="col-sm-12 col-md-6">
                        <label for="statlbl">
                            Choose Status Filter
                            <select id="statlbl" class="statusTypes form-control" style="width: 100%;" name="filters[]" multiple="multiple">
                                @foreach($statusTypes  as $keyfilter => $filter)
                                    <option value="{{$keyfilter}}">{{$filter}}</option>
                                @endforeach
                            </select>
                        </label>
                    </div>
                </div>
            @endif
            <script>
                $(document).ready(function () {
                    $('.statusTypes').select2({
                        width: 'resolve',
                        placeholder: 'Select Status:',
                        allowClear: true
                    });
                });
            </script>
        @endif
    @endif
    @if(ONE::actionType('cbs') == 'edit')
        <dt><label>{{trans('private.datetimepicker_addictional_configuration')}}</label></dt>
        <div class="container p-0">
            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col">
                            <button
                                    style="width: 100%; border-radius: 0;"
                                    class="btn btn-flat empatia" type="button"
                                    data-toggle="collapse"
                                    data-target="#topicDate" id="btnTopic" aria-expanded="false"
                                    aria-controls="topicDate">
                                {{trans('private.datetimepicker_configuration_addictional_topic')}}
                                <i class="fa fa-angle-down pull-right" aria-hidden="true"></i>
                            </button>
                        </div>
                        <div class="col">
                            <button
                                    style="width: 100%; border-radius: 0;"
                                    class="btn btn-flat empatia"
                                    type="button" data-toggle="collapse"
                                    data-target="#voteDate" id="btnVote" aria-expanded="false"
                                    aria-controls="voteDate">
                                {{trans('private.datetimepicker_configuration_addictional_vote')}}
                                <i class="fa fa-angle-down  pull-right" aria-hidden="true"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 mt-2">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card-body"
                                 style="padding-top: 5px; padding-bottom: 0px;">
                                <div class="collapse" id="topicDate">
                                    <div class="card-block">
                                        <div class=col-md-12'>
                                            <label>{{ trans('private.datetimepicker_start_topic') }}</label>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <div class='input-group date'
                                                             id='datetimepicker1'>
                                                            <input type='text'
                                                                   value="@if(!empty($cb->start_topic)){{old('start_topic',$cb->start_topic)}}@endif"
                                                                   class="form-control"
                                                                   name="start_topic"/>
                                                            <span class="input-group-addon">
                                                                <i class="fa fa-calendar" aria-hidden="true"></i>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class=col-md-12'>
                                                <label>{{ trans('private.datetimepicker_end_topic') }}</label>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <div class='input-group date'
                                                                 id='datetimepicker2'>
                                                                <input type='text'
                                                                       value="@if(!empty($cb->end_topic)){{old('end_topic',$cb->end_topic)}}@endif"
                                                                       class="form-control"
                                                                       name="end_topic">
                                                                <span class="input-group-addon">
                                                                <i class="fa fa-calendar" aria-hidden="true"></i>
                                                            </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class=col-md-12'>
                                                <label>{{ trans('private.datetimepicker_range_topic_edit') }}</label>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <div class='input-group date'
                                                                 id='datetimepicker3'>
                                                                <input type='text'
                                                                       value="@if(!empty($cb->start_topic_edit)){{old('start_topic_edit',$cb->start_topic_edit)}}@endif"
                                                                       class="form-control"
                                                                       name="start_topic_edit">
                                                                <span class="input-group-addon">
                                                                <i class="fa fa-calendar" aria-hidden="true"></i>
                                                            </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <div class='input-group date'
                                                                 id='datetimepicker4'>
                                                                <input type='text'
                                                                       value="@if(!empty($cb->end_topic_edit)){{old('end_topic_edit',$cb->end_topic_edit)}}@endif"
                                                                       class="form-control"
                                                                       name="end_topic_edit">
                                                                <span class="input-group-addon">
                                                                <i class="fa fa-calendar" aria-hidden="true"></i>
                                                            </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card-body"
                                 style="padding-top: 5px; padding-bottom: 0px;">

                                <div class="collapse" id="voteDate">
                                    <div class="card-block">
                                        <div class=col-md-12'>
                                            <label>{{ trans('private.datetimepicker_start_vote') }}</label>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <div class='input-group date'
                                                             id='datetimepicker5'>
                                                            <input type='text'
                                                                   value="@if(!empty($cb->start_vote)){{old('start_vote',$cb->start_vote)}}@endif"
                                                                   class="form-control"
                                                                   name="start_vote"/>
                                                            <span class="input-group-addon">
                                                                <i class="fa fa-calendar" aria-hidden="true"></i>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class=col-md-12'>
                                            <label>{{ trans('private.datetimepicker_end_vote') }}</label>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <div class='input-group date'
                                                             id='datetimepicker6'>
                                                            <input type='text'
                                                                   value="@if(!empty($cb->end_vote)){{old('end_vote',$cb->end_vote)}}@endif"
                                                                   class="form-control"
                                                                   name="end_vote"/>
                                                            <span class="input-group-addon">
                                                                <i class="fa fa-calendar" aria-hidden="true"></i>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{--Style and Script datepick   --}}
        <script>

            $(function () {
                $('#datetimepicker1').datetimepicker(
                    {
                        format:'YYYY-MM-DD HH:mm:ss',
                        minDate: getFormattedDate(new Date())
                    }
                );
                $('#datetimepicker2').datetimepicker({
                    useCurrent: false, //Important! See issue #1075

                    format:'YYYY-MM-DD HH:mm:ss',
                    minDate: getFormattedDate(new Date())

                });
                $('#datetimepicker5').datetimepicker(
                    {
                        format:'YYYY-MM-DD HH:mm:ss',
                        minDate: getFormattedDate(new Date())
                    }
                );
                $('#datetimepicker6').datetimepicker({
                    useCurrent: false, //Important! See issue #1075,

                    format:'YYYY-MM-DD HH:mm:ss',
                    minDate: getFormattedDate(new Date())

                });
                $('#datetimepicker3').datetimepicker(
                    {
                        format:'YYYY-MM-DD HH:mm:ss',
                        minDate: getFormattedDate(new Date())
                    }
                );
                $('#datetimepicker4').datetimepicker({
                    useCurrent: false, //Important! See issue #1075

                    format:'YYYY-MM-DD HH:mm:ss',
                    minDate: getFormattedDate(new Date())

                });

                $("#datetimepicker1").on("dp.change", function (e) {
                    $('#datetimepicker2').data("DateTimePicker").minDate(e.date);
                });
                $("#datetimepicker2").on("dp.change", function (e) {
                    $('#datetimepicker1').data("DateTimePicker").maxDate(e.date);
                });
                $("#datetimepicker3").on("dp.change", function (e) {
                    $('#datetimepicker4').data("DateTimePicker").minDate(e.date);
                });
                $("#datetimepicker4").on("dp.change", function (e) {
                    $('#datetimepicker3').data("DateTimePicker").maxDate(e.date);
                });

                $("#datetimepicker5").on("dp.change", function (e) {
                    $('#datetimepicker6').data("DateTimePicker").minDate(e.date);
                });
                $("#datetimepicker6").on("dp.change", function (e) {
                    $('#datetimepicker5').data("DateTimePicker").maxDate(e.date);
                });

                function getFormattedDate(date) {
                    var day = date.getDate();
                    var month = date.getMonth() + 1;
                    var year = date.getFullYear().toString().slice(2);
                    var output =  year + '-' + month + '-' + day;
                }
            });

        </script>
    @endif
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
        <div class="col-xl-3 col-sm-6 col-12">
            <div class="info-box">
                <span class="info-box-icon bg-yellow"><i class="fa fa-comments"></i></span>
                <div class="info-box-content">
                    {{ trans('privateCbs.total_comments') }}
                    <span class="info-box-number comments">{{$cb->statistics->posts ?? 0}}</span>
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

        <!-- Cb Check list -->
        @if(ONE::checkCBPermissions($cb->cb_key, "participation_details"))
            <div class="row">
                <div class="col-xl-12 ">
                    @include('private.cbs.cbCheckList')
                </div>
            </div>
        @endif


        <div class="row">
            <div class="col-xl-12 ">
                @php $type= isset($type)?$type:null; @endphp
                @if ($subpad)
                    <a href="{{action('SecondCycleController@manageCb', ['type' => "project_2c",'cbKey' => $rootCbKey])}}"
                       class="btn btn-flat empatia"><i class="fa fa-arrow-left"></i> Voltar</a>
                @else
                    <a href="{{action('CbsController@indexManager','typeFilter='.$type)}}" class="btn btn-flat empatia"><i
                                class="fa fa-arrow-left"></i> Voltar</a>
                @endif
            </div>
        </div>
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
        <div class="modal fade" tabindex="-1" role="dialog" id="createCbsTemplateModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="card-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">{{trans('privateCbs.create_template')}}</h4>
                    </div>
                    <div class="modal-body" style="min-height: 10vh;">

                        <div id="create_template">
                            <label for="templateName">{{trans('privateCbs.template_name')}}</label>
                            <input type="text" id="templateName" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" onclick="storeTemplate()" class="btn btn-success"
                                required>{{trans("privateCbs.save")}}</button>
                        <button type="button" class="btn btn-secondary"
                                data-dismiss="modal">{{trans("privateCbs.cancel")}}</button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->

        <!-- Add Moderator (Initial Hidden / shows on click privateCbs.addModerator) -->



        <!-- update status modal -->
        <div class="modal fade" tabindex="-1" role="dialog" id="updateStatusModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="card-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
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
                                        <option selected="selected"
                                                value="">{{trans('privateCbs.select_value')}}</option>
                                        @foreach($statusTypes as $key => $statusType)
                                            <option value="{{$key}}">{{$statusType}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="contentStatusComment">{{trans('privateCbs.private_comment')}}</label>
                                    <textarea class="form-control" rows="5" id="contentStatusComment"
                                              name="contentStatusComment" style="resize: none;"></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="contentStatusPublicComment">{{trans('privateCbs.public_comment')}}</label>
                                    <textarea class="form-control" rows="5" id="contentStatusPublicComment"
                                              name="contentStatusPublicComment" style="resize: none;"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                                id="closeUpdateStatus">{{trans("privateCbs.close")}}</button>
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <button type="button" class="btn btn-primary"
                                id="updateStatus">{{trans("privateCbs.save_changes")}}</button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->


        <!-- status history modal -->
        <div class="modal fade" tabindex="-1" role="dialog" id="statusHistoryModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="card-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">{{trans('privateQuestionOption.status_history')}}</h4>
                    </div>
                    <div class="modal-body" style="overflow-y: scroll;max-height: 50vh;">
                        <div id="statusHistory">

                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                                data-dismiss="modal">{{trans("privateCbs.close")}}</button>
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
        function showStatusHistory(topicKey) {
            $.ajax({
                method: 'POST', // Type of response and matches what we said in the route
                url: '{{action("TopicController@statusHistory")}}', // This is the url we gave in the route
                data: {
                    topicKey: topicKey
                }, // a JSON object to send back
                success: function (response) { // What to do if we succeed
                    if (response != 'false') {
                        $('#statusHistory').html(response);
                        $('#statusHistoryModal').modal('show');
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                }
            });
        }

        function updateStatus(topicKey) {
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
                    if ($(this).val().length > 0) {
                        allVals[$(this).attr('name')] = $(this).val();
                    }
                });
                $('#updateStatusModal textarea').each(function () {
                    if ($(this).val().length > 0) {
                        allVals[$(this).attr('name')] = $(this).val();
                    }
                });
                $('#updateStatusModal select').each(function () {
                    if ($(this).val().length > 0) {
                        $(this).closest('.form-group').removeClass('has-error');
                        allVals[$(this).attr('name')] = $(this).val();
                    } else {
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

                                toastr.success('{{ trans('privateCbs.update_topic_status_ok') }}', '', {
                                    timeOut: 3000,
                                    positionClass: "toast-bottom-right"
                                });
                            }
                            $('#updateStatusModal').modal('hide');
                        },
                        error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                            $('#updateStatusModal').modal('hide');
                            toastr.error('{{ trans('privateCbs.error_updating_state_or_sending_email_to_user') }}', '', {
                                timeOut: 3000,
                                positionClass: "toast-bottom-right"
                            });

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

        function storeTemplate() {
            if ($("#templateName").val() != "") {
                $('#createCbsTemplateModal').modal('hide');
                $.ajax({
                    'url': '{{ action('CbsController@storeCbTemplate', ['type' => isset($type)? $type : null,'cbKey' => isset($cb) ? $cb->cb_key : null]) }}',
                    'data': {'templateName': $("#templateName").val()},
                    'method': 'post',
                    'dataType': 'json',
                    error: function () {
                        console.log("error");
                    },
                    complete: function () {

                    },
                    success: function (response) {
                        toastr.success('{!! trans('privateCbs.cb_template_stored') !!}');
                    }
                })
            } else {
                toastr.error("{!! preg_replace( "/\r|\n/", "", htmlentities(trans("privateCbs.name_required_on_modal"),ENT_QUOTES)) !!} #1!");
                return false;
            }
        }

        @if(!empty($cbKey))
        //Check List
        $("#submitCheckList, #line").hide();

        //update checkbox
        function checkChanged(checkboxElem, checklist_key) {
            var checked = false;
            var state = 'none';
            if (checkboxElem.checked) {
                $('#check_' + checklist_key).css('text-decoration', 'line-through');
                checked = true;
                state = 'done';
            }
            else {
                $('#check_' + checklist_key).css('text-decoration', 'none');
                $('#check').attr('value', 'none');
            }

            updateChecklistItem(checked, checklist_key, state);
        }

        function updateChecklistItem(checked, checklist_key, state) {
            $.ajax({
                method: 'get', // Type of response and matches what we said in the route
                url: "{{action('CbsController@updateChecklistItem')}}", // This is the url we gave in the route
                data: {
                    'checklist_key': checklist_key,
                    'checked': checked,
                    'state': state
                }, // a JSON object to send back
                success: function (response) { // What to do if we succeed
                    if (response != 'false') {
                        toastr.success('{{ trans('privateCbs.update_checkList_state_ok') }}', '', {
                            timeOut: 3000,
                            positionClass: "toast-bottom-right"
                        });
                        location.reload();
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                    $('#updateStatusModal').modal('hide');
                    toastr.error('{{ trans('privateCbs.error_update_checklist_state_ok') }}', '', {
                        timeOut: 3000,
                        positionClass: "toast-bottom-right"
                    });

                }
            });
        }


        //add Cb Check list
        function addChecklist() {
            $.ajax({
                type: "GET",
                url: '{{action("CbsController@addCheckList")}}',
                data: {
                    "_token": "{{ csrf_token() }}",
                },
                success: function (response) {
                    $('#addCheckListRow').append(response);
                    $("#submitCheckList, #line").show();
                },
                error: function (response) {
                }
            });
        };


        //create checklist
        function checkChangedNewItem(checkboxElem) {
            if (checkboxElem.checked) {
                checkboxElem.value = 'done';
            } else {
                checkboxElem.value = 'none';
            }
        }

        $("#checkList").submit(function () {
            var checked = [];
            $("input[name='checkList_checkbox[]']").each(function () {
                checked.push(($(this).val()));
            });

            var state = [];
            $(".append_state").each(function () {
                state.push(($(this).attr('name')));
            });

            var text = $('input[name="checkList_text[]"]').map(function () {
                return this.value;
            }).get();

            $.ajax({
                method: 'GET',
                url: "{{action('CbsController@createChecklistItem')}}",
                data: {
                    "_token": "{{ csrf_token() }}",
                    "text": text,
                    "checked": checked,
                    "state": state,
                    "cbKey": '{{ $cbKey }}',
                    "entityKey": '{{ $entityKey }}'
                },
                success: function (response) {
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    toastr.error('{{ trans('privateCbs.error_updating_state_or_sending_email_to_user') }}', '', {
                        timeOut: 3000,
                        positionClass: "toast-bottom-right"
                    });
                }
            });
        });

        @endif

        function selectNewItem(element) {
            element.parentElement.parentElement.firstElementChild.innerText = element.text;
            element.parentElement.parentElement.firstElementChild.name = element.getAttribute("name");
        }

        //remove new check list
        function removeNewCheckList(element) {
            $(element).closest('#addCheckList').remove();
        }

        //remove check list from data base
        function removeCheckList(checklist_key) {
            $.ajax({
                method: 'get', // Type of response and matches what we said in the route
                url: "{{action('CbsController@removeCheckListItem')}}", // This is the url we gave in the route
                data: {
                    "_token": "{{ csrf_token() }}",
                    'checklist_key': checklist_key,
                }, // a JSON object to send back
                success: function (response) { // What to do if we succeed
                    toastr.success('{{ trans('privateCbs.remove_checklist_ok') }}', '', {
                        timeOut: 3000,
                        positionClass: "toast-bottom-right"
                    });
                    location.reload();
                },
                error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail

                    toastr.error('{{ trans('privateCbs.error_remove_checklist_ko') }}', '', {
                        timeOut: 3000,
                        positionClass: "toast-bottom-right"
                    });
                }
            });
        }


    </script>
@endsection

