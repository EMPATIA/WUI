@extends('private._private.index')

@section('content')
       @if(ONE::isEntity())

        @php $form = ONE::form('entitiesDivided', trans('privateEntities.details'), 'orchestrator', 'entity')
                ->settings(["model" => isset($entity) ? $entity : null])
                ->show('EntitiesDividedController@edit', null, ['id' => isset($entity) ? $entity->entity_key : null])
                ->create('EntitiesDividedController@store', 'EntitiesDividedController@index', ['id' => isset($entity) ? $entity->entity_key : null])
                ->edit('EntitiesDividedController@update', 'EntitiesDividedController@showEntity', ['id' => isset($entity) ? $entity->entity_key : null])
                ->open();

        @endphp
    @else
        @php $form = ONE::form('entitiesDivided', trans('privateEntities.details'))
                ->settings(["model" => isset($entity) ? $entity : null])
                ->show('EntitiesController@edit', ['id' => isset($entity) ? $entity->entity_key : null], 'EntitiesController@showEntityM')
                ->create('EntitiesController@store', 'EntitiesController@index', ['id' => isset($entity) ? $entity->entity_key : null])
                ->edit('EntitiesController@update', 'EntitiesController@show', ['id' => isset($entity) ? $entity->entity_key : null])
                ->open();
        @endphp
    @endif

    {!! Form::oneText('name', trans('privateEntities.name'), isset($entity) ? $entity->name : null, ['class' => 'form-control', 'id' => 'name']) !!}
    {{--@endif--}}

    {!! Form::oneText('designation', trans('privateEntities.designation'), isset($entity) ? $entity->designation : null, ['class' => 'form-control', 'id' => 'designation']) !!}
    {!! Form::oneText('description', trans('privateEntities.description'), isset($entity) ? $entity->description : null, ['class' => 'form-control', 'id' => 'description']) !!}
    {!! Form::oneText('url', trans('privateEntities.url'), isset($entity) ? $entity->url : null, ['class' => 'form-control', 'id' => 'url']) !!}
    {!! Form::oneSelect('country_id', trans('privateEntities.country'), isset($country) ? $country : null, isset($entity) ? $entity->country_id : null, isset($entity->country->name) ? $entity->country->name: null, ['class' => 'form-control', 'id' => 'country_id']) !!}
    {!! Form::oneSelect('timezone_id', trans('privateEntities.timezone'), isset($timezone) ? $timezone : null, isset($entity) ? $entity->timezone_id : null, isset($entity->timezone->code) ? $entity->timezone->code : null, ['class' => 'form-control', 'id' => 'timezone_id']) !!}
    {!! Form::oneSelect('currency_id', trans('privateEntities.currency'), isset($currency) ? $currency : null, isset($entity) ? $entity->currency_id : null, isset($entity->currency->currency) ? $entity->currency->currency : null, ['class' => 'form-control', 'id' => 'currency_id']) !!}
    {!! $form->make() !!}

@endsection
