@php
    $demoPageTitle = ONE::transSite("user_title");
@endphp

@extends('public.default._layouts.index')


@section('header_styles')

    @include('public.default.user.cssOverrides')

    <style>

        .no-padding{
            padding: 0;
        }

        .modal-header{
            border-radius: 0;
        }

        .modal-footer{
            padding: 0px;
        }


        @media (min-width: 767px) {
            .user-profile .files-col {
                padding: 0px;
            }
        }

        .form-control:disabled {
            background-color: #4c4c4c;
            color: #c4c4c4;
        }

        .form-group .form-small-btn{
            font-size: 0.8rem;
            border: none;
            box-shadow: none;
            cursor: pointer;
            padding: 0 10px;
            text-decoration: none;
        }

        .form-group .form-small-btn.yellow{
            background-color:#eb9221;
            color: #fff;
        }

        .form-group .form-small-btn.yellow:hover{
            background-color:#f0dc4e;
            color: #eb9221;
        }

        .form-group.has-warning{
            display: flex;
            flex-direction: column;
        }

        .form-control.form-control-warning:disabled,
        .form-control.form-control-success:disabled {
            margin-top: 0;
            margin-bottom: 0;
        }

        .form-control.form-control-warning:disabled{
            background-color: #f0dc4e;
            color: #fff;
        }

        .form-control.form-control-warning:disabled i.fa{
            color: #eb9221;
        }

        .form-control.form-control-success:disabled{
            background-color: #d1f2d6;
            color: #5cb85c;
        }


        .form-group.has-warning .form-control-feedback{
            font-size: 0.8rem;
            line-height: normal;
            flex: 1;
            flex-basis: 300px;
        }

        .form-group .form-small-btn{
            font-size: 0.8rem;
            border:none;
            box-shadow: none;
            cursor: pointer;
        }

        .form-group .form-small-btn.yellow{
            background-color:#eb9221;
            color: #fff;
        }

        .form-group .form-small-btn.yellow:hover{
            background-color:#f0dc4e;
            color: #eb9221;
        }

        .button-user-profile input:hover{
            background-color: {{ ONE::getSiteConfiguration("color_secondary") }} !important;
            color: white !important;
        }

        .custom-form-row {
            padding: 15px 0px;
            padding-top: 15px;
            padding-right: 0px;
            padding-bottom: 15px;
            padding-left: 0px;
            margin-bottom: 5px;
        }

        .custom-form-row .form-group {
            margin-bottom:0;
        }

        .cancel-btn input,
        .submit-btn input,
        .cancel-btn button,
        .submit-btn button,
        .cancel-btn a,
        .submit-btn a{
            text-align: center;
            padding:5px 15px;
            line-height: 25px !important;
            display: block;
            width: 100%;
            font-size:0.9rem;
        }

        .cancel-btn input,
        .cancel-btn button,
        .cancel-btn a{
            background-color: #4c4c4c;
            color: #fff;
        }

        .cancel-btn input:hover,
        .cancel-btn button:hover,
        .cancel-btn a:hover{
            background-color: #212121 !important;
            color: {{ ONE::getSiteConfiguration("color_secondary") }} !important;
            cursor: pointer;
            text-decoration: none;
        }

        .submit-btn input,
        .submit-btn button,
        .submit-btn a{
            background-color:{{ ONE::getSiteConfiguration("color_primary") }} !important;
            color: #fff !important;
        }


        .submit-btn input:hover,
        .submit-btn button:hover,
        .submit-btn a:hover {
            background-color: #fff !important;
            color: {{ ONE::getSiteConfiguration("color_primary") }} !important;
            cursor: pointer;

        }

        .submit-btn input:hover,
        .submit-btn button:hover,
        .submit-btn a:hover{
            text-decoration: none;
        }

    </style>

@endsection

@section('content')

    <link href="{{ asset("css/cropper.min.css") }}" rel='stylesheet' type='text/css'>
    <div class="container-fluid personal-area-buttons">
        <div class="row">
            <div class="col-12">
                <div class="container">
                    <div class="row no-margin">
                        <div class="offset-md-6 col-12 col-md-6 no-padding">
                            <div class="row buttons-margin">
                                <div class="col button">
                                    <a href="{{ action("PublicUsersController@edit",["userKey"=>$user->user_key]) }}" @if(!isset($profileSection) || (isset($profileSection) && $profileSection=="about")) class="active" @endif>
                                        {{ ONE::transSite("user_profile") }}
                                    </a>
                                </div>
                                <div class="col button">
                                    <a href="{{ action("PublicUsersController@showMessages") }}" @if(!isset($profileSection) || (isset($profileSection) && $profileSection=="messages")) class="active" @endif>
                                        {{ ONE::transSite("user_messages") }}
                                    </a>
                                </div>
                                <div class="col button">
                                    <a href="{{ action("PublicUsersController@userTopics") }}" @if(!isset($profileSection) || (isset($profileSection) && $profileSection=="topics")) class="active" @endif>
                                        {{ ONE::transSite("user_participation") }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row align-items-end idea-topic-title">
            <div clasS="col title">
                <span>@if(ONE::isEdit()) {{ ONE::transSite("user_edit_profile") }}@else {{ ONE::transSite("user_profile") }} @endif</span>
            </div>
        </div>
    </div>

    @if($errors->any())
        <div class="row margin-top-20">
            <div class="col-lg-8 col-md-8 mx-auto">
                <div class="field-wrapper alert alert-danger">
                    <span class="form-title">{{ ONE::transSite("user_errors") }}</span>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif


    <div class="container-fluid light-grey-bg user-profile">
        @php
            $form = ONE::form('user')
                ->settings(["model" => isset($user) ? $user : null, "id"=> isset($user) ? $user->user_key : null])
                ->show(null, null)
                ->create('PublicUsersController@store', 'PublicUsersController@edit', ['user_key' => $user->user_key])
                ->edit('PublicUsersController@update', 'PublicUsersController@edit', ['user_key' => $user->user_key])
                ->open()
        @endphp

        {{--
        @if(empty($user->social_networks))
            <div class="form-group  top-buffer">
                <div class="col-md-10 col-xs-12">
                    <button type="button" class="add_facebook addBtn">
                        <a href="{{ action('AuthSocialNetworkController@redirectToFacebook') }}" style="color: white">{{ ONE::transSite('add_facebook') }}</a>
                    </button>
                </div>
            </div>
        @else
            <div class="form-group  top-buffer">
                <div class="col-md-3 col-xs-3">
                    <button type="button" class="remove_facebook rmvBtn" data-toggle="modal" data-target="#removeFacebookModal">
                       {{ ONE::transSite('remove_facebook') }}
                    </button>
                </div>
            </div>
        @endif
        --}}

        {{--<div class="container-fluid form-empatia light-grey-bg">--}}
        @if(!empty(Session::get("message","")))
            <div class="row margin-top-20 no-gutters">
                <div class="col-12 mx-auto">
                    <div class="container no-padding">
                        <div class="field-wrapper alert alert-success">
                            <span class="form-title">{{ ONE::transSite("user_edit_success_message") }}</span>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div style="margin-bottom: 100px;margin-bottom: 30px" class="row no-gutters">
            <div class="col-12">
                <div class="container form-container-padding">
                    {{--<form>--}}
                        {{--<fieldset @if(!ONE::isEdit()) disabled @endif> --}}
                        <div class="row">
                            <div class="@if(ONE::isEdit()) col-lg-10 col-md-12 mx-auto padding-form-container @else col-lg-8 col-md-8 @endif">
                                <div class="row">
                                    <div class="col-12 col-sm-8 col-md-8 col-lg-8 no-padding">
                                        <h5 class="form-title">{{ ONE::transSite("user_access_data") }}</h5>
                                    </div>
                                </div>
                                <div class="row white-bg custom-form-row">
                                    <div class="col-lg-4 form-label">
                                        {!! Form::label('name', ONE::transSite('user_name') . (ONE::isEdit() ? "*" : ""), array('class' => 'label-required color-secundary')) !!}
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="form-group">
                                            {{--{!! Form::oneText('name', null ,(!empty($user->name) ? $user->name : (!One::isEdit() ? ONE::transSite("user_non_defined") : null )),  array('required','class'=>'form-control '.(($errors->has("name")) ? " input-error" : ""))) !!}--}}
                                            <input @if(!ONE::isEdit()) disabled @endif type="text" class='form-control {{(($errors->has("name")) ? " input-error" : "")}}' @if(!ONE::isEdit()) @endif value="{{(!empty($user->name) ? $user->name : (!One::isEdit() ? ONE::transSite("user_non_defined") : null ))}}" name="name" id="name">
                                        </div>
                                    </div>
                                </div>
                                <div class="row white-bg custom-form-row">
                                    <div class="col-lg-4 form-label">
                                        {!! Form::label('email', ONE::transSite('user_email') . ((ONE::isEdit() && Session::has("SITE-CONFIGURATION.boolean_no_email_needed") && !Session::has("SITE-CONFIGURATION.boolean_no_email_needed")) ? "*" : ""), array('class' => 'label-required color-secundary')) !!}
                                    </div>
                                    <div class="col-lg-8">
                                        @if(Session::has('user') && Session::get('user')->confirmed == 0)
                                            <div class="form-group has-warning">
                                                <input type="email" class='form-control form-control-warning {{(($errors->has("name")) ? " input-error" : "")}}' @if(!ONE::isEdit()) @endif @if(ONE::isEdit()) readonly @endif value="{{(!empty($user->email) ? $user->email : (!One::isEdit() ? ONE::transSite("user_non_defined") : null ))}}" name="email" id="email" disabled>
                                                <div class="row">
                                                    <div class="col form-control-feedback">{{ ONE::transSite("user_email_not_confirmed") }}</div>
                                                    <div class="col">
                                                        <a style="float:right" href="{{action('AuthController@sendConfirmEmail') }}" class="form-small-btn yellow mt-2 ml-auto">{{ ONE::transSite("user_resend") }}</a>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            {{--<div class="form-group has-success">--}}
                                            {{--<label class="form-control-label" for="inputSuccess1">Input with success</label>--}}
                                            {{--<input type="text" class="form-control form-control-success" id="inputSuccess1">--}}
                                            {{--<div class="form-control-feedback">Success! You've done it.</div>--}}
                                            {{--<small class="form-text text-muted">Example help text that remains unchanged.</small>--}}
                                            {{--</div>--}}

                                            <div class="form-group has-success">
                                                <input type="email" class='form-control form-control-success {{(($errors->has("name")) ? " input-error" : "")}}' @if(!ONE::isEdit()) @endif @if(ONE::isEdit()) readonly @endif value="{{(!empty($user->email) ? $user->email : (!One::isEdit() ? ONE::transSite("user_non_defined") : null ))}}" name="email" id="email" disabled>
                                                <div class="row">
                                                    <div class="col form-control-feedback">{{ ONE::transSite("user_email_confirmed") }}</div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>


                                @if(isset($registerParameters))
                                    @if(!empty($registerParameters))
                                        <div class="row">
                                            <div class="col-12 col-sm-8 col-md-8 col-lg-8 no-padding">
                                                <br>
                                                <h5 class="form-title">{{ ONE::transSite("user_parameter_personal_data") }}</h5>
                                            </div>
                                        </div>
                                        @foreach($registerParameters as $parameter)
                                            <div class="row white-bg custom-form-row">
                                                @if($parameter['parameter_type_code'] == 'text')
                                                    <div clasS="col-lg-4 form-label">
                                                        {!! Form::label($parameter['name'], $parameter['name'] . (ONE::isEdit() && $parameter['mandatory']  ? "*" : ""), array('class' => 'label-required color-secundary ' .($parameter['mandatory'] == 1 ? 'required' :null ) )) !!}
                                                    </div>
                                                    <div clasS="col-lg-8">
                                                        <div class="form-group">
                                                            {{--{!! Form::text($parameter['parameter_user_type_key'], $parameter['value'],  array($parameter['mandatory'] == true ? 'required' : null ,'class'=>'form-control oneFormed'.($errors->has( $parameter['parameter_user_type_key'] ) ?  " input-error" : "" ), 'title' => $parameter['name'], 'disabled' => @if(ONE::isEdit()) @endif)) !!}--}}
                                                            <input name="{{$parameter['parameter_user_type_key']}}" id="{{$parameter['parameter_user_type_key']}}" class="form-control oneFormed {{($errors->has( $parameter['parameter_user_type_key'] ) ?  " input-error" : "" )}}" value="{{(!empty($parameter['value']) ? $parameter['value'] : (!One::isEdit() ? ONE::transSite("user_parameter_non_defined") : null ))}}" @if(!ONE::isEdit()) disabled @endif/>
                                                            {{--<input type="text" class='form-control {{(($errors->has("name")) ? " input-error" : "")}}' @if(!ONE::isEdit()) disabled @endif value="{{(!empty($user->name) ? $user->name : (!One::isEdit() ? trans("parameter_non_defined") : null ))}}" name="name" id="name">--}}
                                                        </div>
                                                    </div>
                                                @elseif($parameter['parameter_type_code'] == 'text_area')
                                                    <div clasS="col-lg-4 form-label">
                                                        {!! Form::label($parameter['parameter_user_type_key'], $parameter['name'] . (ONE::isEdit() && $parameter['mandatory']  ? "*" : ""), array('class' => 'label-required color-secundary ' .($parameter['mandatory'] == "1" ? 'required':null))) !!}
                                                    </div>
                                                    <div clasS="col-lg-8">
                                                        <div class="form-group">
                                                            {!! Form::textarea($parameter['parameter_user_type_key'], $parameter['value'],  array('class'=>'form-control oneFormed'.($errors->has( $parameter['parameter_user_type_key'] ) ?  " input-error" : "" ), 'required' => ($parameter['mandatory'] == true ? 'required' : null), 'title' => $parameter['name'])) !!}
                                                        </div>
                                                    </div>
                                                @elseif($parameter['parameter_type_code'] == 'numeric')
                                                    <div clasS="col-lg-4 form-label">
                                                        {!! Form::label($parameter['parameter_user_type_key'], $parameter['name'] . (ONE::isEdit() && $parameter['mandatory']  ? "*" : ""), array('class' => 'label-required color-secundary ' .($parameter['mandatory'] == "1" ? 'required':null))) !!}
                                                    </div>
                                                    <div clasS="col-lg-8">
                                                        <div class="form-group">
                                                            @if (ONE::isEdit())
                                                                {!! Form::number($parameter['parameter_user_type_key'], $parameter['value'],  array('class'=>'form-control oneFormed', 'required' => ($parameter['mandatory'] == "1" ? 'required' : null), 'title' => $parameter['name'])) !!}
                                                            @else
                                                                <input disabled type="number" class='form-control' value="{{(!empty($parameter['value']) ? $parameter['value'] : (!One::isEdit() ? ONE::transSite("user_parameter_non_defined") : null ))}}" name="{{$parameter['parameter_user_type_key']}}" id="{{$parameter['parameter_user_type_key']}}">

                                                            @endif
                                                        </div>
                                                    </div>
                                                @elseif($parameter['parameter_type_code'] == 'radio_buttons')
                                                    @if(count($parameter['parameter_user_options'])> 0)
                                                        <div clasS="col-lg-4 form-label">
                                                            {!! Form::label($parameter['parameter_user_type_key'], $parameter['name'] . (ONE::isEdit() && $parameter['mandatory']  ? "*" : ""), array('class' => 'col-md-2 col-xs-12 ' .($parameter['mandatory'] == "1" ? 'required':null))) !!}
                                                        </div>
                                                        <div clasS="col-lg-8">
                                                            <div class="form-group">
                                                                @if (ONE::isEdit())
                                                                    @foreach($parameter['parameter_user_options'] as $option)
                                                                        <div class="radio">
                                                                            <label>
                                                                                <input role="radiogroup" aria-label="{!! $parameter['name'] !!}" type="radio" name="{{$parameter['parameter_user_type_key']}}" title="{!! $parameter['name'] !!}" value="{{$option['parameter_user_option_key']}}" @if($parameter['mandatory']) required @endif @if($option['selected'] || old($parameter['parameter_user_type_key'])==$option['parameter_user_option_key']) checked @endif>{{$option['name']}}
                                                                            </label>
                                                                        </div>
                                                                    @endforeach
                                                                @else
                                                                    <?php $hasValue = false;?>
                                                                    @foreach($parameter['parameter_user_options'] as $option)
                                                                        @if($option['selected'])
                                                                            <?php $hasValue = true;?>
                                                                            {{$option['name']}}
                                                                        @endif
                                                                    @endforeach
                                                                    @if (!$hasValue)
                                                                        {{ ONE::transSite("user_parameter_non_defined") }}
                                                                    @endif
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endif
                                                @elseif($parameter['parameter_type_code'] == 'gender')
                                                    @if(count($parameter['parameter_user_options'])> 0)
                                                        <div clasS="col-lg-4 form-label">
                                                            {!! Form::label($parameter['name'], $parameter['name'] . (ONE::isEdit() && $parameter['mandatory']  ? "*" : ""), array('class' => 'col-md-2 col-xs-12 ' .($parameter['mandatory'] == "1" ? 'required':null))) !!}
                                                        </div>
                                                        <div clasS="col-lg-8">
                                                            <div class="form-group">
                                                                @if (ONE::isEdit())
                                                                    @foreach($parameter['parameter_user_options'] as $option)
                                                                        <div class="radio">
                                                                            <label>
                                                                                <input role="radiogroup" aria-label="{!! $parameter['name'] !!}" type="radio" name="{{$parameter['parameter_user_type_key']}}" title="{!! $parameter['name'] !!}" value="{{$option['parameter_user_option_key']}}" @if($parameter['mandatory']) required @endif @if($option['selected']) checked @endif>{{$option['name']}}
                                                                            </label>
                                                                        </div>
                                                                    @endforeach
                                                                @else
                                                                    <?php $hasValue = false;?>
                                                                    @foreach($parameter['parameter_user_options'] as $option)
                                                                        @if($option['selected'])
                                                                            <?php $hasValue = true;?>
                                                                            {{$option['name']}}
                                                                        @endif
                                                                    @endforeach
                                                                    @if (!$hasValue)
                                                                        {{ ONE::transSite("user_parameter_non_defined") }}
                                                                    @endif
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endif
                                                @elseif($parameter['parameter_type_code'] == 'check_box')
                                                    @if(count($parameter['parameter_user_options'])> 0)
                                                        <div clasS="col-lg-4 form-label">
                                                            {!! Form::label($parameter['name'], $parameter['name'] . (ONE::isEdit() && $parameter['mandatory']  ? "*" : ""), array('class' => ($parameter['mandatory'] == "1" ? 'required':null))) !!}
                                                        </div>
                                                        <div clasS="col-lg-8">
                                                            <div class="form-group">
                                                                @if (ONE::isEdit())
                                                                    @foreach($parameter['parameter_user_options'] as $option)
                                                                        <div class="checkbox">
                                                                            <label><input type="checkbox" value="{{$option['parameter_user_option_key']}}" title="{!! $parameter['name'] !!}" name="{{$parameter['parameter_user_type_key']}}[]" @if($parameter['mandatory']) required @endif @if($option['selected'] || old($parameter['parameter_user_type_key'])==$option['parameter_user_option_key']) checked @endif>{{$option['name']}}</label>
                                                                        </div>
                                                                    @endforeach
                                                                @else
                                                                    <?php $hasValue = false;?>
                                                                    @foreach($parameter['parameter_user_options'] as $option)
                                                                        @if($option['selected'])
                                                                            <?php $hasValue = true;?>
                                                                            {{$option['name']}}
                                                                        @endif
                                                                    @endforeach
                                                                    @if (!$hasValue)
                                                                        {{ ONE::transSite("user_parameter_non_defined") }}
                                                                    @endif
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endif
                                                @elseif($parameter['parameter_type_code'] == 'dropdown')
                                                    <div clasS="col-lg-4 form-label">
                                                        {!! Form::label($parameter['parameter_user_type_key'], $parameter['name'] . (ONE::isEdit() && $parameter['mandatory']  ? "*" : ""), array('class' => 'label-required color-secundary ' .($parameter['mandatory'] == "1" ? 'required':null))) !!}
                                                    </div>
                                                    <div clasS="col-lg-8">
                                                        <div class="form-group">
                                                            @if (ONE::isEdit())
                                                                <div class="field-wrapper">
                                                                    <select class="form-control @if($errors->has( $parameter['parameter_user_type_key'] )) input-error @endif" id="{{$parameter['parameter_user_type_key']}}" name="{{$parameter['parameter_user_type_key']}}" @if($parameter['mandatory']) required @endif>
                                                                        <option value="" selected>{{ ONE::transSite("user_parameter_select_option") }}</option>
                                                                        @foreach($parameter['parameter_user_options'] as $option)
                                                                            <option value="{{$option['parameter_user_option_key']}}" @if($option['selected'] || old($parameter['parameter_user_type_key'])==$option['parameter_user_option_key']) selected @endif>{{$option['name']}}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            @else
                                                                <?php $hasValue = false;?>
                                                                @foreach($parameter['parameter_user_options'] as $option)
                                                                    @if($option['selected'])
                                                                        <?php $hasValue = true;?>
                                                                        {{$option['name']}}
                                                                    @endif
                                                                @endforeach
                                                                @if (!$hasValue)
                                                                    {{ ONE::transSite("user_parameter_non_defined") }}
                                                                @endif
                                                            @endif
                                                        </div>
                                                    </div>
                                                @elseif($parameter['parameter_type_code'] == 'mobile')
                                                    <div clasS="col-lg-4 form-label">
                                                        {!! Form::label($parameter['name'], $parameter['name'] . (ONE::isEdit() && $parameter['mandatory']  ? "*" : ""), array('class' => 'label-required color-secundary ' .($parameter['mandatory'] == 1 ? 'required' :null ) )) !!}
                                                    </div>
                                                    <div clasS="col-lg-8">
                                                        <div class="form-group">
                                                            @if (ONE::isEdit())
                                                                {!! Form::number($parameter['parameter_user_type_key'], $parameter['value'],  array($parameter['mandatory'] == true ? 'required' : null ,'class'=>'form-control oneFormed'.($errors->has( $parameter['parameter_user_type_key'] ) ?  " input-error" : "" ), 'title' => $parameter['name'])) !!}
                                                            @else
                                                                    <input disabled type="number" class='form-control' value="{{(!empty($parameter['value']) ? $parameter['value'] : (!One::isEdit() ? ONE::transSite("user_parameter_non_defined") : null ))}}" name="{{$parameter['parameter_user_type_key']}}" id="{{$parameter['parameter_user_type_key']}}">
                                                            @endif
                                                        </div>
                                                    </div>
                                                @elseif($parameter['parameter_code'] == 'birthday')
                                                    <div clasS="col-lg-4 form-label">
                                                        {!! Form::label($parameter['name'], $parameter['name'] . (ONE::isEdit() && $parameter['mandatory']  ? "*" : ""), array('class' => 'label-required color-secundary ' .($parameter['mandatory'] == 1 ? 'required' :null ) )) !!}
                                                    </div>
                                                    <div class="col-8">
                                                        <div class="form-group">
                                                            <input type="date" class="form-control" name="{{$parameter['parameter_user_type_key']}}" value="{{(!empty($parameter['value']) ? $parameter['value'] : (!One::isEdit() ? ONE::transSite("user_parameter_non_defined") : null ))}}" @if(!ONE::isEdit()) disabled @endif >
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    @endif
                                @endif
                                @if(ONE::isEdit())
                                    <div class="row margin-top-20">
                                        <div class="col-12 mx-auto no-padding">
                                            <div class="row no-gutters">
                                                <div class="col-sm-6 col-12 cancel-btn">
                                                    <a href="{{ action('PublicUsersController@edit',['userKey' => Session::get('user')->user_key]) }}">{{ ONE::transSite("user_cancel") }}</a>
                                                </div>
                                                <div class="col-sm-6 col-12 submit-btn button-user-profile">
                                                    <input style="text-align: center; padding: 5px 15px; line-height: 20px; display: block; width: 100%;cursor: pointer" type="submit" value="{{ ONE::transSite("user_update_profile") }}"/>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                @else
                                    <style>
                                        .but-user-profile a:hover{
                                            background-color: {{ ONE::getSiteConfiguration("color_secondary") }}!important;
                                            color: white!important;
                                        }
                                    </style>

                                    <div class="row" style="margin-top:25px">
                                        <div class="ml-auto submit-btn edit-button but-user-profile no-padding">
                                            <a href="{{ action('PublicUsersController@edit',['userKey' => Session::get('user')->user_key,'f' => 'user']) }}">
                                                <i class="fa fa-pencil" aria-hidden="true"></i> {{ ONE::transSite("user_edit") }}
                                            </a>
                                        </div>
                                    </div>

                                    {{--</div>--}}

                                @endif



                            </div>

                            @if(!ONE::isEdit())
                                <div class="col-lg-3 offset-lg-1 col-md-4 files-col">
                                    @if(empty($user->social_networks))
                                        <div class="button-container smaller small-device-margin mb-2" style="background-color:#3b5998;">
                                            <a href="{{ action('AuthSocialNetworkController@redirectToFacebook') }}" class="button-container">
                                                <i class="fa fab fa-facebook-f"></i> {{ ONE::transSite('add_facebook') }}</a>
                                        </div>
                                    @else
                                        <div class="button-container secondary-color  smaller small-device-margin"  style="margin-bottom:10px;">
                                            <button type="button" class="button-container smaller" data-toggle="modal" data-target="#removeFacebookModal" style="width:100%">
                                                <i class="fa fab fa-facebook-f"></i>  {{ ONE::transSite('remove_facebook') }}
                                            </button>
                                        </div>
                                    @endif

                                    <div class="button-container secondary-color smaller">
                                        <a href="#" data-toggle="modal" data-target="#changePasswordModal">
                                            {{ONE::transSite("user_change_password")}}
                                        </a>
                                    </div>
                                    <div class="photo-box mt-2">
                                        @if($user->photo_id > 0)
                                            <div class="box image-div" style="height: 200px; background-image:url('{{URL::action('FilesController@download', ['id' => $user->photo_id, 'code' => $user->photo_code, 1, 'w' => 200] )}}');"></div>
                                        @else
                                            <div class="box image-div">
                                                <div>
                                                    <em class="fa fa-camera user-icon-camera"></em><br>
                                                    {{ONE::transSite("user_no_photo")}}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        @if($user->photo_id > 0)
                                            <div class="button-container dark-grey-bg smaller">
                                                <a href="{{ action('PublicUsersController@removePhoto',['userKey' => Session::get('user')->user_key,'f' => 'user']) }}" id="remove-user-image" ><i class="fa fa-times"></i> {{ONE::transSite('remove_image')}}</a>
                                            </div>
                                            <div class="button-container secondary-color smaller">
                                                <a href="#" id="user-image" ><i class="fa fa-upload"></i> {{ONE::transSite('user_image_upload')}}</a>
                                            </div>
                                        {{--{!! ONE::imageButtonUpload("user-image","button-container secondary-color smaller", "fa fa-upload") !!}--}}
                                        @else
                                            <div class="button-container secondary-color smaller">
                                                {!! ONE::imageButtonUpload("user-image","button-container secondary-color smaller", "fa fa-upload") !!}
                                            </div>
                                        @endif
                                        {!! ONE::imageCropModal('getCroppedCanvasModal', 'getCroppedCanvasTitle', ONE::transSite("user_image_resize")) !!}
                                    </div>
                                    {{--<div class="button-container primary-color bigger">--}}
                                    {{--<a href="#">--}}
                                    {{--<i class="fa fa-facebook" aria-hidden="true"></i>--}}
                                    {{--<p>Associate facebook account</p>--}}
                                    {{--</a>--}}
                                    {{--</div>--}}
                                </div>
                            @endif
                        </div>
                        {{--</fieldset>--}}
                    {{--</form> --}}


                </div>
            </div>

            {!! $form->make() !!}
        </div>
    </div>



    <div class="modal fade" tabindex="-1" role="" id="changePasswordModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    {{ONE::transSite('user_change_password')}}
                    <button type="button" class="close pull-right" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    {!! Form::open(['action' => ['PublicUsersController@updatePassword', $user->user_key], 'method'  => 'POST']) !!}
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="form-group has-feedback">
                        <label class="user-profile form-label" style="padding: 0 0" for="old_password">{{ ONE::transSite('user_old_password') }}</label>
                        <input type="password" name="old_password" class="form-control" title="old_password" autofocus>
                    </div>
                    <div class="form-group has-feedback">
                        <label class="user-profile form-label" style="padding: 0 0" for="password">{{ ONE::transSite('user_new_password') }}</label>
                        <input type="password" name="password" class="form-control" title="password">
                    </div>
                    <div class="form-group has-feedback">
                        <label class="user-profile form-label" style="padding: 0 0" for="password_confirmation">{{ ONE::transSite('user_new_password_confirmation') }}</label>
                        <input type="password" name="password_confirmation" class="form-control"  title="password_confirmation">
                    </div>
                    <div class="modal-footer">
                        <div class="col-6" style="padding:0px 4px">
                            <button class="cancel-btn" type="button" data-dismiss="modal" aria-label="Close">{{ONE::transSite('user_cancel')}}</button>
                        </div>
                        <div class="col-6" style="padding:0px 4px">
                            <button class="submit-btn" type="submit" id="btn_change_password">{{ONE::transSite('user_change_password_submit')}}</button>
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" tabindex="-1" role="" id="removeFacebookModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    {{ONE::transSite('remove_facebook_account')}}
                    <button type="button" class="close pull-right" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    {!! Form::open(['action' => ['AuthSocialNetworkController@removeFacebook', $user->user_key], 'method'  => 'get']) !!}
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div>
                        {!! ONE::transSite("modal_delete_msg_facebook") !!}
                    </div>
                    <div class="modal-footer">
                        <div class="col-6" style="padding:0px 4px">
                            <button class="cancel-btn" type="button" data-dismiss="modal" aria-label="Close">{{ONE::transSite('modal_facebool_cancel')}}</button>
                        </div>
                        <div class="col-6" style="padding:0px 4px">
                            <button class="submit-btn" type="submit" id="btn_change_password">{{ONE::transSite('modal_delete_facebook')}}</button>
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>


@endsection

@section('scripts')
    <script src="{{ asset('vendor/jildertmiedema/laravel-plupload/js/plupload.full.min.js') }}"></script>
    <script src="{{ asset("js/cropper.min.js") }}"></script>
    <script src="{{ asset("js/canvas-to-blob.js") }}"></script>
    @include('private._private.functions') {{-- Helper Functions --}}
    <script>
        {{--{!! ONE::imageUploader('bannerUploader', action('FilesController@upload'), 'imageFileUploaded', 'user-image', 'user-image-drop-zone', 'banner-list', 'user-image-drop-zone', 'getCroppedCanvasModal', 1, 2, isset($uploadKey) ? $uploadKey : "") !!}
        --}}{!! ONE::fileUploader('bannerUploader', action('FilesController@upload'), 'imageFileUploaded', 'user-image', 'user-image-drop-zone', 'banner-list', 'user-image-drop-zone', 2,isset($uploadKey) ? $uploadKey : "", ["images"], true) !!}

        {{-- function imageUploader($variable, $route, $uploadedFunction, $idBrowseButton, $idDropZone, $idUploading, $idFiles, $idModal, $aspectRatio, $type, $uploadToken)
             public function fileUploader($variable, $route, $uploadedFunction, $idBrowseButton, $idDropZone, $idUploading, $idFiles, $type, $uploadToken,$acceptedTypes = null, $singleFile = false)--}}


bannerUploader.init();

        updateClickListener();

    </script>
@endsection

