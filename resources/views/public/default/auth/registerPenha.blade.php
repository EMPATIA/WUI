@section('styles')
    <style>
        @media (min-width: 576px){
            .modal-dialog {
            max-width: 90%;
            }
        }
    </style>
@endsection


@extends('public.default._layouts.index')

@php($demoPageTitle = ONE::transSite("register_title"))

@section('header_scripts')
    @if (!empty(Session::get('SITE-CONFIGURATION')['recaptcha_site_key']) &&  !empty(Session::get('SITE-CONFIGURATION')['recaptcha_secret_key']))
        <script src='https://www.google.com/recaptcha/api.js'></script>
    @endif
@endsection

@section('header_styles')

@include('public.default.user.cssOverrides')
    <style>
        .submit_register{
            text-align: center;
            padding: 5px 15px;
            line-height: 20px;
            display: block;
            width: 100%;
            cursor: pointer;
            background-color: #1d5ba6;
            border-radius: 0;
            color: #fff;
            font-size: 0.9rem;
            width: 100%;
        }
        .pointer-events-none{
            pointer-events: none;
            opacity: 0.4;
        }


        .submit-btn{
            background-color: {{ ONE::getSiteConfiguration("color_primary") }};
            text-decoration: none;
            color:white;
            border: 1px solid {{ ONE::getSiteConfiguration("color_primary") }};
            text-decoration: none;
        }

        .cancel-btn{
            background-color: #4c4c4c;
            text-decoration: none;
            color:white;
            border: 1px solid #4c4c4c;
            text-decoration: none;
        }

        .submit-btn:hover, .submit-btn:active, .submit-btn:focus{
            background-color: white;
            color: {{ ONE::getSiteConfiguration("color_primary") }};
            text-decoration:none;
        }

        .cancel-btn:hover, .cancel-btn:active, .cancel-btn:focus{
            background-color: white;
            color: #4c4c4c;
        }

        .modal-header {
            border-radius:0;
        }

        .modal-body {
            padding: 2rem 30px 0rem 30px;
        }

        .terms-conditions-wrapper {
            max-height: 800px;
            overflow-y: auto;
        }

        .modal-terms {
            max-width: 900px;
            width:80%;
        }

        .buttons-register-submit:hover {
            box-shadow: inset 0px 0px 0px 2px {{ ONE::getSiteConfiguration("color_primary") }}!important;
            background-color: #fff!important;
            color: {{ ONE::getSiteConfiguration("color_primary") }}!important;
        }

        .cancel-btn a:hover {
            color: {{ ONE::getSiteConfiguration("color_secondary") }}!important;
        }
    </style>
@endsection

@section('content')
    <!-- Title -->
    {{--<div class="container">--}}
    {{--<div class="row align-items-end idea-topic-title">--}}
    {{--<div clasS="col-lg-8 col-md-8 mx-auto title">--}}
    {{--<span style="margin-left: 10px">{{ ONE::transSite("register_fill_form") }}</span>--}}
    {{--</div>--}}
    {{--</div>--}}
    {{--</div>--}}

    <div class="container-fluid form-empatia white-bg">
        <div class="row">
            <div class="col-12">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-8 col-md-8 mx-auto">
                            <div class="row form-row">
                                <div style="padding: 0px" class="col-12 col-sm-8 col-md-8 col-lg-8">
                                    <br>
                                    <h4 style="font-size: 18px">{{ ONE::transSite("register_fill_form") }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid form-empatia light-grey-bg">
        <div class="row">
            <div class="col-12">
                <div class="container">
                    <form action="{{ URL::action('AuthController@verifyRegisterAndLogin') }}" method="POST" onsubmit="register()">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="withoutemail" value="{{ Session::get("SITE-CONFIGURATION.boolean_no_email_needed") }}">
                        @if ($errors->any())
                            <div class="row">
                                <div class="col-lg-8 col-md-8 mx-auto">
                                    <div class="field-wrapper alert alert-danger">
                                        <h3 class="form-title">{{ ONE::transSite("register_errors") }}</h3>
                                        <ul>
                                            @foreach ($errors->getBag('default')->get('registerError') as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                            @foreach ($errors->getBag('default')->get('password') as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                            @foreach ($errors->getBag('default')->get('auth.verifyRegisterAndLogin') as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                        @if($errors->getBag('default')->get('registerError'))
                                            <button type="button" class="alert-btn-inside" data-toggle="modal" data-target="#modal_error_unique">
                                                {{ ONE::transSite("register_send_message") }}
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-lg-8 col-md-8 mx-auto">

                                @if(isset($registerParameters))
                                    @if(!empty($registerParameters))
                                        <div class="row form-row">
                                            <div style="padding: 0px" class="col-12 col-sm-8 col-md-8 col-lg-8">
                                                <br>
                                                <h5 class="form-title">{{ ONE::transSite("register_personal_data") }}</h5>
                                            </div>
                                        </div>
                                        @foreach($registerParameters as $parameter)
                                            <div class="row white-bg form-row">
                                                @if($parameter['parameter_type_code'] == 'text')
                                                    <div clasS="col-lg-4 form-label">
                                                        {!! Form::label($parameter['parameter_user_type_key'], $parameter['name'] . ($parameter['mandatory'] == true ? ' *':null), array('class' => ($parameter['mandatory'] == true ? 'required label-required':null) )) !!}
                                                    </div>
                                                    <div clasS="col-lg-8">
                                                        <div class="form-group has-warning">
                                                            {!! Form::text($parameter['parameter_user_type_key'], $parameter['value'],  array($parameter['mandatory'] == true ? 'required' : null , 'class'=>'form-control'.($errors->has( $parameter['parameter_user_type_key'] ) ?  " input-error" : "" ), 'title' => $parameter['name'])) !!}
                                                            {{--<small id="titleHelp" class="form-text text-muted"> Sed volutpat erat tellus, id porttitor velit vehicula ac. Donec vestibulum tortor at varius tempor.</small>--}}
                                                        </div>
                                                    </div>
                                                @elseif($parameter['parameter_type_code'] == 'text_area')
                                                    <div clasS="col-lg-4 form-label">
                                                        {!! Form::label($parameter['parameter_user_type_key'], $parameter['name'] . ($parameter['mandatory'] == true ? ' *':null), array('class' => ($parameter['mandatory'] == true ? 'required label-required':null) )) !!}
                                                    </div>
                                                    <div clasS="col-lg-8">
                                                        <div class="form-group has-warning">
                                                            {!! Form::textarea($parameter['parameter_user_type_key'], $parameter['value'],  array('class'=>'form-control'.($errors->has($parameter['parameter_user_type_key']) ? " input-error": ""), 'required' => ($parameter['mandatory'] == true ? 'required' : null), 'title' => $parameter['name'])) !!}
                                                            {{--<small id="titleHelp" class="form-text text-muted"> Sed volutpat erat tellus, id porttitor velit vehicula ac. Donec vestibulum tortor at varius tempor.</small>--}}
                                                        </div>
                                                    </div>
                                                @elseif($parameter['parameter_type_code'] == 'numeric' || $parameter['parameter_type_code'] == 'mobile_phone')
                                                    <?php
                                                    $parameterInputOptions = array(
                                                        'class'=>'form-control',
                                                        'required' => ($parameter['mandatory'] == true ? 'required' : null),
                                                        'title' => $parameter['name']
                                                    );

                                                    if($parameter["code"]=="postal") {
                                                        $parameterInputOptions["min"] = "1000";
                                                        $parameterInputOptions["max"] = "9999";
                                                    }
                                                    ?>
                                                    <div clasS="col-lg-4 form-label">
                                                        {!! Form::label($parameter['parameter_user_type_key'], $parameter['name'] . ($parameter['mandatory'] == true ? ' *':null), array('class' => ($parameter['mandatory'] == true ? 'required label-required':null))) !!}
                                                    </div>
                                                    <div clasS="col-lg-8">
                                                        <div class="form-group has-warning">
                                                            {!! Form::number($parameter['parameter_user_type_key'], $parameter['value'],  $parameterInputOptions) !!}
                                                            {{-- <small id="titleHelp" class="form-text text-muted"> Sed volutpat erat tellus, id porttitor velit vehicula ac. Donec vestibulum tortor at varius tempor.</small> --}}
                                                        </div>
                                                    </div>
                                                @elseif($parameter['parameter_type_code'] == 'radio_buttons')
                                                    @if(count($parameter['parameter_user_options'])> 0)
                                                        <div clasS="col-lg-4 form-label">
                                                            {!! Form::label($parameter['parameter_user_type_key'], $parameter['name'] . ($parameter['mandatory'] == true ? ' *':null), array('class' => ($parameter['mandatory'] == true ? 'required label-required':null))) !!}
                                                        </div>
                                                        <div clasS="col-lg-8">
                                                            <div class="form-group has-warning">
                                                                @foreach($parameter['parameter_user_options'] as $option)
                                                                    <div class="radio">
                                                                        <label>
                                                                            <input role="radiogroup"
                                                                                   aria-label="{!! $parameter['name'] !!}" type="radio"
                                                                                   name="{{$parameter['parameter_user_type_key']}}"
                                                                                   title="{!! $parameter['name'] !!}"
                                                                                   value="{{$option['parameter_user_option_key']}}"
                                                                                   @if($parameter['mandatory']) required
                                                                                   @endif @if($option['selected']) checked @endif>{{$option['name']}}
                                                                        </label>
                                                                    </div>
                                                                @endforeach
                                                                {{--<small id="titleHelp" class="form-text text-muted"> Sed volutpat erat tellus, id porttitor velit vehicula ac. Donec vestibulum tortor at varius tempor.</small>--}}
                                                            </div>
                                                        </div>
                                                    @endif
                                                @elseif($parameter['parameter_type_code'] == 'check_box')
                                                    @if(count($parameter['parameter_user_options'])> 0)
                                                        <div clasS="col-lg-4 form-label">
                                                            {!! Form::label($parameter['name'], $parameter['name'] . ($parameter['mandatory'] == true ? ' *':null), array('class' => ($parameter['mandatory'] == true ? 'required label-required':null))) !!}
                                                        </div>
                                                        <div clasS="col-lg-8">
                                                            <div class="form-group has-warning">
                                                                @foreach($parameter['parameter_user_options'] as $option)
                                                                    <div class="checkbox">
                                                                        <label><input type="checkbox"
                                                                                      value="{{$option['parameter_user_option_key']}}"
                                                                                      title="{!! $parameter['name'] !!}"
                                                                                      name="{{$parameter['parameter_user_type_key']}}[]"
                                                                                      @if($parameter['mandatory']) required
                                                                                      @endif @if($option['selected'] || old($parameter['parameter_user_type_key'])==$option['parameter_user_option_key']) checked @endif>{{$option['name']}}
                                                                        </label>
                                                                    </div>
                                                                @endforeach
                                                                {{--<small id="titleHelp" class="form-text text-muted"> Sed volutpat erat tellus, id porttitor velit vehicula ac. Donec vestibulum tortor at varius tempor.</small>--}}
                                                            </div>
                                                        </div>
                                                    @endif


                                                @elseif($parameter['parameter_type_code'] == 'dropdown')
                                                    @if(count($parameter['parameter_user_options'])> 0)
                                                        <div clasS="col-lg-4 form-label">
                                                            {!! Form::label($parameter['parameter_user_type_key'], $parameter['name'] . ($parameter['mandatory'] == true ? ' *':null), array('class' => ($parameter['mandatory'] == true ? 'required label-required':null) )) !!}
                                                        </div>
                                                        <div clasS="col-lg-8">
                                                            <select class="form-control @if($errors->has( $parameter['parameter_user_type_key'] )) input-error @endif" id="{{$parameter['parameter_user_type_key']}}"
                                                                    name="{{$parameter['parameter_user_type_key']}}"
                                                                    @if($parameter['mandatory']) required @endif>
                                                                <option value="" selected>{{ONE::transSite("register_select_option")}}</option>
                                                                @foreach($parameter['parameter_user_options'] as $option)
                                                                    <option value="{{$option['parameter_user_option_key']}}"
                                                                            @if($option['selected'] || old($parameter['parameter_user_type_key'])==$option['parameter_user_option_key'] ) selected @endif>{{$option['name']}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    @endif
                                                @elseif($parameter['parameter_type_code'] == 'mobile')
                                                    <div clasS="col-lg-4 form-label">
                                                        {!! Form::label($parameter['name'], $parameter['name'] . (ONE::isEdit() && $parameter['mandatory']  ? "*" : ""), array('class' => 'label-required color-secundary ' .($parameter['mandatory'] == 1 ? 'required' :null ) )) !!}
                                                    </div>
                                                    <div clasS="col-lg-8">
                                                        <div class="form-group has-warning">
                                                            {!! Form::text($parameter['parameter_user_type_key'], $parameter['value'],  array($parameter['mandatory'] == true ? 'required' : null ,'class'=>'form-control oneFormed'.($errors->has( $parameter['parameter_user_type_key'] ) ?  " input-error" : "" ), 'title' => $parameter['name'])) !!}
                                                            {{--<small id="titleHelp" class="form-text text-muted"> Sed volutpat erat tellus, id porttitor velit vehicula ac. Donec vestibulum tortor at varius tempor.</small>--}}
                                                        </div>
                                                    </div>
                                                @elseif($parameter['code'] == 'birthday')
                                                    <div clasS="col-lg-4 form-label">
                                                        {!! Form::label($parameter['name'], $parameter['name'] . (ONE::isEdit() && $parameter['mandatory']  ? "*" : ""), array('class' => 'label-required color-secundary ' .($parameter['mandatory'] == 1 ? 'required' :null)  , 'format' => 'dd/mm/yyyy')) !!}
                                                    </div>
                                                    <div class="col-8">
                                                        <div class="form-group">
                                                            <input type="date" class="form-control" name="{{$parameter['parameter_user_type_key']}}" value="@if(!empty($errors)){{old($parameter['parameter_user_type_key'])}}@else{{$parameter['parameter_user_type_key']}}@endif" >
                                                        </div>
                                                    </div>
                                                @endif

                                            </div>
                                        @endforeach
                                    @endif
                                @endif
                                {{--<div class="row white-bg form-row">--}}
                                {{--<div clasS="offset-lg-4 col-lg-8 warning-info">--}}
                                {{--<i class="fa fa-exclamation-triangle" aria-hidden="true"></i> This information is not right--}}
                                {{--</div>--}}
                                {{--</div>--}}

                                <div class="row form-row">
                                    <div style="padding: 0px" class="col-12 col-sm-8 col-md-8 col-lg-8">
                                        <h5 class="form-title">{{ ONE::transSite("register_access_validation") }}</h5>
                                    </div>
                                </div>

                                <div class="row white-bg form-row">
                                    {{--<div class="offset-lg-4 col-lg-8 checkbox-buttons">--}}
                                        {{--<label style="color: #fec20c; font-weight: 600">{{ ONE::transSite("register_access_validation") }}</label>--}}
                                    {{--</div>--}}
                                    <div class="offset-lg-4 col-lg-8 checkbox-buttons" style="padding-bottom: 10px;">
                                        <input type="checkbox" id="thing1" name="checkboxThing-group" required>
                                        <label for="thing1">{{ ONE::transSite("register_accept") }} <a href="#" data-toggle="modal" data-target="#privacy_policy">{{ ONE::transSite("register_privacy_policy") }}</a></label>
                                    </div>
                                    <div class="offset-lg-4 col-lg-8 checkbox-buttons" style="padding-bottom: 10px;">
                                        <input type="checkbox" id="thing2" name="checkboxThing-group" required>
                                        <label for="thing2">{{ ONE::transSite("register_accept") }} <a href="#" data-toggle="modal" data-target="#terms_and_conditions">{{ ONE::transSite("register_service_terms") }}</a></label>
                                    </div>
                                    @if (!empty(Session::get('SITE-CONFIGURATION')['recaptcha_site_key']) &&  !empty(Session::get('SITE-CONFIGURATION')['recaptcha_secret_key']))
                                        <div clasS="col-lg-4 form-label">
                                            &nbsp; {{ONE::transSite('register_captcha_explanation')}}
                                        </div>
                                        <div clasS="col-lg-8" style="padding-bottom: 10px;padding-top: 10px;">
                                            <div class="form-group">
                                                <div class="g-recaptcha"data-sitekey="{{Session::get('SITE-CONFIGURATION')['recaptcha_site_key']}}"></div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <span class="pull-right">* {{ ONE::transSite("register_required_data") }}</span>
                            </div>

                        </div>
                        <div class="row margin-top-20">
                            <div class="col-lg-8 col-md-8 mx-auto no-padding">
                                <div class="row no-gutters">
                                    <div class="col-sm-6 col-12 cancel-btn">
                                        <a href="{{action('AuthController@login')}}">{{ ONE::transSite("register_cancel") }}</a>
                                    </div>
                                    <div class="col-sm-6 col-12 submit-btn">
                                        <input class="submit_register buttons-register-submit" type="submit" id="submit_button" value="{{ONE::transSite('register_submit')}}"/>
                                        <div id="email_already_exists_modal_button" class="submit_register buttons-register-submit" style="display:none;">
                                            {{ONE::transSite('register_submit')}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="terms_and_conditions" role="dialog">
        <div class="modal-dialog modal-terms modal-50">
            <div class="modal-content">
                <div class="modal-header no-border">
                    <h3 class="modal-title terms-conditions-modal-title">{{ ONE::transSite("register_terms_and_conditions") }}</h3>
                    <div class="float-right"><a data-dismiss="modal" style="cursor:pointer"><em class="fa fa-times my-custom-fa-close"></em></a></div>
                </div>
                <div class="modal-body terms-conditions-wrapper">
                    {!! html_entity_decode(ONE::getSiteEthic('use_terms')) !!}
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="privacy_policy" role="dialog">
        <div class="modal-dialog modal-terms modal-50">
            <div class="modal-content">
                <div class="modal-header no-border">
                    <h3 class="modal-title terms-conditions-modal-title">{{ ONE::transSite("register_privacy_policy") }}</h3>
                    <div class="float-right"><a data-dismiss="modal" style="cursor:pointer"><em class="fa fa-times my-custom-fa-close"></em></a></div>
                </div>
                <div class="modal-body terms-conditions-wrapper">
                    {!! html_entity_decode(ONE::getSiteEthic('privacy_policy')) !!}
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal_email_already_exists" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title terms-conditions-modal-title">{{ ONE::transSite("register_warning") }}</h3>
                    <div class="float-right"><a data-dismiss="modal" style="cursor:pointer"><em class="fa fa-times my-custom-fa-close"></em></a></div>
                </div>
                <div class="modal-body">
                    {!! ONE::transSite("register_this_user_already_exists") !!}
                </div>
                <div class="modal-footer">
                    <div class="container">
                        <div class="row">
                            <div class="col-6">
                            </div>
                            <div class="col-6">
                                <a href="/" class="submit-btn">{!! ONE::transSite("register_go_home") !!}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal_error_unique" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title terms-conditions-modal-title">{{ ONE::transSite("register_send_message") }}</h3>
                    <div class="float-right"><a data-dismiss="modal" style="cursor:pointer"><em class="fa fa-times my-custom-fa-close"></em></a></div>
                </div>
                <div class="modal-body" id="message-to-send-success" style="display:none;">
                    {{ ONE::transSite("register_send_message_success_message") }}
                </div>
                <div class="modal-body" id="message-to-send-failed" style="display:none;">
                    {{ ONE::transSite("register_send_message_failed_message") }}
                </div>
                <div class="modal-body" id="message-to-send">
                        <div class="row mt-3">
                            <div class="col-12 col-sm-12 col-md-4 col-lg-3">
                                {{ ONE::transSite("register_name") }}
                            </div>
                            <div class="col-12 col-sm-12 col-md-8 col-lg-9">
                                <input id="usernameSend" type="text" class="form-control @if($errors->has("name")) input-error @endif" name="name" value="{{ old('name') }}" required>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12 col-sm-12 col-md-4 col-lg-3">
                                <label class="required label-required" for="email">{{ ONE::transSite("register_email") }}</label>
                            </div>
                            <div class="col-12 col-sm-12 col-md-8 col-lg-9">
                                <input id="emailSend" type="email" class="form-control @if($errors->has("email")) input-error @endif" name="email" value="{{ old('email') }}" required>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12 col-sm-12 col-md-4 col-lg-3">
                                <label class="required label-required" for="mobile_phone">{{ ONE::transSite("register_mobile_phone") }}</label>
                            </div>
                            <div class="col-12 col-sm-12 col-md-8 col-lg-9">
                                <input id="mobilePhoneSend" type="number" class="form-control" id="mobile_phone" name="mobile_phone" required>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12 col-sm-12 col-md-4 col-lg-3">
                                <label class="required label-required" for="text">{{ ONE::transSite("register_text") }}</label>
                            </div>
                            <div class="col-12 col-sm-12 col-md-8 col-lg-9">
                                <textarea class="form-control" rows="5" id="textSend" name="text" required></textarea>
                            </div>
                        </div>
                        <input type="hidden" id="parameterUserKey" name="parameter_user_key" value="{{$errors->getBag('default')->first('parameterUserKey')}}">
                        <input type="hidden" id="parameterValue" name="parameter_value" value="{{$errors->getBag('default')->first('parameterValue')}}">
                        <input type="hidden" id="registerMessage" name="register_message" value="1">
                </div>
                <div class="modal-footer" id="message-to-send-footer">
                    <div class="container">
                        <div class="row">
                            <div class="col-6">
                                <a href="/"    class="cancel-btn">{!! ONE::transSite("register_go_to_home") !!}</a>
                            </div>
                            <div class="col-6">
                                <input type="submit" class="submit-btn" value="{!! ONE::transSite("register_send_message") !!}" id="send-message"></input>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" id="message-to-send-footer-success" style="display:none;">
                    <div class="container">
                        <div class="row">
                            <div class="col-6 mx-auto">
                                <a href="/"    class="submit-btn">{!! ONE::transSite("register_go_home") !!}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        var submit_button = document.getElementById("submit_button");
        submit_button.addEventListener("click", function(e) {
            var required = document.querySelectorAll("input[required][type='text']");
            required.forEach(function(element) {
                if(element.value.trim() == "") {
                    element.style.backgroundColor = "#f7606f";
                    $( element ).next().css( "border", "1px solid red;" );
                } else {
                    element.style.backgroundColor = "white";
                }
            });
            var required = document.querySelectorAll("select[required]");
            required.forEach(function(element) {
                if(element.value.trim() == "") {
                    element.style.cssText = "background-color:#f7606f !important;";
                } else {
                    element.style.backgroundColor = "white";
                }
            });
            var required = document.querySelectorAll("textarea[required]");
            required.forEach(function(element) {
                if(element.value.trim() == "") {
                    element.style.backgroundColor = "#f7606f";
                } else {
                    element.style.backgroundColor = "white";
                }
            });
        });

        function register(){
            $(".btn-register").css('opacity','0.5');
            $(".btn-register").css('pointer-events','none');
        }

        $(document).ready(function() {
            var timeout = 0;

            $('.modal').on('show.bs.modal', function () {
                $(this).find('.modal-body').css({
                    width:'auto', //probably not needed
                    height:'auto', //probably not needed
                });
            });

            $( window ).resize(function() {
                $('.modal-body').css({
                    width:'auto', //probably not needed
                    height:'auto', //probably not needed
                });
            });


        });

        $(document).on('click', '#send-message', function () {
            $("#message-to-send").hide();
            $("#message-to-send-footer").hide();
            dataToSend = {
                "name": $("#usernameSend").val(),
                "email": $("#emailSend").val(),
                "mobile_phone": $("#mobilePhoneSend").val(),
                "message": $("#textSend").val(),
                "parameter_user_key": $("#parameterUserKey").val(),
                "parameter_value": $("#parameterValue").val(),
                "register_message": $("#registerMessage").val(),
            };

            console.log(dataToSend);

            $.ajax({
                url: "{{ action('PublicUsersController@sendMessage') }}",
                type: "post", //send it through get method
                data: {
                    dataToSend,
                    _token: "{{ csrf_token()}}"
                },
                success: function (response) {
                    $("#message-to-send-success").show();
                    $("#message-to-send-footer-success").show();
                },
                error: function (xhr) {
                    $("#message-to-send-failed").show();
                    $("#message-to-send-footer-success").show();
                }
            });
        });
    </script>
    <style>
        .terms-conditions-wrapper {
            font-size: 14px;
        }

        .form-row{
            margin-left: -15px;
            margin-right: -15px;
        }
        
        .modal-footer .cancel-btn:hover{
            background-color: #c4c4c4 !important;
        }
    </style>
@endsection
