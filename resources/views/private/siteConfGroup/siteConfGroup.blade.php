@extends('private._private.index')

@section('content')

    @php $form = ONE::form('siteConfGroup')
            ->settings(["model" => isset($siteConfGroup) ? $siteConfGroup : null,'id'=>isset($siteConfGroup) ? $siteConfGroup->site_conf_group_key : null])
            ->show('SiteConfGroupController@edit', 'SiteConfGroupController@delete', ['roleKey' => isset($siteConfGroup) ? $siteConfGroup->site_conf_group_key : null], 'SiteConfGroupController@index', ['roleKey' => isset($siteConfGroup) ? $siteConfGroup->code : null])
            ->create('SiteConfGroupController@store', 'SiteConfGroupController@index', ['roleKey' => isset($siteConfGroup) ? $siteConfGroup->site_conf_group_key : null])
            ->edit('SiteConfGroupController@update', 'SiteConfGroupController@show', ['roleKey' => isset($siteConfGroup) ? $siteConfGroup->site_conf_group_key : null])
            ->open();
    @endphp
    {!! Form::oneText('code', trans('privateSiteConfGroup.code'), isset($siteConfGroup) ? $siteConfGroup->code : null, ['class' => 'form-control', 'id' => 'code']) !!}

    @if(isset($languages))
        @foreach($languages as $language)
            @php $form->openTabs('tab-translation-' . $language->code, $language->name); @endphp
            <div style="padding: 10px;">
                {!! Form::oneText('name_'.$language->code .'', trans('privateSiteConfGroup.name'), isset($configTranslation[$language->code]) ? $configTranslation[$language->code]['name'] : null, ['class' => 'form-control', 'id' => 'title_'.$language->code .'']) !!}
                {!! Form::oneTextArea('description_'.$language->code .'', trans('privateSiteConfGroup.description'), isset($configTranslation[$language->code]) ? $configTranslation[$language->code]['description'] : null, ['class' => 'form-control', 'id' => 'description_'.$language->code .'']) !!}
            </div>
        @endforeach
        @php $form->makeTabs(); @endphp
    @endif

    {!! $form->make() !!}

@endsection

@section('scripts')
    <script>
        @if(ONE::actionType('siteConfGroup') == "show")
            $(function () {

            $('#siteConfsList').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}'
                },
                processing: true,
                serverSide: true,
                ajax: '{!! action('SiteConfGroupController@getConfsOfGroup',$siteConfGroup->site_conf_group_key) !!}',
                columns: [
                    { data: 'code', name: 'code', width: "50px"  },
                    { data: 'name', name: 'name' },
                    { data: 'action', name: 'action', searchable: false, orderable: false, width: "30px" },
                ],
                order: [['1', 'asc']]
            });
        });
        @endif
    </script>
@endsection