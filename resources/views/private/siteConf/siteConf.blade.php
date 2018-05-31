@extends('private._private.index')

@section('content')
    @php $form = ONE::form('siteConfigurations')
            ->settings(["model" => isset($siteConf) ? $siteConf : null,'id'=>isset($siteConf) ? $siteConf->site_conf_key  : null])
            ->show('SiteConfigurationsController@edit', 'SiteConfigurationsController@delete', ['siteConfGroup' => isset($siteConfGroupKey) ? $siteConfGroupKey : null,'siteConf' => isset($siteConf) ? $siteConf->site_conf_key : null], 'SiteConfGroupController@showSiteConfGroupConfigurations', ['siteConfGroup' => isset($siteConfGroupKey) ? $siteConfGroupKey : null,'siteConf' => isset($siteConf) ? $siteConf->site_conf_key : null])
            ->create('SiteConfigurationsController@store', 'SiteConfigurationsController@show', ['siteConfGroup' => isset($siteConfGroupKey) ? $siteConfGroupKey : null,'siteConf' => isset($siteConf) ? $siteConf->site_conf_key : null])
            ->edit('SiteConfigurationsController@update', 'SiteConfigurationsController@show', ['siteConfGroup' => isset($siteConfGroupKey) ? $siteConfGroupKey : null,'siteConf' => isset($siteConf) ? $siteConf->site_conf_key : null])
            ->open();
    @endphp

    {!! Form::oneText('code', trans('privateSiteConf.code'), isset($siteConf) ? $siteConf->code : null, ['class' => 'form-control', 'id' => 'code']) !!}
    {!! Form::oneSelect('group', trans('privateSiteConf.group'), isset($siteConfGroupsToSelect) ? $siteConfGroupsToSelect : null, (isset($siteConf) ? $siteConf->site_conf_group_id : (isset($siteConfGroupSelected) ? $siteConfGroupSelected : null)), null, null, ['class' => 'form-control', 'id' => 'group', (isset($siteConfGroupDisabled) ? $siteConfGroupDisabled : null)]) !!}

    @if(isset($languages))
        @foreach($languages as $language)
            @php $form->openTabs('tab-translation-' . $language->code, $language->name); @endphp
            <div style="padding: 10px;">
                {!! Form::oneText('name_'.$language->code .'', trans('privateSiteConf.name'), isset($configTranslation[$language->code]) ? $configTranslation[$language->code]['name'] : null, ['class' => 'form-control', 'id' => 'title_'.$language->code .'']) !!}
                {!! Form::oneTextArea('description_'.$language->code .'', trans('privateSiteConf.description'), isset($configTranslation[$language->code]) ? $configTranslation[$language->code]['description'] : null, ['class' => 'form-control', 'id' => 'description_'.$language->code .'']) !!}
            </div>
        @endforeach
        @php $form->makeTabs(); @endphp
    @endif

    {!! $form->make() !!}

@endsection