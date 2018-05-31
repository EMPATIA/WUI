@extends('private._private.index')

@section('content')
    @php
    $form = ONE::form('useTerms', trans('privateEntitySites.details'), 'orchestrator', 'site_use_terms')
            ->settings(["model" => isset($site) ? $site : null, 'id' => isset($site) ? $site->key : null])
            ->show('UserTermsController@edit', null, ['id' => isset($site) ? $site->key : null],
                    null, ['id' => isset($site) ? $site->key : null])
            ->create('EntitiesSitesController@store', 'EntitiesSitesController@index', ['entityKey' => isset($entityKey) ? $entityKey : null])
            ->edit('UserTermsController@update', 'UserTermsController@show', ['siteKey' => isset($site) ? $site->key : null])
            ->open();

    @endphp

    {!! Form::hidden('name', isset($site) ? $site->name  : "") !!}
    {!! Form::hidden('description', isset($site) ? $site->description : null) !!}
    {!! Form::hidden('layout_key', isset($site->layout->layout_key) ? $site->layout->layout_key : null) !!}
    {!! Form::hidden('link', isset($site) ? $site->link  : null) !!}
    {!! Form::hidden('no_reply_email', isset($site) ? $site->no_reply_email  : null) !!}
    {!! Form::hidden('partial_link', isset($site) ? $site->partial_link : 0) !!}
    {!! Form::hidden('active', isset($site) ? $site->active : 1) !!}
    {!! Form::hidden('start_date', isset($site) ? $site->start_date : null) !!}
    {!! Form::hidden('end_date', isset($site) ? (!empty($site->end_date)? $site->end_date: '') : '') !!}

    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title"><i class="fa"></i> {{ trans('privateEntitiesDivided.use_terms') }}</h3>
        </div>
        <div class="box-body">

            <div class="row">

                <div class="@if(ONE::actionType('entitySites') == 'show') col-md-8 @endif col-12">
                    @if(count($languages) > 0)
                        @foreach($languages as $language)
                            @php $form->openTabs('tab-translation-' . $language->code, $language->name); @endphp
                            <div style="padding:10px;">
                                @if(ONE::actionType('useTerms') == 'show')
                                    <dt><i class="fa fa-eye"></i> {{ trans('privateSites.preview') }}</dt>
                                    <div style="border:1px solid #999999;width:100%;height:350px;overflow-y: auto">
                                        {{html_entity_decode(!empty($site->use_terms->{$language->code}->content) ? $site->use_terms->{$language->code}->content : null)}}
                                    </div>
                                    <hr style="margin: 10px 0 10px 0">
                                @else
                                    {!! Form::oneTextArea($language->default == true ? 'required_content_'.$language->code : 'content_'.$language->code,
                                                          trans('privateSites.useTerms'),
                                                          !empty($site->use_terms->{$language->code}->content) ? $site->use_terms->{$language->code}->content : null,
                                                          ['class' => 'form-control use_term', 'id' => 'content_'.$language->code]) !!}
                                @endif
                            </div>
                        @endforeach
                        @php $form->makeTabs(); @endphp
                    @endif
                </div>
            </div>
        </div>
    </div>
    {!! $form->make() !!}
@endsection
@section('scripts')
    <script>

        $(function() {
            getSidebar('{{ action("OneController@getSidebar") }}', 'useTerms', "{{(isset($site) ? $site->key : null)}}", 'site' )


        })


    </script>

    <script src="{{ asset("js/tinymce/tinymce.min.js") }}"></script>


    <script>
        $(document).ready(function(){

            {!! ONE::addTinyMCE(".use_term", ['action' => action('ContentManagerController@getTinyMCE')]) !!}

        });

    </script>
@endsection