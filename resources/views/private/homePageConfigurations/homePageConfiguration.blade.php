@extends('private._private.index')
@section('header_styles')
    <link href="{{ asset("css/cropper.min.css") }}" rel='stylesheet' type='text/css'>
@endsection
@section('content')

    @php $form = ONE::form('homePageConfigurations')
            ->settings(["model" => isset($homePageConfigurationGroup) ? $homePageConfigurationGroup : null, 'id' => isset($homePageConfigurationGroup) ? $homePageConfigurationGroup->group_key : null])
            ->show('HomePageConfigurationsController@edit', 'HomePageConfigurationsController@delete', ['key' => isset($homePageConfigurationGroup) ? $homePageConfigurationGroup->group_key : null], null, ['entityKey' => isset($entityKey) ? $entityKey : null, 'siteKey' => isset($homePageConfigurationGroup->site->key) ? $homePageConfigurationGroup->site->key : null])
            ->create('HomePageConfigurationsController@store', 'EntitiesController@showEntitySite', ['entityKey' => (isset($entityKey) ? $entityKey : ONE::getEntityKey()), 'siteKey' => isset($siteKey) ? $siteKey : null])
            ->edit('HomePageConfigurationsController@update', 'HomePageConfigurationsController@show', ['key' => isset($homePageConfigurationGroup) ? $homePageConfigurationGroup->group_key : null])
            ->open();
    @endphp
    {!! Form::hidden('siteKey',isset($siteKey) ? $siteKey : '') !!}
    {!! Form::hidden('group_key',isset($homePageConfigurationGroup) ? $homePageConfigurationGroup->group_key : '') !!}
    {!! Form::oneText('groupName', trans('homePageConfiguration.groupName'), isset($homePageConfigurationGroup) ? $homePageConfigurationGroup->group_name : null, ['class' => 'form-control', 'id' => 'groupName', ((ONE::actionType('homePageConfigurations') != 'create')? 'readonly' : null)]) !!}
    @if(ONE::actionType('homePageConfigurations') == 'show')

        @foreach($homePageConfigurationGroup->home_page_configurations as $config)
            {!! Form::oneText('value', isset($config->home_page_type) ? $config->home_page_type->name : null, isset($config) ? $config->value : null, ['class' => 'form-control', 'id' => 'groupName', ((ONE::actionType('homePageConfigurations') != 'create')? 'readonly' : null)]) !!}
        @endforeach
    @endif
    @if(ONE::actionType('homePageConfigurations') != 'show')
        @if(count($homePageType->childs) == 0)

            {!! Form::hidden('typeCode_'.(isset($homePageType) ? $homePageType->home_page_type_key : ''),isset($homePageType) ? $homePageType->type_code : '') !!}
            {!! Form::hidden('homePageTypeKeys[]',isset($homePageType) ? $homePageType->home_page_type_key : '') !!}
            @if($homePageType->type_code == 'text')
                @include('private.homePageConfigurations.configurationText')
            @elseif($homePageType->type_code == 'text_area')
                @include('private.homePageConfigurations.configurationTextArea')
            @elseif($homePageType->type_code == 'link')
                @include('private.homePageConfigurations.configurationLink')
            @elseif($homePageType->type_code == 'internal_link')
                @include('private.homePageConfigurations.configurationInternalLink')
            @elseif($homePageType->type_code == 'image')
                @if(ONE::actionType('homePageConfigurations') != 'show')
                    <div id="editImage">
                        <p>{!! ONE::fileUploadBox("image-drop-zone", trans('homePageConfiguration.drop-zone'), (isset($homePageType) ? $homePageType->name : null), 'select-banner', 'banner-list', 'files_banner') !!}</p>
                    </div>
                    {!! ONE::imageCropModal('getCroppedCanvasModal', 'getCroppedCanvasTitle', trans('homePageConfiguration.resize')) !!}
                @endif
            @endif
        @elseif(count($homePageType->childs) > 0)
            <!--COMECA AQUI -->

            <!--AGRUPAR LANGUAGES -->
            @if(isset($languages))
                @foreach($languages as $language)
                    @php $form->openTabs('tab-all-translations' . $language->code, $language->name); @endphp

                    @foreach($homePageType->childs as $child)
                        @if($child->type_code == 'text')
                            @include('private.homePageConfigurations.configurationText')
                        @elseif($child->type_code == 'text_area')
                            @include('private.homePageConfigurations.configurationTextArea')
                        @endif

                    @endforeach
                @endforeach
                @php $form->makeTabs(); @endphp
            @endif

            <!-- TRATAR RESTO TPL'S -->
            @foreach($homePageType->childs as $child)
                {!! Form::hidden('typeCode_'.(isset($child) ? $child->home_page_type_key : ''),isset($child) ? $child->type_code : '') !!}
                {!! Form::hidden('homePageTypeKeys[]',isset($child) ? $child->home_page_type_key : '') !!}

                @if($child->type_code == 'link')
                    @include('private.homePageConfigurations.configurationLink')
                @elseif($child->type_code == 'internal_link')
                    @include('private.homePageConfigurations.configurationInternalLink')
                @elseif($child->type_code == 'image')
                    @if(ONE::actionType('homePageConfigurations') != 'show')
                        {!! Form::hidden('imageLink',
                            isset($homePageConfiguration[(isset($child) ? $child->home_page_type_key : $homePageType->home_page_type_key)]) ?
                            $homePageConfiguration[(isset($child) ? $child->home_page_type_key : $homePageType->home_page_type_key)]['value'] : null,
                            ['id' => 'imageLink']) !!}
                        <div id="{{(isset($child) ? $child->home_page_type_key : '')}}">
                            <p>{!! ONE::fileUploadBox("image-drop-zone", trans('homePageConfiguration.drop-zone'), (isset($child) ? $child->name : null), 'select-banner', 'banner-list', 'files_banner') !!}</p>
                        </div>
                        {!! ONE::imageCropModal('getCroppedCanvasModal', 'getCroppedCanvasTitle', trans('homePageConfiguration.resize')) !!}
                    @endif
                @endif
            @endforeach
        @endif
    @endif

    @if(ONE::actionType('homePageConfigurations') == 'show')
        {!! Form::oneText('value', trans('homePageConfiguration.value'), isset($homePageConfiguration) ? $homePageConfiguration->value : null, ['class' => 'form-control', 'id' => 'value']) !!}
    @endif
    {!! $form->make() !!}

@endsection

@section('scripts')
    <!-- Plupload Javascript fix and bootstrap fix @ start -->
    <link rel="stylesheet" href="/bootstrap/plupload-fix/bootstrap.css">
    <script src="/bootstrap/plupload-fix/bootstrap.min.js"></script>
    <!-- Plupload Javascript fix and bootstrap fix @ End -->
    <script src="{{ asset('vendor/jildertmiedema/laravel-plupload/js/plupload.full.min.js') }}"></script>
    <script src="{{ asset("js/cropper.min.js") }}"></script>
    @include('private._private.functions') {{-- Helper Functions --}}
    <script>
        {!! ONE::imageUploader('bannerUploader', action('FilesController@upload'), 'homeConfigurationImageUploaded', 'select-banner', 'image-drop-zone', 'banner-list', 'files_banner', 'getCroppedCanvasModal', 0, 0, isset($uploadKey) ? $uploadKey : "") !!}
        bannerUploader.init();

        updateClickListener();
    </script>
    <script>

        /* ------------ Menu Type ------------ */
        function changeType(selectedObj){
            var value = selectedObj.value;
            var str = selectedObj.id;
            var res = str.split("_");
            var id = res[1];
            $(".types").hide();
            $("#typeId"+value+"_"+id).slideDown();
            $(".typeId"+value+"_"+id).slideDown();
        }

        function changeTypeValue(selectedObj){
            var value = selectedObj.value;
            var str = selectedObj.id;
            var res = str.split("_");
            var id = res[1];
            $("#value_"+id).val(value);

        }
    </script>
@endsection