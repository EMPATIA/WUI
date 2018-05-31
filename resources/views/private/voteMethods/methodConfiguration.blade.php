@extends('private._private.index')

@section('content')

    @php $form = ONE::form('config')
            ->settings(["model" => isset($config) ? $config : null,'id'=>isset($config) ? $config->id : null ])
            ->show('VoteMethodConfigController@edit', 'VoteMethodConfigController@delete', ['methodId' => isset($methodId) ? $methodId : null,'config' => isset($config) ? $config->id : null], 'VoteMethodsController@showConfigurations', ['methodId' => isset($methodId) ? $methodId : null])
            ->create('VoteMethodConfigController@store', 'VoteMethodsController@showConfigurations', ['methodId' => isset($methodId) ? $methodId : null,'config' => isset($config) ? $config->id : null])
            ->edit('VoteMethodConfigController@update', 'VoteMethodConfigController@show', ['methodId' => isset($methodId) ? $methodId : null,'config' => isset($config) ? $config->id : null])
            ->open();
    @endphp
    {!! Form::oneText('code', trans('privateVoteMethods.code'), isset($config) ? $config->code : null, ['class' => 'form-control', 'id' => 'code']) !!}
    {!! Form::oneSelect('parameter_type', trans('privateVoteMethods.parameter_type'), isset($parameterType) ? $parameterType : null,  isset($config) ? $config->parameter_type : null ,null  ,  ['class' => 'form-control', 'required'] ) !!}
    @if(ONE::actionType('config') == 'show')
        {!! Form::oneText('name', trans('privateVoteMethods.name'), isset($config) ? $config->name : null, ['class' => 'form-control', 'id' => 'name']) !!}
        {!! Form::oneTextArea('description', trans('privateVoteMethods.description'), isset($config) ? $config->description : null, ['class' => 'form-control', 'id' => 'description']) !!}
    @endif
    @if(isset($languages) and ONE::actionType('config') != 'show')
        @foreach($languages as $language)
            @php $form->openTabs('tab-translation-' . $language->code, $language->name); @endphp
            <div style="padding: 10px;">
                {!! Form::oneText('name_'.$language->code .'', trans('privateVoteMethods.name'), isset($configTranslation[$language->code]) ? $configTranslation[$language->code]['name'] : null, ['class' => 'form-control', 'id' => 'name_'.$language->code .'']) !!}
                {!! Form::oneTextArea('description_'.$language->code .'', trans('privateVoteMethods.description'), isset($configTranslation[$language->code]) ? $configTranslation[$language->code]['description'] : null, ['class' => 'form-control', 'id' => 'description_'.$language->code .'']) !!}
            </div>
        @endforeach
        @php $form->makeTabs(); @endphp
    @endif

    {!! $form->make() !!}

@endsection