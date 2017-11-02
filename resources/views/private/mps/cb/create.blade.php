@extends('private._private.index')

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
@section('content')
    <div class="box box-primary">
        <div class="box-body">            <!-- Form -->
            @php
            $form = ONE::form('node')
                ->settings(["model" => isset($operator) ? $operator : null,'id' => $operator->operator_key ?? null])
                ->show('MPCbsController@edit', 'MPCbsController@delete', ['operator_key' => isset($operator) ? $operator->operator_key : null], 'MPsController@showConfigurations', ['mp_key' => isset($operator) ? $operator->mp->mp_key : null])
                ->create('MPCbsController@store', 'MPsController@showConfigurations', ['mp_key' => isset($operator) ? $operator->mp->mp_key : null])
                ->edit('MPCbsController@update', 'MPCbsController@index', ['operator_key' => isset($operator) ? $operator->operator_key : null,'mp_key' => isset($operator) ? $operator->mp->mp_key : null])
                ->open();
            @endphp
            {!! Form::hidden('operator_key', $operator->operator_key ?? null) !!}
            {!! Form::hidden('mp_key', $operator->mp->mp_key ?? null) !!}
            {!! Form::hidden('operator_type', $operator->operator_type->code ?? null) !!}
            <div id="cbWizard">
                <div class="navbar"  style="margin:0px 15px 10px;">

                    <div class="navbar-inner">
                        <ul id='cbMainStepper' class="nav nav-pills">
                            <li class="active"><a href="#step_details" data-toggle="tab"  data-step="details">{{ trans('privateCbs.details') }}</a></li>
                            @php $i = 0; @endphp
                            @foreach($configurations as $configuration)
                                <li><a href="#step_configuration_{{$i}}" data-toggle="tab" data-step="{{ $configuration->code }}"  class="disabledTab" >{{ $configuration->title }}</a></li>
                                @php $i++; @endphp
                            @endforeach
                            <li><a href="#step_parameters" data-toggle="tab" data-step="parameters" class="disabledTab">{{ trans("privateCbs.parameters") }}</a></li>
                            <li><a href="#step_moderators" data-toggle="tab" data-step="moderators" class="disabledTab" >{{ trans("privateCbs.moderators") }}</a></li>
                        </ul>
                    </div>
                </div>

                <div class="tab-content">
                    <div class="tab-pane fade in active" id="step_details" step="details">
                        <!-- Description -->
                        <span class="form-text" style="font-weight: bold">{{ trans('privateCbs.details') }}</span>

                        <div class="well">
                            @include('private.mps.cb.wizard.stepDetails')
                        </div>

                        <!-- Buttons: Next -->
                        <div class="row">
                            <div class="col-md-12">
                                <a class="btn btn-flat empatia next pull-right" href="#">{!! trans("privateCbs.next") !!} <i class="fa fa-step-forward" aria-hidden="true"></i></a>
                                <br/><br/>
                            </div>
                        </div>

                    </div>

                    @php $i=0; @endphp
                    @foreach($configurations as $configuration)
                        <div class="tab-pane fade" id="step_configuration_{{$i}}">

                            <!-- Description -->
                            <span class="form-text" style="font-weight: bold">{{ trans('privateCbs.configurations') }} > {{ $configuration->title }}</span>

                            <div class="well">
                                @include('private.mps.cb.wizard.stepConfigurations')
                            </div>

                            <!-- Buttons: Previous && Next -->
                            <a class="btn btn-flat empatia prev" href="#"><i class="fa fa-step-backward" aria-hidden="true"></i> {!! trans("privateCbs.previous") !!}</a>
                            <a class="btn btn-flat empatia next pull-right" href="#">{!! trans("privateCbs.next") !!} <i class="fa fa-step-forward" aria-hidden="true"></i></a>
                        </div>
                        @php $i++; @endphp
                    @endforeach

                    <div class="tab-pane fade" id="step_parameters">
                        <!-- Description -->
                        <span class="form-text" style="font-weight: bold;">{{ trans("privateCbs.parameters") }}</span>

                        <div class="well">
                            @include('private.mps.cb.wizard.stepParameters')
                        </div>

                        <!-- Buttons: Previous && Next -->
                        <a class="btn btn-flat empatia prev " href="#"><i class="fa fa-step-backward" aria-hidden="true"></i> {!! trans("privateCbs.previous") !!}</a>
                        <a class="btn btn-flat empatia next pull-right" href="#">{!! trans("privateCbs.next") !!} <i class="fa fa-step-forward" aria-hidden="true"></i> </a>
                    </div>

                    <div class="tab-pane fade" id="step_moderators">
                        <!-- Description -->
                        <span class="form-text" style="font-weight: bold">{{ trans("privateCbs.moderators") }}</span>

                        <div class="well">
                            @include('private.mps.cb.wizard.stepModerators')
                        </div>

                        <!-- Buttons: Previous -->
                        <a class="btn btn-flat empatia prev " href="#"><i class="fa fa-step-backward" aria-hidden="true"></i> {!! trans("privateCbs.previous") !!}</a>
                        @if(ONE::actionType('node') != 'show')
                            <button type="submit" class="btn btn-flat empatia pull-right" form="node">{{trans("privateCbs.create")}} </button>
                        @endif
                    </div>

                </div>

                <hr>

            </div>

            <div id="modalGroup"></div>

            {!! $form->make() !!}
        </div>

    </div>

    @if(ONE::actionType('node') != 'create')
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
                            @if(ONE::actionType('node') == 'edit')
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
    @endif

@endsection

@section('scripts')
    <script>

        $(function() {
            getSidebar('{{ action("OneController@getSidebar") }}', 'configurations', '{{isset($operator) ? $operator->mp->mp_key : null}}', 'mp_configurations' )
        });

        $('#parameterUpdateForm').submit(function(e) {
            $.ajax({
                url: '{{action("MPCbsController@updateParameter")}}',
                method: 'POST',
                data: $('#parameterUpdateForm').serialize(),
                success: function (response) {
                    if (response != 'false') {
                        //                    $("#parameterItemId_"+parameterId).detach();
                    }
                },
                error: function (msg) {
                    console.log(msg);
                }
            });
            e.preventDefault();
            $('#modalParameterUpdate').modal('hide');
            return false;
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


        /* Stepper Engine [create.blade.php] --------------------------- START */
        $('#cbWizard .next').click(function(){
            var stepDiv = $(this).parents('.tab-pane').next().attr("id");
            if( stepDiv ==  "step_moderators"){
                $('form').find('input[type=submit]').show();
            }

            if( stepDiv == "step_configuration_0" && $("#title").val() =="" ){
                toastr.error("{!! preg_replace( "/\r|\n/", "", htmlentities(trans("privateCbs.title_required_on_tab"),ENT_QUOTES)) !!} #1!");

                return false;
            } else {
                var nextId = stepDiv;
                $('[href=#'+nextId+']').removeClass('disabledTab');
                $('[href=#'+nextId+']').tab('show');
                return false;
            }
        });

        $('#cbWizard .prev').click(function(){
            var nextId = $(this).parents('.tab-pane').prev().attr("id");
            $('[href=#'+nextId+']').tab('show');
            return false;
        });

        $('#cbWizard a[data-toggle="tab"]').on('shown.bs.tab', function (e) {

            //update progress
            var step = $(e.target).data('step');
            var percent = (parseInt(step) / {{ count($configurations)+5 }} ) * 100;

            //
            if( $("#title").val() =="" ){
                toastr.error("{!! preg_replace( "/\r|\n/", "", htmlentities(trans("privateCbs.title_required_on_tab"),ENT_QUOTES)) !!} #1!");
            }

            $('.progress-bar').css({width: percent + '%'});
            $('.progress-bar').text("{!! str_replace(array("\r", "\n"), '',  trans("privateCbs.step")) !!} " + step + " of {{ count($configurations)+5 }}");


            if(step == 'moderators'){
                $('form').find('input[type=submit]').show();
            }
        });

        $('.first').click(function(){
            $('#cbWizard a:first').tab('show')
        });
        /* Stepper Engine [create.blade.php] --------------------------- END */

        // Hide submit button
        $('form').find('input[type=submit]').hide();

        // Disable click events for class disableLink
        $('.disableLink').on('click', function(e) { e.preventDefault(); });
    </script>
@endsection