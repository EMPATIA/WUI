@extends('private._private.index')

@section('content')

    @php
    $form = ONE::form('config')
            ->settings(["model" => isset($config) ? $config : null,'id'=>isset($config) ? $config->id : null ])
            ->show('CbsConfigsController@edit', 'CbsConfigsController@delete', ['configTypeId' => isset($configTypeId) ? $configTypeId : null,'id' => isset($config) ? $config->id : null], 'CbsConfigTypesController@showConfigurations', ['configTypeId' => isset($configTypeId) ? $configTypeId : null])
            ->create('CbsConfigsController@store', 'CbsConfigTypesController@showConfigurations', ['configTypeId' => isset($configTypeId) ? $configTypeId : null,'id' => isset($config) ? $config->id : null])
            ->edit('CbsConfigsController@update', 'CbsConfigsController@show', ['configTypeId' => isset($configTypeId) ? $configTypeId : null,'id' => isset($config) ? $config->id : null])
            ->open();
    @endphp

    {!! Form::oneText('code', trans('privateCbsConfigs.code'), isset($config) ? $config->code : null, ['class' => 'form-control', 'id' => 'code','required']) !!}
    @if(ONE::actionType('config') == 'show')
        {!! Form::oneText('title', trans('privateCbsConfigs.title'), isset($config) ? $config->title : null, ['class' => 'form-control', 'id' => 'title']) !!}
        {!! Form::oneTextArea('description', trans('privateCbsConfigs.description'), isset($config) ? $config->description : null, ['class' => 'form-control', 'id' => 'description']) !!}
    @endif
    @if(isset($languages) and ONE::actionType('config') != 'show')
        @foreach($languages as $language)
            @php $form->openTabs('tab-translation-' . $language->code, $language->name); @endphp
            <div style="padding: 10px;">
                {!! Form::oneText('title_'.$language->code .'', trans('privateCbsConfigs.title'), isset($configTranslation[$language->code]) ? $configTranslation[$language->code]['title'] : null, ['class' => 'form-control', 'id' => 'title_'.$language->code .'']) !!}
                {!! Form::oneTextArea('description_'.$language->code .'', trans('privateCbsConfigs.description'), isset($configTranslation[$language->code]) ? $configTranslation[$language->code]['description'] : null, ['class' => 'form-control', 'id' => 'description_'.$language->code .'']) !!}
            </div>
        @endforeach
        @php $form->makeTabs(); @endphp
    @endif
    {!! $form->make() !!}



@endsection
@section('scripts')
    <script>
        $(function(){
            getSidebar('{{ action("OneController@getSidebar") }}', 'config', "{{isset($configType) ? $configType->id : null}}", 'sidebar_admin.cbs_configs');
        })
    </script>
@endsection