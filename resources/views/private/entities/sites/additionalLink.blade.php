@extends('private._private.index')

@section('content')
    @php
    $form = ONE::form('SiteAdditionalUrlsController', trans('privateEntitySiteAdditionalLink.details'), 'orchestrator', 'entity_site')
        ->settings(["model" => isset($additionalUrl) ? $additionalUrl : null])
        ->show('SiteAdditionalUrlsController@edit', 'SiteAdditionalUrlsController@deleteConfirm', ['id' => isset($additionalUrl) ? $additionalUrl->id : null],
            'EntitiesSitesController@show', ['siteKey' => isset($site) ? $site->key : null])
        ->create('SiteAdditionalUrlsController@store', 'EntitiesSitesController@show', ['siteKey' => isset($site) ? $site->key : null])
        ->edit('SiteAdditionalUrlsController@update', 'SiteAdditionalUrlsController@show', ['id' => isset($additionalUrl) ? $additionalUrl->id : null])
        ->open();
    @endphp

    {!! Form::hidden('entity_key',isset($entityKey)? $entityKey:'') !!}
    {!! Form::hidden('site_key',isset($site) ? $site->key : '') !!}
    {!! Form::oneText('link', trans('privateEntitySiteAdditionalLink.url'), isset($additionalUrl) ? $additionalUrl->link  : null, ['class' => 'form-control', 'id' => 'link','required']) !!}

    {!! $form->make() !!}

@endsection

