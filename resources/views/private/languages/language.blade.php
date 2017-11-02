@extends('private._private.index')

@section('content')
    @if(ONE::asPermission('admin') && ONE::getEntityKey() == null)
        @php $form = ONE::form('languages', trans('privateLanguages.details'))
                ->settings(["model" => isset($language) ? $language : null])
                ->show('LanguagesController@edit', 'LanguagesController@delete', ['id' => isset($language) ? $language->id : null], 'LanguagesController@index')
                ->create('LanguagesController@store', 'LanguagesController@index', ['id' => isset($language) ? $language->id : null])
                ->edit('LanguagesController@update', 'LanguagesController@show', ['id' => isset($language) ? $language->id : null])
                ->open();
         @endphp
    @else
        @php $form = ONE::form('languages', trans('privateLanguages.details'), 'orchestrator', 'entity_language')
               ->settings(["model" => isset($language) ? $language : null])
               ->show('LanguagesController@edit', 'LanguagesController@delete', ['id' => isset($language) ? $language->id : null], 'EntitiesDividedController@showLanguages')
               ->create('LanguagesController@store', 'LanguagesController@index', ['id' => isset($language) ? $language->id : null])
               ->edit('LanguagesController@update', 'LanguagesController@show', ['id' => isset($language) ? $language->id : null])
               ->open();
        @endphp
   @endif

   {!! Form::oneText('name', trans('languages.name'), isset($language) ? $language->name : null, ['class' => 'form-control', 'id' => 'name']) !!}
   {!! Form::oneText('code', trans('languages.code'), isset($language) ? $language->code : null, ['class' => 'form-control', 'id' => 'code']) !!}

   {!! $form->make() !!}

@endsection