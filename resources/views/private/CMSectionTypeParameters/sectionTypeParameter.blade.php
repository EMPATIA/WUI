@extends('private._private.index')

@section('content')

    <div class="row">
        <div class="col-md-12">
            @php
            $form = ONE::form('CMSectionTypeParameters', trans('privateCMSectionTypeParameters.details'))
                ->settings(["model" => isset($sectionTypeParameter) ? $sectionTypeParameter : null, 'id' => isset($sectionTypeParameter) ? $sectionTypeParameter->section_type_parameter_key : null])
                ->show('CMSectionTypeParametersController@edit','CMSectionTypeParametersController@delete', ['key' => isset($sectionTypeParameter) ? $sectionTypeParameter->section_type_parameter_key : null],'CMSectionTypeParametersController@index')
                ->create('CMSectionTypeParametersController@store', 'CMSectionTypeParametersController@show', ['key' => isset($sectionTypeParameter) ? $sectionTypeParameter->section_type_parameter_key : null])
                ->edit('CMSectionTypeParametersController@update', 'CMSectionTypeParametersController@show', ['key' => isset($sectionTypeParameter) ? $sectionTypeParameter->section_type_parameter_key : null])
                ->open();
            @endphp
            {!! Form::oneText('code', trans('privateCMSectionTypeParameters.code'), isset($sectionTypeParameterParameter) ? $sectionTypeParameterParameter->code : null, ['class' => 'form-control', 'key' => 'code','required' => 'required'] ) !!}
            {!! Form::oneText('type_code', trans('privateCMSectionTypeParameters.type_code'), isset($sectionTypeParameterParameter) ? $sectionTypeParameterParameter->type_code : null, ['class' => 'form-control', 'id' => 'type_code','required' => 'required'] ) !!}

            <div class="row">
                <div class="col-12">
                    @if(count($languages) > 0)
                        @foreach($languages as $language)
                            @php $form->openTabs('tab-translation' . $language->code, $language->name); @endphp
                            <div style="">
                                {!! Form::oneText('name_'.$language->code, ['name' => trans('privateCMSectionTypeParameters.name'),'description' => trans('privateCMSectionTypeParameters.help_name')],($sectionTypeParameter->translations->{$language->code}->name ??  null), ['class' => 'form-control', 'id' => 'name_'.$language->code]) !!}
                                {!! Form::oneTextArea('description_'.$language->code, ['name' => trans('privateCMSectionTypeParameters.description'),'description' => trans('privateCMSectionTypeParameters.help_description')],($sectionTypeParameter->translations->{$language->code}->description ??  null), ['class' => 'form-control', 'id' => 'description_'.$language->code]) !!}
                            </div>
                        @endforeach
                        @php $form->makeTabs(); @endphp
                    @endif
                </div>
            </div>
            {!! $form->make() !!}
        </div>
    </div>
@endsection