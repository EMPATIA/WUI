@extends('private._private.index')

@section('content')
    @if(ONE::isEntity())
        @php $form = ONE::form('entities', trans('privateEntities.details'))
                ->settings(["model" => isset($entity) ? $entity : null])
                ->show('EntitiesController@edit', null, ['id' => isset($entity) ? $entity->entity_key : null])
                ->create('EntitiesController@store', 'EntitiesController@index', ['id' => isset($entity) ? $entity->entity_key : null])
                ->edit('EntitiesController@update', 'EntitiesController@show', ['id' => isset($entity) ? $entity->entity_key : null])
                ->open();
        @endphp
    @else
        @php $form = ONE::form('entities')
                ->settings(["model" => isset($entity) ? $entity : null])
                ->show('EntitiesController@edit', 'EntitiesController@delete', ['id' => isset($entity) ? $entity->entity_key : null], null)
                ->create('EntitiesController@store', 'EntitiesController@index', ['id' => isset($entity) ? $entity->entity_key : null])
                ->edit('EntitiesController@update', 'EntitiesController@show', ['id' => isset($entity) ? $entity->entity_key : null])
                ->open();
        @endphp
    @endif
    {{--    @if(ONE::actionType('entities') == 'edit')--}}
    {!! Form::oneText('name', trans('privateEntities.name'), isset($entity) ? $entity->name : null, ['class' => 'form-control', 'id' => 'name']) !!}
    {{--@endif--}}
    {!! Form::oneText('designation', trans('privateEntities.designation'), isset($entity) ? $entity->designation : null, ['class' => 'form-control', 'id' => 'designation']) !!}
    {!! Form::oneText('description', trans('privateEntities.description'), isset($entity) ? $entity->description : null, ['class' => 'form-control', 'id' => 'description']) !!}
    {!! Form::oneText('url', trans('privateEntities.url'), isset($entity) ? $entity->url : null, ['class' => 'form-control', 'id' => 'url']) !!}
    {!! Form::oneSelect('country_id', trans('privateEntities.country'), isset($country) ? $country : null, isset($entity) ? $entity->country_id : null, isset($entity->country->name) ? $entity->country->name: null, ['class' => 'form-control', 'id' => 'country_id']) !!}
    {!! Form::oneSelect('timezone_id', trans('privateEntities.timezone'), isset($timezone) ? $timezone : null, isset($entity) ? $entity->timezone_id : null, isset($entity->timezone->name) ? $entity->timezone->name : null, ['class' => 'form-control', 'id' => 'timezone_id']) !!}
    {!! Form::oneSelect('currency_id', trans('privateEntities.currency'), isset($currency) ? $currency : null, isset($entity) ? $entity->currency_id : null, isset($entity->currency->currency) ? $entity->currency->currency : null, ['class' => 'form-control', 'id' => 'currency_id']) !!}
    @if(ONE::actionType('entities') == 'create')
        {!! Form::oneSelect('language_id', trans('privateEntities.language'), isset($language) ? $language : null, null, null, ['class' => 'form-control', 'id' => 'language_id'] ) !!}
    @endif

    {!! $form->make() !!}
@endsection
