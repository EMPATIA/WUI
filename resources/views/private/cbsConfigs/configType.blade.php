@extends('private._private.index')

@section('content')

    @php $form = ONE::form('cbConfigType')
            ->settings(["model" => isset($configType) ? $configType : null])
            ->show('CbsConfigTypesController@edit', 'CbsConfigTypesController@delete', ['id' => isset($configType) ? $configType->id : null], 'CbsConfigTypesController@index', ['id' => isset($configType) ? $configType->id : null])
            ->create('CbsConfigTypesController@store', 'CbsConfigTypesController@index', ['id' => isset($configType) ? $configType->id : null])
            ->edit('CbsConfigTypesController@update', 'CbsConfigTypesController@show', ['id' => isset($configType) ? $configType->id : null])
            ->open();
    @endphp

    {!! Form::oneText('code', trans('privateCbsConfigs.code'), isset($configType) ? $configType->code : null, ['class' => 'form-control', 'id' => 'code','required']) !!}
    @if(ONE::actionType('cbConfigType') == 'show')
        {!! Form::oneText('title', trans('privateCbsConfigs.title'), isset($configType) ? $configType->title : null, ['class' => 'form-control', 'id' => 'title']) !!}
        {!! Form::oneTextArea('description', trans('privateCbsConfigs.description'), isset($configType) ? $configType->description : null, ['class' => 'form-control', 'id' => 'description']) !!}
    @endif
    @if(isset($languages) and ONE::actionType('cbConfigType') != 'show')
        @foreach($languages as $language)
            @php $form->openTabs('tab-translation-' . $language->code, $language->name); @endphp
            <div style="padding: 10px;">
                {!! Form::oneText('title_'.$language->code .'', trans('privateCbsConfigs.title'), isset($configTypeTranslation[$language->code]) ? $configTypeTranslation[$language->code]['title'] : null, ['class' => 'form-control', 'id' => 'title_'.$language->code .'']) !!}
                {!! Form::oneTextArea('description_'.$language->code .'', trans('privateCbsConfigs.description'), isset($configTypeTranslation[$language->code]) ? $configTypeTranslation[$language->code]['description'] : null, ['class' => 'form-control', 'id' => 'description_'.$language->code .'']) !!}
            </div>
        @endforeach
        @php $form->makeTabs(); @endphp
    @endif
    {!! $form->make() !!}

@endsection
@section('scripts')
    <script>
        $(function () {
            getSidebar('{{ action("OneController@getSidebar") }}', 'details', "{{isset($configType) ? $configType->id : null}}", 'sidebar_admin.cbs_configs');

        });
    </script>
@endsection