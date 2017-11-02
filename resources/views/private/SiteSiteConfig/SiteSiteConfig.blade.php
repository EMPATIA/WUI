@extends('private._private.index')

@section('content')

    @php $form = ONE::form('SiteSiteConfig')
            ->settings(["model" => isset($SiteSiteConfig) ? $SiteSiteConfig : null,'id'=>isset($SiteSiteConfig) ? $SiteSiteConfig->id : null])
            ->show('SiteSiteConfigController@edit', 'SiteSiteConfigController@delete', ['roleKey' => isset($SiteSiteConfig) ? $SiteSiteConfig->id : null], 'SiteSiteConfigController@index', ['roleKey' => isset($SiteSiteConfig) ? $SiteSiteConfig->id : null])
            ->create('SiteSiteConfigController@store', 'SiteSiteConfigController@index', ['roleKey' => isset($SiteSiteConfig) ? $SiteSiteConfig->id : null])
            ->edit('SiteSiteConfigController@update', 'SiteSiteConfigController@show', ['roleKey' => isset($SiteSiteConfig) ? $SiteSiteConfig->id : null])
            ->open();
    @endphp

    {!! Form::oneSelect('site_conf_id', trans('privateSiteSiteConf.siteConfId'), isset($siteConfsArray) ? $siteConfsArray : null, isset($SiteSiteConfig) ? $SiteSiteConfig->site_conf_id : null, isset($SiteSiteConfig) ? $SiteSiteConfig->site_conf_name : null, ['class' => 'form-control', 'id' => 'site_conf_id', (isset($SiteSiteConfig->disabled) ? $SiteSiteConfig->disabled : "")]) !!}
    {!! Form::oneSwitch('parameter_value', trans('privateSiteSiteConfig.parameterValue'), isset($SiteSiteConfig) ? $SiteSiteConfig->parameter_value : null, ['class' => 'form-control', 'id' => 'parameter_value']) !!}

    {!! $form->make() !!}

@endsection

@section('scripts')
    <script>

    </script>
@endsection