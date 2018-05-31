@extends('public.default._layouts.index')
@section('content')
    <!-- Form -->
    <div class="container" style="margin-bottom: 50px">
        <div class="contentPage-heading-wrapper" style="z-index: 0">

            <div class="row pageSectionTitle">
                <div class="col-12 col-sm-12">
                    <h1 class="page-title bolder">{{ trans("defaultUser.fillProfile") }}</h1>
                    <div class="pageSectionTitle-line"></div>
                </div>
            </div>
        </div>

        <div>
            <h3>{{trans("publicUser.percentage_complete")}}</h3>
        </div>
        <div class="progress">
            <div class="progress-bar" role="progressbar" aria-valuenow="{{$parametersPercentageFilled}}" aria-valuemin="0" aria-valuemax="100" style="width: {{$parametersPercentageFilled}}%; min-width: 2em;">
                <span style="color: #000; font-weight: bold">{{(int) $parametersPercentageFilled}}%</span>
            </div>
        </div>
        <div class="row registerUser-content">
            <div class="col-12">
                @php
                $form = ONE::form('levelForm')
                    ->settings(["model" => isset($user) ? $user : null,'id' => isset($user) ? $user->user_key : null])
                    ->show(null,null)
                    ->create(null,null)
                    ->edit('PublicUsersController@updateLevelInfo', 'PublicController@index', isset($user) ? $user->user_key : null)
                    ->open();
                @endphp
                    {{--Hidden fields--}}
                    {!! Form::hidden('name', isset($user->name) ? $user->name : null) !!}
                    {!! Form::hidden('email', isset($user->email) ? $user->email : null) !!}

                @if(isset($registerParameters))
                    @foreach($registerParameters as $parameter)
                        @if($parameter['parameter_type_code'] == 'text')
                            {!! Form::oneText($parameter['parameter_user_type_key'], $parameter['name'],
                                $parameter['value'],
                                ['class' => 'form-control', 'id' => $parameter['parameter_user_type_key'], 'required' => ($parameter['mandatory'] == true ? 'required' : null)]) !!}
                        @elseif($parameter['parameter_type_code'] == 'text_area')
                            {!! Form::oneTextArea($parameter['parameter_user_type_key'], $parameter['name'],
                                $parameter['value'],
                                ['class' => 'form-control', 'id' => $parameter['parameter_user_type_key'] , 'required' => ($parameter['mandatory'] == true ? 'required' : null) ]) !!}
                        @elseif($parameter['parameter_type_code'] == 'radio_buttons')
                            @if(count($parameter['parameter_user_options'])> 0)
                                <div class="form-group">
                                    <label for="{{$parameter['parameter_user_type_key']}}">{{ $parameter['name'] }}:@if($parameter['mandatory']) <span class="required-symbol">*</span> @endif</label>
                                    @foreach($parameter['parameter_user_options'] as $option)
                                        <div class="radio">
                                            <label>
                                                <input type="radio" name="{{$parameter['parameter_user_type_key']}}" id="{{$parameter['parameter_user_type_key']}}" value="{{$option['parameter_user_option_key']}}" @if($parameter['mandatory']) required @endif @if($option['selected']) checked @endif>{{$option['name']}}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        @elseif($parameter['parameter_type_code'] == 'check_box')
                            @if(count($parameter['parameter_user_options'])> 0)
                                <div class="form-group">
                                    <label for="{{$parameter['parameter_user_type_key']}}">{{ $parameter['name'] }}:@if($parameter['mandatory']) <span class="required-symbol">*</span> @endif</label>
                                    @foreach($parameter['parameter_user_options'] as $option)
                                        <div class="checkbox">
                                            <label><input type="checkbox" value="{{$option['parameter_user_option_key']}}" name="{{$parameter['parameter_user_type_key']}}[]" id="{{$parameter['parameter_user_type_key']}}" @if($parameter['mandatory']) required @endif @if($option['selected']) checked @endif>{{$option['name']}}</label>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        @elseif($parameter['parameter_type_code'] == 'dropdown')
                            <div class="form-group">
                                <label for="{{$parameter['parameter_user_type_key']}}">{{ $parameter['name'] }}: @if($parameter['mandatory']) <span class="required-symbol">*</span> @endif</label>
                                <select class="form-control" id="{{$parameter['parameter_user_type_key']}}" name="{{$parameter['parameter_user_type_key']}}" @if($parameter['mandatory']) required @endif>
                                    <option value="" selected>{{trans("publicUser.selectOption")}}</option>
                                    @foreach($parameter['parameter_user_options'] as $option)
                                        <option value="{{$option['parameter_user_option_key']}}" @if($option['selected']) selected @endif>{{$option['name']}}</option>
                                    @endforeach
                                </select>
                            </div>
                        @elseif($parameter['parameter_type_code'] == 'birthday')
                            {!! Form::oneDate($parameter['parameter_user_type_key'], $parameter['name'], ($parameter['value'] != '' ? $parameter['value'] : null), ['class' => 'form-control oneDatePicker', 'id' => $parameter['parameter_user_type_key'],'required' => ($parameter['mandatory'] == true ? 'required' : null)]) !!}
                        @endif
                    @endforeach
                @endif
                {!! $form->make() !!}
            </div>
        </div>
    </div>
@endsection


@section('scripts')
    <script src="{{ asset("js/cropper.min.js") }}"></script>
    <script src="{{ asset("js/canvas-to-blob.js") }}"></script>

    @include('private._private.functions') {{-- Helper Functions --}}
@endsection