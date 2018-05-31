@extends('private._private.index')

@section('content')

    <div class="row">
        <div class="col-md-12">
            @php
            $form = ONE::form('BEMenuElementConfigurations', trans('privateBEMenuElementParameters.details'))
                ->settings(["model" => isset($parameter) ? $parameter : null, 'id' => isset($parameter) ? $parameter->key : null])
                ->show('BEMenuElementParametersController@edit','BEMenuElementParametersController@delete', ['key' => isset($parameter) ? $parameter->key : null],'BEMenuElementParametersController@index')
                ->create('BEMenuElementParametersController@store', 'BEMenuElementParametersController@show', ['key' => isset($parameter) ? $parameter->key : null])
                ->edit('BEMenuElementParametersController@update', 'BEMenuElementParametersController@show', ['key' => isset($parameter) ? $parameter->key : null])
                ->open();
            @endphp
            {!! Form::oneText('code', trans('privateBEMenuElementParameters.code'), isset($parameterParameter) ? $parameterParameter->code : null, ['class' => 'form-control','required' => 'required'] ) !!}

            <div class="row">
                <div class="col-12">
                    @if(count($languages) > 0)
                        @foreach($languages as $language)
                            @php $form->openTabs('tab-translation' . $language->code, $language->name); @endphp
                            <div style="">
                                {!! Form::oneText('name_'.$language->code, ['name' => trans('privateBEMenuElementParameters.name'),'description' => trans('privateBEMenuElementParameters.help_name')],($parameter->translations->{$language->code}->name ??  null), ['class' => 'form-control', 'id' => 'name_'.$language->code]) !!}
                                {!! Form::oneTextArea('description_'.$language->code, ['name' => trans('privateBEMenuElementParameters.description'),'description' => trans('privateBEMenuElementParameters.help_description')],($parameter->translations->{$language->code}->description ??  null), ['class' => 'form-control', 'id' => 'description_'.$language->code]) !!}
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