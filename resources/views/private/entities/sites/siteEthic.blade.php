@extends('private._private.index')

@section('content')
    @php
    $form = ONE::form('siteEthic', trans('privateSiteEthics.details'), 'orchestrator', 'site_privacy_policy')
        ->settings(["model" => $siteKey ?? null, 'id' => $siteKey ?? null])
        ->show('SiteEthicsController@edit', 'SiteEthicsController@delete',
            ['site_key' => $siteKey ?? null,'site_ethic_key' => isset($siteEthic) ? $siteEthic->site_ethic_key : null, 'version' => isset($siteEthic) ? $siteEthic->version : null],
            null, ['site_key' => $siteKey ?? null,'site_ethic_key' => isset($siteEthic) ? $siteEthic->site_ethic_key : null])
        ->create('SiteEthicsController@store', null, ['site_key' => isset($siteKey) ? $siteKey : null])
        ->edit('SiteEthicsController@update', null, ['site_key' => $siteKey ?? null,'site_ethic_key' => isset($siteEthic) ? $siteEthic->site_ethic_key : null])
        ->open();
    @endphp

    {!! Form::hidden('type', $type  ?? null) !!}

    <div class="">
        <div class="">

            @if(ONE::actionType('siteEthic') == 'show')
                <div style="margin-bottom: 10px;">
                    {!! Form::label('versions', trans('privateSiteEthics.version')) !!}
                    {!! Form::select('siteEthicVersions', isset($siteEthicVersions) ? $siteEthicVersions : null, isset($siteEthic) ? $siteEthic->version : null, ['class' => 'form-control', 'id' => 'siteEthicVersions']) !!}
                </div>
                <div style="margin-bottom: 10px;">
                    @if(!$siteEthic->active)
                        {!! Form::button('<i class="fa fa-check"></i>&nbsp;' . trans('privateSiteEthics.activate_version'), ['class' => 'btn btn-flat btn-success pull-right', 'onclick' => "location.href='".action('SiteEthicsController@activateVersion', ['site_key' => $siteKey,'site_ethic_key' => $siteEthic->site_ethic_key, 'version' => $siteEthic->version])."'" ]) !!}
                    @endif
                </div>
            @endif
            <div class="row">
                <div class="@if(ONE::actionType('siteEthic') == 'show') col-md-8 @endif col-12">
                    @if(count($languages) > 0)
                        @foreach($languages as $language)
                            @php $form->openTabs('tab-translation-' . $language->code, $language->name); @endphp
                            <div style="padding:10px;">
                                @if(ONE::actionType('siteEthic') == 'show')
                                    <dt><i class="fa fa-eye"></i> {{ trans('privateSiteEthics.preview') }}</dt>
                                    <div style="border:1px solid #999999;width:100%;height:350px;overflow-y: auto">
                                        {{html_entity_decode(!empty($siteEthic->translations->{$language->code}->content) ? $siteEthic->translations->{$language->code}->content : null)}}
                                    </div>
                                    <hr style="margin: 10px 0 10px 0">
                                @else
                                    {!! Form::oneTextArea($language->default == true ? 'required_content_'.$language->code : 'content_'.$language->code,
                                                          $title ?? '',
                                                          !empty($siteEthic->translations->{$language->code}->content) ? $siteEthic->translations->{$language->code}->content : null,
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

    <script src="{{ asset("js/tinymce/tinymce.min.js") }}"></script>

    <script>
        $(document).ready(function(){

            {!! ONE::addTinyMCE(".use_term", ['action' => action('ContentsController@getTinyMCE')]) !!}

        });

        //get version selected and reload page to that version
        $("#siteEthicVersions").change(function(){
           var version = this.value;
            var url = window.location.href;
            @if(!empty($actionUrl))
                url = '{{$actionUrl}}/'+version;
            @endif
            window.location = url;

        });

    </script>
@endsection