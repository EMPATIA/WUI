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
    <div class="box box-primary">
        <div class="">

            <!-- Form -->
            @php
            $form = ONE::form('cbs')
                ->settings(["model" => isset($cb) ? $cb : null])
                ->show('CbsController@edit', 'CbsController@delete', ['type' => isset($type)? $type : null,'cbKey' => isset($cb) ? $cb->cb_key : null], 'CbsController@indexManager', ['type' => isset($type)? $type : null,'id' => isset($cb) ? $cb->cb_key : null])
                ->create('CbsController@store', null, ['type' => isset($type)?$type:null,'id' => isset($cb) ? $cb->cb_key : null])
                ->edit('CbsController@advancedUpdate', 'CbsController@show', ['type' => isset($type)? $type : null,'id' => isset($cb) ? $cb->cb_key : null])
                ->open();
            @endphp

            {!! Form::hidden('cb_key', isset($cbKey) ? $cbKey : null, ['id' => 'cb_key']) !!}
            {!! Form::hidden('parent_cb_id', isset($cb) ? $cb->parent_cb_id : 0, ['id' => 'parent_cb_id']) !!}

            <div id="cbWizard">
                <div class=""  style="">
                    <div class="cb-empatia-wrapper">

                        <button type="button" onclick="getTemplates()" class="btn btn-flat btn-submit" data-toggle="modal" data-target="#useTemplate" style="margin-bottom: 10px">{{ trans('privateCbs.use_template') }}</button>

                        <a type="" class="btn-flat btn-cancel pull-right" href="{{action('CbsController@indexManager')}}">{!! trans("privateCbs.cancel") !!}</a>
                   <br>

                    <hr>
                </div>
                    <br>
                    <div class="navbar-inner">
                        <ul id='cbMainStepper' class="nav nav-pills">
                            <li @if(!isset($step)) class="active" @endif><a href="#step1" data-toggle="tab"  data-step="1" class="disabledTab">{{ trans('privateCbs.details') }}</a></li>
                            <li class="step-arrow"><i class="fa fa-arrow-right"></i></li>
                            @php $i = 0; @endphp
                            @foreach($configurations as $configuration)
                                <li><a href="#step{{ 2+$i }}" data-toggle="tab" data-step="{{ 2+$i }}"  class="disabledTab" >{{ $configuration->title }}</a></li>
                                <li  class="step-arrow"><i class="fa fa-arrow-right"></i></li>
                                @php $i++; @endphp
                            @endforeach
                            <li @if(isset($step) and $step == 'param') class="active" @endif><a href="#step{{ 2+$i }}" data-toggle="tab" data-step="{{ 2+$i }}" class="disabledTab">{{ trans("privateCbs.parameters") }}</a></li>
                            <li  class="step-arrow"><i class="fa fa-arrow-right"></i></li>
                            <li @if(isset($step) and $step == 'votes') class="active" @endif><a href="#step{{ 3+$i }}" data-toggle="tab" data-step="{{ 3+$i }}" class="disabledTab" >{{ trans("privateCbs.votes") }}</a></li>
                            <li  class="step-arrow"><i class="fa fa-arrow-right"></i></li>
                            <li @if(isset($step) and $step == 'moderators') class="active" @endif><a href="#step{{ 4+$i }}" data-toggle="tab" data-step="{{ 4+$i }}" class="disabledTab" >{{ trans("privateCbs.moderators") }}</a></li>
                        </ul>
                    </div>
                </div>

                <div class="tab-content">
                    <div class="tab-pane @if(!isset($step)) in active @endif" id="step1" step="1">
                        <!-- Description -->
                        <!-- <span class="help-block" style="font-weight: bold">{{ trans('privateCbs.details') }}</span> -->
                        <br>
                        <div class="well">
                            @include('private.cbs.wizard.step1')
                        </div>
                        <!-- Buttons: Next -->
                        <div class="row">
                            <div class="col-md-12">
                                <a class="btn btn-flat btn-next pull-right" href="#">{!! trans("privateCbs.next") !!} <i class="fa fa-step-forward" aria-hidden="true"></i></a>
                                <br/><br/>
                            </div>
                        </div>

                    </div>

                    @php $i = 0; @endphp
                    @foreach($configurations as $configuration)
                        <div class="tab-pane" id="step{{ 2+$i }}" style="margin-top:20px">

                            <!-- Description -->
                            <br>
                         <p class="help-block" style="font-weight: normal; font-size: 15px; color: #2EA7DE; margin-bottom: 15px;">{{ trans('privateCbs.configurations') }} <i class="fa fa-arrow-right" style="font-size: 10px"></i>  {{ $configuration->title }}</p>

                            <div class="well" style="height: 90%; ">
                                @include('private.cbs.wizard.step2')
                            </div>
                            <!-- Buttons: Previous && Next -->
                            <a class="btn btn-flat empatia btn-prev" href="#"><i class="fa fa-step-backward" aria-hidden="true"></i> {!! trans("privateCbs.previous") !!}</a>
                            <a class="btn btn-flat empatia btn-next pull-right" href="#">{!! trans("privateCbs.next") !!} <i class="fa fa-step-forward" aria-hidden="true"></i></a>
                        </div>
                        @php $i++; @endphp
                    @endforeach

                    <div class="tab-pane @if(isset($step) and $step == 'param') in active @endif" id="step{{ 2+$i }}" style="height: 90%">
                        <!-- Description -->
                        <!-- <span class="help-block" style="font-weight: bold;">{{ trans("privateCbs.parameters") }}</span> -->
                          <br>
                        <div class="well" style="height: 90%; ">
                            @include('private.cbs.wizard.step3')
                        </div>

                        <!-- Buttons: Previous && Next -->
                        <a class="btn btn-flat empatia btn-prev " href="#"><i class="fa fa-step-backward" aria-hidden="true"></i> {!! trans("privateCbs.previous") !!}</a>
                        <a class="btn btn-flat empatia btn-next pull-right" href="#">{!! trans("privateCbs.next") !!} <i class="fa fa-step-forward" aria-hidden="true"></i> </a>
                    </div>

                    <div class="tab-pane  @if(isset($step) and $step == 'votes') in active @endif" id="step{{ 3+$i }}" style="height: 90%">
                        <!-- Description -->
                        <!-- <span class="help-block" style="font-weight: bold;">{{ trans("privateCbs.votes") }}</span> -->
                           <br>
                        <div class="well" style="height: 90%; ">
                            @include('private.cbs.wizard.step4')
                        </div>

                        <!-- Buttons: Previous && Next -->
                        <a class="btn btn-flat empatia btn-prev " href="#"><i class="fa fa-step-backward" aria-hidden="true"></i> {!! trans("privateCbs.previous") !!}</a>
                        <a class="btn btn-flat empatia btn-next pull-right" href="#">{!! trans("privateCbs.next") !!} <i class="fa fa-step-forward" aria-hidden="true"></i></a>
                    </div>

                    <div class="tab-pane  @if(isset($step) and $step == 'moderators') in active @endif" id="step{{ 4+$i }}" style="height: 90%">
                        <!-- Description -->
                        <!-- <span class="help-block" style="font-weight: bold">{{ trans("privateCbs.moderators") }}</span> -->
                          <br>
                        <div class="well" style="height: 90%; ">
                            @include('private.cbs.wizard.step6')
                        </div>
                        <!-- Buttons: Previous && Next -->
                        <a class="btn btn-flat empatia btn-prev " href="#"><i class="fa fa-step-backward" aria-hidden="true"></i> {!! trans("privateCbs.previous") !!}</a>
                        <a class="btn btn-flat empatia pull-right" onclick="cbDetails()">{{trans("privateCbs.create")}}</a>
                        {{--<button type="submit" class="btn btn-flat empatia pull-right" form="cbs" >{{trans("privateCbs.create")}} </button>--}}
                    </div>




                </div>

                <hr>

            </div>

            <div id="modalGroup"></div>

            {!! $form->make() !!}
        </div>

    </div>

    <div class="modal fade" tabindex="-1" role="dialog" id="useTemplate" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="card-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">{{trans('privateCbs.select_template')}}</h4>
                </div>
                <div class="modal-body" style="min-height: 10vh;">
                    <div id="select_template_div">
                        <label for="select_template">{{trans('privateCbs.template_name')}}</label>
                        <select id="select_template" class="form-control">
                            <option value="">{{ trans('privateCbs.choose_option') }}</option>
                        </select>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" id="submit" class="btn btn-success" data-dismiss="modal" onclick="fetchTemplate()" disabled>{!! trans("privateCbs.use_template") !!}</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{trans("privateCbs.cancel")}}</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <!-- Modal Parameter to update -->
    <div class="modal fade" id="modalParameterUpdate" tabindex="-1" role="dialog">
        <form role="form" action="" method="post" name="parameterUpdateForm" id="parameterUpdateForm">
            <div class="modal-dialog">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="modal-content">
                    <div class="card-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">{{ trans("privateCbs.parameter") }}</h4>
                    </div>
                    <div class="modal-body" id="modalAddParameterBody">

                    </div>
                    <div class="modal-footer">
                        @if(ONE::actionType('cbs') == 'edit')
                            <button type="submit" form="parameterUpdateForm" class="btn btn-primary">{{trans("privateCbs.update_parameter")}}</button>
                        @endif
                        <button type="button" class="btn btn-secondary col-sm-2 pull-right" data-dismiss="modal"
                                id="frm_cancel">{{ trans("privateCbs.close") }}
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>





@endsection

@section('scripts')
    <!-- Plupload Javascript fix and bootstrap fix @ start -->
    <link rel="stylesheet" href="/bootstrap/plupload-fix/bootstrap.css">
    <script src="/bootstrap/plupload-fix/bootstrap.min.js"></script>
    <!-- Plupload Javascript fix and bootstrap fix @ End -->
    <script src="{{ asset('vendor/jildertmiedema/laravel-plupload/js/plupload.full.min.js') }}"></script>
    <script>

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

        /* Stepper Engine [create.blade.php] --------------------------- START */
        $('#cbWizard .btn-next').click(function(){
            var stepDiv = $(this).parents('.tab-pane').next().attr("id");

            if(stepDiv == "step{{ $i+2 }}"){
                createCb();
            }

//            if( stepDiv ==  "step7"){
//                $('form').find('input[type=submit]').show();
//            }

            if( stepDiv == "step2" && $("#title").val() =="" ){
                toastr.error("{!! preg_replace( "/\r|\n/", "", htmlentities(trans("privateCbs.titleRequiredOnTab"),ENT_QUOTES)) !!} #1!");

                return false;
            } else  if( stepDiv == "step2" && $("#start_date").val() =="" ){
                toastr.error("{!! preg_replace( "/\r|\n/", "", htmlentities(trans("privateCbs.start_dateRequiredOnTab"),ENT_QUOTES)) !!} #3!");
                return false;
            } else if(stepDiv == "step2" && $("#end_date").val() !== "" && $("#start_date").val() >= $("#end_date").val()) {
                toastr.error("{{ preg_replace( "/\r|\n/", "", htmlentities(trans("privateCbs.invalid_dates"),ENT_QUOTES)) }} #1!");
                return false;
            }

            else {
                var nextId = stepDiv;
                $('[href=#'+nextId+']').tab('show');
                return false;
            }
        });

        $('#cbWizard .btn-prev').click(function(){
            var nextId = $(this).parents('.tab-pane').prev().attr("id");
            $('[href=#'+nextId+']').tab('show');
            return false;
        })

        $('#cbWizard a[data-toggle="tab"]').on('shown.bs.tab', function (e) {

            //update progress
            var step = $(e.target).data('step');
            var percent = (parseInt(step) / {{ count($configurations)+5 }} ) * 100;

            //
            if( $("#title").val() =="" ){
                toastr.error("{!! preg_replace( "/\r|\n/", "", htmlentities(trans("privateCbs.titleRequiredOnTab"),ENT_QUOTES)) !!} #1!");
            }

            if( $("#start_date").val() =="" ){
                toastr.error("{!! preg_replace( "/\r|\n/", "", htmlentities(trans("privateCbs.start_dateRequiredOnTab"),ENT_QUOTES)) !!} #3!");
            }
            $('.progress-bar').css({width: percent + '%'});
            $('.progress-bar').text("{!! str_replace(array("\r", "\n"), '',  trans("privateCbs.step")) !!} " + step + " of {{ count($configurations)+5 }}");

//            if(step == 7){
//                $('form').find('input[type=submit]').show();
//            }
        })

        $("#select_template").change(function(){
            if($(this).val() != ""){
                $("#submit").removeAttr('disabled');
            }else{
                $("#submit").attr('disabled', 'disabled');
            }
        })

        function getTemplates(){
            $("#select_template").empty();
            $.ajax({
                'url': '{{action('CbsController@getAllTemplates')}}',
                'type': 'post',
                'data': {},
                success: function(response){

                    $("#select_template").append('<option value="">' + '{{ trans('privateCbs.choose_option') }}' + '</option>');
                    $.each(response, function(index, value){
                        $.each(value, function(index1, value1){
                            $("#select_template").append('<option value="' + value1.key + '">' + value1.name + '</option>');
                        })
                    })
                },
                error: function(jqXHR, textStatus, errorThrown){
                    console.log(JSON.stringify(jqXHR));
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                }
            })
        }

        function fetchTemplate() {

            var selected = $("#select_template").val();
            $.ajax({
                'url': '{{action('CbsController@getCbTemplate', $type)}}',
                'type': 'get',
                'data': {templateCbKey: selected},
                success: function (response) {
                    window.location.href = response;
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log(JSON.stringify(jqXHR));
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                }
            })
        }

        $('.first').click(function(){
            $('#cbWizard a:first').tab('show')
        })
        /* Stepper Engine [create.blade.php] --------------------------- END */

        // Hide submit button
        $('form').find('input[type=submit]').hide();

        // Disable click events for class disableLink
        $('.disableLink').on('click', function(e) { e.preventDefault(); });

        function createCb(){
            var name = $("#cbWizard").find("#title");

            if($("#cb_key").text() == ""){
                $.ajax({
                    'url':'{{action('CbsController@store', ['type' => isset($type)?$type:null,'parentCbKey'=> $parentCbKey , 'id' => isset($cb) ? $cb->cb_key : null])}}',
                    'method': 'post',
                    'data': $("form").serialize(),
                    success: function(response){
                        $("#cb_key").append(response.cbKey);
                        $("#parent_cb_id").append(response.parent_cb_id);
                    },
                    error: function(){}
                })
            }else{

                $.ajax({
                    'url':'{{action('CbsController@updateCb', ['type' => isset($type)?$type:null])}}'+'?cbKey='+$("#cb_key").text(),
                    'method': 'post',
                    'data': $("form").serialize(),
                    success: function(response){
                    },
                    error: function(){}
                })
            }
        }
        function cbDetails(){
            $.ajax({
                'url': '{{action('CbsController@getDetailsView', ['type' => isset($type)?$type:null])}}',
                'method' : 'get',
                'data' : {cbKey : $("#cb_key").val()},
                success: function(response){
                    window.location.href = response;
                },
                error: function(){}
            })
        }
    </script>
@endsection

@section('header_styles')
    <style>
        .disabledTab{
            pointer-events: none;
        }

        .progress-bar-green, .progress-bar-success {
            background-color: #62a351!important;
        }

        .nav-pills > li.active > a, .nav-pills > li.active > a:hover, .nav-pills > li.active > a:focus {
            border-top-color: #62a351!important;
            background-color: #62a351!important;
        }

        .navParameterWizard > li.disabled > a {
            color: #dedede!important;
        }
    </style>
@endsection
