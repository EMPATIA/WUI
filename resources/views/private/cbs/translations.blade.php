@extends('private._private.index')


@section('content')
    <div class="row">
        <div class="col-12 col-md-9 ">
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title"> {{ trans('private.translations_list') }}
                    </h3>
                </div>
                <div class="box-body">
                    <div class="container">
                        {{--<div class="row translations" style="margin-bottom:20px">--}}
                        {{--  <div class='margin-top-20 margin-bottom-20'><b class='hide_show_languages'>{{trans('Translations::translation.hide_show_languages')}}:</b>
                            @foreach ($languages as $language)
                                <label class="toggle-vis-label"  title="{{$language->name}}" ><input id="toggle-vis-{{$language->code}}"  value="{{$language->code}}" class="toggle-vis" type="checkbox" checked onclick='javascript:hideShowLanguages(value);' > {{$language->code}} </label>
                            @endforeach
                        </div>  --}}
                        {{--</div>--}}

                        <div class="row translations" style="margin-bottom:10px">
                            <div class="btn btn-flat btn-create btn-sm" onclick="javascript:$('#exampleModal').modal();">
                                <i class="fa fa-plus" data-toggle="modal" data-target="#exampleModal"></i> {{ trans('private.translations_addTranslation') }}
                            </div>
                            {{-- export/import translations --}}
                            <div class="col-x-12 col-md-4 col-lg-3 col-xl-2" >
                                <a class="btn btn-flat btn-create btn-sm" href="{!! action('TranslationsController@exportTranslations', [(isset($cbKey) ? 'cbKey='.$cbKey: (isset($siteKey) ? 'siteKey='.$siteKey :''  ))])  !!}">{{trans("private.translations_exportToCsv")}}</a>
                            </div>
                        </div>

                        <div class="row" style="margin-top:15px">
                            <div class="col-xs-12 col-md-6" style="text-align: left;">
                                <label>Code</label>
                            </div>
                            <div class="col-xs-12 col-md-6">
                                <div class="row">
                                    @foreach($languages as $language)
                                        <div class="col-xs-12 col-md-6 col-lg-3" style="text-align: left" data-language="{{$language->code}}">
                                            <label>{{$language->code}}</label>
                                        </div>

                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @if(!empty($translations))
                            @foreach($translations as $translation)
                                <div class="row trans_{{$translation->code}}" style="margin-top: 5px">
                                    <div class="col-xs-12 col-md-6 code" id="{{$translation->code}}" title="{{$translation->code}}">
                                        @if(strlen($translation->code) > 45)
                                            {{ substr($translation->code, 0, 45)."..." }}
                                        @else
                                            {{ $translation->code }}
                                        @endif
                                        <i id="{{$translation->code}}" data-id="{{$translation->id}}" class="fa fa-trash pull-right"></i>
                                    </div>
                                    <div class="col-xs-12 col-md-6">
                                        <div class="row">
                                            @foreach($languages as $language)
                                                <div class="col" id="{{$translation->code}}" data-id="{{$translation->id}}" data-language="{{$language->code}}">
                                                    @if(collect($translation->translation_language)->contains('language_code', '=', $language->code))
                                                        <input class="form-control trans_{{$language->code}}" id="{{$language->code}}" type="text" value="{{collect($translation->translation_language)->where('language_code', '=', $language->code)->first()->translation}}">
                                                    @else
                                                        <input class="form-control trans_{{$language->code}}" id="{{$language->code}}" type="text" value="" placeholder="{{trans('private.translations_empty')}}">
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif

                        {{--  <div class="row newLine" style="margin-top: 5px">
                            <div class="col-1">
                                <input class="form-control code code_save" id="code" type="text">
                            </div>
                            @foreach($languages as $language)
                            <div class="col-1 trans">
                                <input class="form-control new" id="{{$language->code}}" type="text" value="">
                            </div>
                            @endforeach
                        </div>  --}}
                    </div>

                    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <label> Code </label>
                                    <input class="form-control newCode" id="code" type="text">
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <button type="button" class="btn btn-primary new_code">Save changes</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>




        {{--<div class="col-md-12">--}}
            {{--<div class="box box-primary">--}}
                {{--<div class="box-header">--}}
                    {{--<h3 class="box-title"> {{ trans('private.translations_list') }}--}}
                    {{--</h3>--}}
                {{--</div>--}}
                {{--<div class="box-body">--}}
                    {{--<div class="container">--}}
                        {{--<div class="row translations" style="margin-bottom:20px">--}}
                        {{--  <div class='margin-top-20 margin-bottom-20'><b class='hide_show_languages'>{{trans('Translations::translation.hide_show_languages')}}:</b>--}}
                            {{--@foreach ($languages as $language)--}}
                                {{--<label class="toggle-vis-label"  title="{{$language->name}}" ><input id="toggle-vis-{{$language->code}}"  value="{{$language->code}}" class="toggle-vis" type="checkbox" checked onclick='javascript:hideShowLanguages(value);' > {{$language->code}} </label>--}}
                            {{--@endforeach--}}
                        {{--</div>  --}}
                        {{--</div>--}}
                        {{--<div class="row translations" style="margin-bottom:20px; border-style: none">--}}
                            {{--<div class="col-xs-12 col-md-4 col-lg-3 col-xl-2" style="border-left: none">--}}
                                {{--{!! Form::open(array('action' => 'TranslationsController@importTranslations','method'=>'post', 'files'=>'true')) !!}--}}
                                {{--{!! Form::hidden('cbKey', isset($cbKey) ? $cbKey : '') !!}--}}
                                {{--{!! Form::hidden('siteKey', isset($siteKey) ? $siteKey : '') !!}--}}
                                {{--{!! Form::file('csv', ['class'=>'btn ', 'style'=>"margin-bottom:20px"]) !!}<br>--}}
                                {{--{!! Form::submit(trans("private.translations_uploadFile"), ['class'=>'btn btn-flat btn-create btn-sm']) !!}--}}
                                {{--{!! Form::close() !!}--}}
                            {{--</div>--}}
                        {{--</div>--}}
                        {{--<div class="row translations" style="margin-bottom:10px">--}}
                            {{--<div class="btn btn-flat btn-create btn-sm">--}}
                                {{--<i class="fa fa-plus" data-toggle="modal" data-target="#exampleModal"></i> {{ trans('private.translations_addTranslation') }}--}}
                            {{--</div>--}}
                            {{-- export/import translations --}}
                            {{--<div class="col-x-12 col-md-4 col-lg-3 col-xl-2" >--}}
                                {{--<a class="btn btn-flat btn-create btn-sm" href="{!! action('TranslationsController@exportTranslations', [(isset($cbKey) ? 'cbKey='.$cbKey: (isset($siteKey) ? 'siteKey='.$siteKey :''  ))])  !!}">{{trans("private.translations_exportToCsv")}}</a>--}}
                            {{--</div>--}}
                        {{--</div>--}}

                        {{--<div class="row" style="margin-top:15px">--}}
                            {{--<div class="col-x-12 col-md-4 col-lg-3 col-xl-4" style="text-align: left;">--}}
                                {{--<label>Code</label>--}}
                            {{--</div>--}}
                            {{--<div class="col-xs-12 col-md-8 col-lg-9 col-xl-8">--}}
                                {{--<div class="row">--}}
                                    {{--@foreach($languages as $language)--}}
                                        {{--<div class="col-xs-12 col-md-6 col-lg-3" style="text-align: left" data-language="{{$language->code}}">--}}
                                            {{--<label>{{$language->code}}</label>--}}
                                        {{--</div>--}}

                                    {{--@endforeach--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                        {{--@if(!empty($translations))--}}
                            {{--@foreach($translations as $translation)--}}
                                {{--<div class="row trans_{{$translation->code}}" style="margin-top: 5px">--}}
                                    {{--<div class="col-xs-12 col-md-4 col-lg-3 col-xl-4 code" id="{{$translation->code}}" title="{{$translation->code}}">--}}
                                        {{--@if(strlen($translation->code) > 45)--}}
                                            {{--{{ substr($translation->code, 0, 45)."..." }}--}}
                                        {{--@else--}}
                                            {{--{{ $translation->code }}--}}
                                        {{--@endif--}}
                                        {{--<i id="{{$translation->code}}" data-id="{{$translation->id}}" class="fa fa-trash pull-right"></i>--}}
                                    {{--</div>--}}
                                    {{--<div class="col-xs-12 col-md-8 col-lg-9 col-xl-8">--}}
                                        {{--<div class="row">--}}
                                            {{--@foreach($languages as $language)--}}
                                                {{--<div class="col-xs-12 col-md-6 col-lg-4 col-xl-3" id="{{$translation->code}}" data-id="{{$translation->id}}" data-language="{{$language->code}}">--}}
                                                    {{--@if(collect($translation->translation_language)->contains('language_code', '=', $language->code))--}}
                                                        {{--<input class="form-control trans_{{$language->code}}" id="{{$language->code}}" type="text" value="{{collect($translation->translation_language)->where('language_code', '=', $language->code)->first()->translation}}">--}}
                                                    {{--@else--}}
                                                        {{--<input class="form-control trans_{{$language->code}}" id="{{$language->code}}" type="text" value="" placeholder="{{trans('private.translations_empty')}}">--}}
                                                    {{--@endif--}}
                                                {{--</div>--}}
                                            {{--@endforeach--}}
                                        {{--</div>--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                            {{--@endforeach--}}
                        {{--@endif--}}

                        {{--  <div class="row newLine" style="margin-top: 5px">--}}
                            {{--<div class="col-1">--}}
                                {{--<input class="form-control code code_save" id="code" type="text">--}}
                            {{--</div>--}}
                            {{--@foreach($languages as $language)--}}
                            {{--<div class="col-1 trans">--}}
                                {{--<input class="form-control new" id="{{$language->code}}" type="text" value="">--}}
                            {{--</div>--}}
                            {{--@endforeach--}}
                        {{--</div>  --}}
                    {{--</div>--}}

                    {{--<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">--}}
                        {{--<div class="modal-dialog" role="document">--}}
                            {{--<div class="modal-content">--}}
                                {{--<div class="modal-header">--}}
                                    {{--<h5 class="modal-title" id="exampleModalLabel">Modal title</h5>--}}
                                    {{--<button type="button" class="close" data-dismiss="modal" aria-label="Close">--}}
                                        {{--<span aria-hidden="true">&times;</span>--}}
                                    {{--</button>--}}
                                {{--</div>--}}
                                {{--<div class="modal-body">--}}
                                    {{--<label> Code </label>--}}
                                    {{--<input class="form-control newCode" id="code" type="text">--}}
                                {{--</div>--}}
                                {{--<div class="modal-footer">--}}
                                    {{--<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>--}}
                                    {{--<button type="button" class="btn btn-primary new_code">Save changes</button>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                {{--</div>--}}
            {{--</div>--}}
        {{--</div>--}}

        <div class="col-12 col-md-3 ">
            <div class="card" >
                <div class="card-header">
                    <h4>
                        {{trans("private.translations_uploadFile")}}
                    </h4>
                </div>
                <div class="card-body ">
                    <div class="row translations" style="margin-bottom:20px; border-style: none">
                        <div class="col-xs-12 col-md-4 col-lg-3 col-xl-2" style="border-left: none">
                            {!! Form::open(array('action' => 'TranslationsController@importTranslations','method'=>'post', 'files'=>'true')) !!}
                            {!! Form::hidden('cbKey', isset($cbKey) ? $cbKey : '') !!}
                            {!! Form::hidden('siteKey', isset($siteKey) ? $siteKey : '') !!}
                            {!! Form::file('csv', ['class'=>'btn ', 'style'=>"margin-bottom:20px"]) !!}<br>
                            {!! Form::submit(trans("private.translations_uploadFile"), ['class'=>'btn btn-flat btn-create btn-sm']) !!}
                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script>

        var cbKey = '{{$cbKey ?? null}}';
        var siteKey = '{{$siteKey ?? null}}';

        $(".code").change(function(){
            $.ajax({
                'url' : '{{action("TranslationsController@store")}}',
                'method' : 'post',
                'data' : {code: $(this).val(), cb_key: cbKey, site_key: siteKey, _token: '{{csrf_token()}}'},
                success: function(response){

                },
            })
        })

        $(".new_code").click(function(){
            $.ajax({
                'url' : '{{action("TranslationsController@store")}}',
                'method' : 'post',
                'data' : {code: $(".newCode").val(), cb_key: cbKey, site_key: siteKey, _token: '{{csrf_token()}}'},
                success: function(response){
                    location.reload();

                },
            })
        })

        $("input:not(.code,.toggle-vis,.new,.newCode)").change(function(){

            if($(this).parent().prop('id')==""){
                //alert("code vazio");
            }
            else{
                $.ajax({
                    'url' : '{{action("TranslationsController@store")}}',
                    'method' : 'post',
                    'data' : {id:$(this).parent().data('id'), cb_key: cbKey, site_key: siteKey, lang_code: $(this).prop('id'), trans: $(this).val(), _token: '{{csrf_token()}}'},
                    success: function(response){

                    },
                })
            }
        })

        $(".fa-trash").click(function(){
            var key = $(this).prop('id');

            $.ajax({
                'url' : '{{action("TranslationsController@deleteLine")}}',
                'method' : 'get',
                'data' : {code: $(this).prop('id'), id:$(this).data('id'), cb_key: cbKey, site_key: siteKey, _token: '{{csrf_token()}}'},
                success: function(response){

                },
            })
            $("div").find('.row.trans_'+key).remove();
        })

    </script>
@endsection


