@extends('private._private.index')

@section('header_scripts')
    <!-- Plupload Javascript fix and bootstrap fix @ start -->
    <link rel="stylesheet" href="/bootstrap/plupload-fix/bootstrap.css">
    <script src="/bootstrap/plupload-fix/bootstrap.min.js"></script>
    <!-- Plupload Javascript fix and bootstrap fix @ End -->
    <script src="{{ asset('vendor/jildertmiedema/laravel-plupload/js/plupload.full.min.js') }}"></script>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            @php $form = ONE::form('contentTypeTypes', trans('privateContentTypeTypes.details'), 'cm', 'content_subtypes')
                ->settings(["model" => isset($contentTypeTypeKey) ? $contentTypeTypeKey : null, 'id' => isset($contentTypeTypeKey) ? $contentTypeTypeKey : null])
                ->show('ContentTypeTypesController@edit', 'ContentTypeTypesController@delete', ['id' => isset($contentTypeTypeKey) ? $contentTypeTypeKey : null], 'ContentTypeTypesController@index', ['id' => isset($contentTypeTypeKey) ? $contentTypeTypeKey : null])
                ->create('ContentTypeTypesController@store', 'ContentTypeTypesController@index', ['id' => isset($contentTypeTypeKey) ? $contentTypeTypeKey : null])
                ->edit('ContentTypeTypesController@update', 'ContentTypeTypesController@show', ['contentTypeTypeKey' => isset($contentTypeTypeKey) ? $contentTypeTypeKey : null])
                ->open();
            @endphp

            @if(ONE::actionType('contentTypeTypes') == 'show')
                {!! Form::oneText('name', trans('privateContentTypeTypes.name'), $contentTypeType->name ?? trans("privateContentTypeTypes.no_translations"), ['class' => 'form-control', 'id' => 'name']) !!}
                @if(isset($contentTypeType->color))
                        <dt>{!! trans('privateContentTypeTypes.color') !!}</dt>
                        <span class="label" style="background-color: {!! $contentTypeType->color !!}">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                        <hr style="margin: 10px 0 10px 0">
                    @endif
                    @if(isset($contentTypeType->text_color))
                        <dt>{!! trans('privateContentTypeTypes.text_color') !!}</dt>
                        <span class="label" style="background-color: {!! $contentTypeType->text_color !!}">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                        <hr style="margin: 10px 0 10px 0">
                    @endif
            @endif

            {!! Form::oneText('code', trans('privateContentTypeTypes.code'), $contentTypeType->code ?? null, ['class' => 'form-control', 'id' => 'code']) !!}
            {!! Form::oneSelect('content_types', trans('privateContentTypeTypes.content_types'), isset($contentTypes) ? $contentTypes : null, isset($contentTypeType) ? $contentTypeType->content_type_id : '', isset($contentTypes->code) ? $contentTypes->code : '', ['class' => 'form-control', 'id' => 'content_types', 'required' => 'required']) !!}

            @if(ONE::actionType('contentTypeTypes') == 'edit' || ONE::actionType('contentTypeTypes') == 'create')
                <div class="row">
                    <div class="col-md-2 col-4">
                        {!! Form::oneColor('color', trans('privateContentTypeTypes.color'), $contentTypeType->color ?? null, ['class' => 'form-control', 'id' => 'color']) !!}

                    </div>
                </div>

                    <div class="row">
                        <div class="col-md-2 col-4">
                            {!! Form::oneColor('text_color', trans('privateContentTypeTypes.text_color'), $contentTypeType->text_color ?? null, ['class' => 'form-control', 'id' => 'text_color']) !!}

                        </div>
                    </div>
                    {{--@if(in_array('icon', $fieldTypes))--}}
                        <div class="row">
                            <div class="col-12">
                            {!! Form::oneFileUpload('file', trans('privateContentTypeTypes.file'), (!empty($file) ? $file : []), $uploadKey, array("readonly"=> false,"filesCountLimit"=>1, "acceptedtypes"=>"images")) !!}
                            </div>
                        </div>
                    {{--@endif--}}

                @if(count($languages) > 0)
                    @foreach($languages as $language)
                        @php $form->openTabs('tab-translation-' . $language->code, $language->name); @endphp
                        <div style="padding: 10px;">
                            <!-- Name -->
                            {!! Form::oneText('name_'.$language->code,
                            trans('privateContentTypeTypes.name'), isset($translations[$language->code]) ? $translations[$language->code]->name : null,
                                               ['class' => 'form-control', 'id' => 'name_'.$language->code, ($language->default) ? 'required' : ''  ]) !!}

                        </div>
                    @endforeach
                    @php $form->makeTabs(); @endphp
                @endif
            @endif

            {!! $form->make() !!}
        </div>
    </div>

@endsection


