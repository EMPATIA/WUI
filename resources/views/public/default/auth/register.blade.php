@extends('public.default._layouts.index')

@section('header_scripts')
    @if (!empty(Session::get('SITE-CONFIGURATION')['recaptcha_site_key']) &&  !empty(Session::get('SITE-CONFIGURATION')['recaptcha_secret_key']))
        <script src='https://www.google.com/recaptcha/api.js'></script>
    @endif
@endsection

@section('content')

    <!-- Header -->
    <div class="container registerContent">
        <div class="row pageSectionTitle">
            <div class="col-xs-12 col-sm-12">
                <h1 class="page-title">{{ trans("defaultAuth.register") }}</h1>
            </div>
        </div>
        <div class="row register-box top-buffer">

            <form action="{{ URL::action('AuthController@verifyRegisterAndLogin') }}" method="POST" onsubmit="register()">
                <div class="col-md-2 col-xs-12">
                    <div class="text-center">
                        <div class="user-img">
                            <i class="fa fa-user"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-10 col-xs-12">
                    <div class="col-md-6 col-xs-12">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <div class="form-group">
                            <label for="name">{{ trans('defaultAuth.nameSurname') }}:</label>
                            <input id="name" type="text" name="name" class="form-control" placeholder="Name" value="{{ old('name') }}" required autofocus>
                        </div>
                        <div class="form-group">
                            <label for="email">
                                {{ trans('defaultAuth.email') }}:
                                <span id="email-verify" class="pull-right"><span></span><i class="fa fa-circle hidden"></i></span>
                            </label>
                            <input id="email" type="text" name="email" class="form-control" placeholder="Email" required value="{{ old('email') }}">
                        </div>
                        <div class="form-group">
                            <label for="password">{{ trans('defaultAuth.password') }}:</label>
                            <input id="password" type="password" name="password" class="form-control" placeholder="{{ trans('defaultAuth.password') }}" required>
                        </div>
                        <div class="form-group">
                            <label for="password_confirmation">{{ trans('defaultAuth.confirm_password') }}:</label>
                            <input id="password_confirmation" type="password" name="password_confirmation" class="form-control" placeholder="{{ trans('defaultAuth.confirm_password') }}">
                        </div>

                        @if(isset($registerParameters))
                            @foreach($registerParameters as $parameter)
                                <div class="row">
                                    @if($parameter['parameter_type_code'] == 'text')
                                        <div class="col-xs-12 col-sm-8 col-md-7 col-lg-12">
                                            <div class="form-group">
                                                {!! Form::label($parameter['name'], $parameter['name'], array('class' => 'col-xs-12 pad-0 ' .($parameter['mandatory'] == true ? 'label-required' : null), "style"=>"padding-left:0;")) !!}
                                                {!! Form::text($parameter['parameter_user_type_key'], $parameter['value'],  array($parameter['mandatory'] == true ? 'required' : '' ,'class'=>'form-control' . ($parameter['parameter_user_type_key']==="72V8xUOUQEQayZ0bxE0MuDprqlKlqgch" ?  "hidden" : ""), 'title' => $parameter['name'])) !!}
                                            </div>
                                        </div>
                                    @elseif($parameter['parameter_type_code'] == 'text_area')
                                        <div class="col-xs-12 col-sm-8 col-md-7 col-lg-12">
                                            <div class="form-group">
                                                {!! Form::label($parameter['name'], $parameter['name'], array('class' => 'col-xs-12 pad-0 ' .($parameter['mandatory'] == true ? 'label-required':null), "style"=>"padding-left:0;")) !!}

                                                {!! Form::textarea($parameter['parameter_user_type_key'], $parameter['value'],  array('class'=>'form-control', 'required' => ($parameter['mandatory'] == true ? 'required' : null), 'title' => $parameter['name'])) !!}
                                            </div>
                                        </div>
                                    @elseif($parameter['parameter_type_code'] == 'numeric')
                                        <div class="col-xs-12 col-sm-8 col-md-7 col-lg-12">
                                            <div class="form-group">
                                                {!! Form::label($parameter['name'], $parameter['name'], array('class' => 'col-xs-12 pad-0 ' .($parameter['mandatory'] == true ? 'label-required':null), "style"=>"padding-left:0;")) !!}

                                                {!! Form::number($parameter['parameter_user_type_key'], $parameter['value'],  array('class'=>'form-control', 'required' => ($parameter['mandatory'] == true ? 'required' : null), 'title' => $parameter['name'])) !!}
                                            </div>
                                        </div>
                                    @elseif($parameter['parameter_type_code'] == 'radio_buttons')
                                        <fieldset>
                                            @if(count($parameter['parameter_user_options'])> 0)
                                                <div class="col-xs-12 col-sm-8 col-md-7 col-lg-12">
                                                    <div class="form-group">
                                                        {!! Form::label($parameter['name'], $parameter['name'], array('class' => 'col-xs-12 pad-0 ' .($parameter['mandatory'] == true ? 'label-required':null), "style"=>"padding-left:0;")) !!}

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
                                                    </div>
                                                </div>
                                            @endif
                                        </fieldset>
                                    @elseif($parameter['parameter_type_code'] == 'check_box')
                                        @if(count($parameter['parameter_user_options'])> 0)
                                            <div class="col-xs-12 col-sm-8 col-md-7 col-lg-12">
                                                <div class="form-group">
                                                    {!! Form::label($parameter['name'], $parameter['name'], array('class' => 'col-xs-12 pad-0 ' .($parameter['mandatory'] == true ? 'label-required':null), "style"=>"padding-left:0;")) !!}

                                                    @foreach($parameter['parameter_user_options'] as $option)
                                                        <div class="checkbox">
                                                            <label><input type="checkbox"
                                                                          value="{{$option['parameter_user_option_key']}}"
                                                                          title="{!! $parameter['name'] !!}"
                                                                          name="{{$parameter['parameter_user_type_key']}}[]"
                                                                          @if($parameter['mandatory']) required
                                                                          @endif @if($option['selected']) checked @endif>{{$option['name']}}
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    @elseif($parameter['parameter_type_code'] == 'dropdown')
                                        <div class="col-xs-12 col-sm-8 col-md-7 col-lg-12">
                                            <div class="form-group">
                                                {!! Form::label($parameter['name'], $parameter['name'], array('class' => 'col-xs-12 pad-0 ' .($parameter['mandatory'] == true ? 'label-required':null), "style"=>"padding-left:0;")) !!}

                                                <select class="form-control" id="{{$parameter['parameter_user_type_key']}}"
                                                        name="{{$parameter['parameter_user_type_key']}}"
                                                        @if($parameter['mandatory']) required @endif>
                                                    <option value="" selected>{{trans("user.select_option")}}</option>
                                                    @foreach($parameter['parameter_user_options'] as $option)
                                                        <option value="{{$option['parameter_user_option_key']}}"
                                                                @if($option['selected']) selected @endif>{{$option['name']}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    @elseif($parameter['parameter_type_code'] == 'birthday')
                                        <div class="col-xs-12 col-sm-8 col-md-7 col-lg-12">
                                            <div class="form-group">
                                                {!! Form::label($parameter['name'], $parameter['name'], array('class' => 'col-xs-12 pad-0 ' .($parameter['mandatory'] == true ? 'label-required':null), "style"=>"padding-left:0;")) !!}

                                                <div class="input-group date">
                                                    <span class="input-group-addon"><span class="glyphicon glyphicon-th"></span></span>
                                                    <input class="form-control oneDatePicker"
                                                           id="{!! $parameter['parameter_user_type_key'] !!}"
                                                           title="{!! $parameter['name'] !!}"
                                                           {!! $parameter['mandatory'] == true ? 'required' : null !!} placeholder="{!! \Carbon\Carbon::now()->format('Y-m-d') !!}"
                                                           data-date-format="yyyy-mm-dd"
                                                           name="{!! $parameter['parameter_user_type_key'] !!}"
                                                           value="{!! $parameter['value'] !!}" type="text">
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        @endif

                        <div class="row">
                            <div class="col-xs-12">
                                @if (!empty(Session::get('SITE-CONFIGURATION')['recaptcha_site_key']) &&  !empty(Session::get('SITE-CONFIGURATION')['recaptcha_secret_key']))
                                    <div class="g-recaptcha" data-sitekey="{{Session::get('SITE-CONFIGURATION')['recaptcha_site_key']}}"></div>
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 register-message">

                                <div class="acceptTermsConditions">
                                    <p><input type="checkbox" value="1" id="checkboxAcceptTerms" name="checkboxAcceptTerms" required/>&nbsp;<label for="checkboxAcceptTerms"> {{ trans('defaultAuth.accept') }}
                                            <a data-toggle="modal" href="#terms_and_conditions" class="u-style">{{ trans('defaultAuth.terms_and_conditions') }}</a></label>
                                    </p>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 register-button-div">
                        <a href="/" class="btn btn-flat btn-back">{{ trans('defaultAuth.cancel') }}</a>
                        <button type="submit" class="registerSubmitBtn"><span class="glyphicon glyphicon-ok"></span> {{ trans('defaultAuth.register') }}</button>
                    </div>
                <br>
                </div>

            </form>
        </div>
    </div>
    <div class="modal fade" id="terms_and_conditions" role="dialog">
        <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header no-border">
                <div class="pull-right"><a data-dismiss="modal"><i class="fa fa-times my-custom-fa-close"></i></a></div>
                <h3 class="modal-title terms-conditions-modal-title">{{trans('defaultAuth.terms_and_conditions')}}</h3>
            </div>
            <div class="modal-body terms-conditions-wrapper">
                {!! html_entity_decode($useTerms) !!}
            </div>
        </div>
        </div>
    </div>
    <div class="bottom-buffer"></div>

@endsection

@section('scripts')
    <script>

        function register(){
            $(".btn-register").css('opacity','0.5');
            $(".btn-register").css('pointer-events','none');
        }
        $(document).ready(function() {
            var timeout = 0;
            $("input[name='email']").on("keyup", function(e){
                clearTimeout(timeout);
                timeout = setTimeout(function() {
                    emailVerify = $("#email-verify");
                    emailVerify.find("i").css("color", "#FF0").removeClass("hidden").attr("title","{{ trans("register.verifying_email") }}");
                    emailVerify.find("span").text("");

                    $(e.target).css("border","");
                    if (/^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/.test($(e.target).val())) {
                        $.ajax({
                            method: 'POST', // Type of response and matches what we said in the route
                            url: '{{ action("AuthController@verifyEmailExists") }}', // This is the url we gave in the route
                            data: {email: $(e.target).val(), _token: "{{ csrf_token() }}"}, // a JSON object to send back
                            success: function (response) { // What to do if we succeed

                                if (response.exists == true) {
                                    color = "red";
                                    text = "{{ trans("register.already_registered_email") }}";
                                    $(e.target).css("border","1px solid red");
                                } else {
                                    color = "#0E0";
                                    text = "{{ trans("register.valid_email") }}";
                                }

                                emailVerify = $("#email-verify");
                                emailVerify.find("i").css("color", color).attr("title","");
                                emailVerify.find("span").text(text);
                            },
                            error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                                console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                            }
                        });
                    } else {
                        emailVerify.find("i").css("color","red").attr("title","");
                        emailVerify.find("span").text("{{ trans("register.invalid_email") }}");
                        $(e.target).css("border","1px solid red");
                    }
                }, 1000);
            });
        })
    </script>
@endsection