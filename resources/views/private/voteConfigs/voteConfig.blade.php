@extends('private._private.index')

@section('content')
    @php $form = ONE::form('voteConfig')
            ->settings(["model" => isset($voteConfig) ? $voteConfig : null,'id'=>isset($voteConfig) ? $voteConfig->vote_configuration_key : null])
            ->show('VotesConfigsController@edit', 'VotesConfigsController@delete', ['configKey' => isset($voteConfig) ? $voteConfig->vote_configuration_key : null], 'VotesConfigsController@index')
            ->create('VotesConfigsController@store', 'VotesConfigsController@index', ['configKey' => isset($voteConfig) ? $voteConfig->vote_configuration_key : null])
            ->edit('VotesConfigsController@update', 'VotesConfigsController@show', ['configKey' => isset($voteConfig) ? $voteConfig->vote_configuration_key : null])
            ->open();
    @endphp

    @if(ONE::actionType('voteConfig') == 'show')
        {!! Form::oneText('name', trans('voteConfigs.name'), isset($voteConfig->name) ? $voteConfig->name : null, ['class' => 'form-control', 'id' => 'name']) !!}
        {!! Form::oneTextArea('description', trans('voteConfigs.description'), isset($voteConfig->description) ? $voteConfig->description : null, ['class' => 'form-control', 'id' => 'description']) !!}
    @endif
    {!! Form::oneText('code', trans('privateVotesConfigs.code'), isset($voteConfig) ? $voteConfig->code : null, ['class' => 'form-control', 'id' => 'code']) !!}

    @if(isset($languages) and ONE::actionType('voteConfig') != 'show')
        @foreach($languages as $language)
            @php $form->openTabs('tab-translation-' . $language->code, $language->name); @endphp
            <div style="padding: 10px;">
                {!! Form::oneText('name_'.$language->code .'', trans('privateVotesConfigs.name'), isset($translation[$language->code]) ? $translation[$language->code]['name'] : null, ['class' => 'form-control', 'id' => 'name_'.$language->code .'']) !!}
                {!! Form::oneTextArea('description_'.$language->code .'', trans('privateVotesConfigs.description'), isset($translation[$language->code]) ? $translation[$language->code]['description'] : null, ['class' => 'form-control', 'id' => 'description_'.$language->code .'']) !!}
            </div>

        @endforeach
        @php $form->makeTabs(); @endphp
    @endif
    {!! $form->make() !!}


@endsection