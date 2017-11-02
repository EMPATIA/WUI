@extends('private._private.index')

@section('header_scripts')
    <!-- Plupload Javascript fix and bootstrap fix @ start -->
    <link rel="stylesheet" href="/bootstrap/plupload-fix/bootstrap.css">
    <script src="/bootstrap/plupload-fix/bootstrap.min.js"></script>
    <!-- Plupload Javascript fix and bootstrap fix @ End -->
    <script src="{{ asset('vendor/jildertmiedema/laravel-plupload/js/plupload.full.min.js') }}"></script>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            @php $form = ONE::form('dashBoardElementConfigurations')
                ->settings(["model" => isset($dashBoardElementConfiguration->id) ? $dashBoardElementConfiguration->id : null, 'id' => isset($dashBoardElementConfiguration->id) ? $dashBoardElementConfiguration->id : null])
                ->show('DashBoardElementConfigurationsController@edit', 'DashBoardElementConfigurationsController@delete', ['id' => isset($dashBoardElementConfiguration->id) ? $dashBoardElementConfiguration->id : null], 'DashBoardElementConfigurationsController@index', ['id' => isset($dashBoardElementConfiguration->id) ? $dashBoardElementConfiguration->id : null])
                ->create('DashBoardElementConfigurationsController@store', 'DashBoardElementConfigurationsController@index', ['id' => isset($dashBoardElementConfiguration->id) ? $dashBoardElementConfiguration->id : null])
                ->edit('DashBoardElementConfigurationsController@update', 'DashBoardElementConfigurationsController@show', ['id' => isset($dashBoardElementConfiguration->id) ? $dashBoardElementConfiguration->id : null])
                ->open();
            @endphp
            {{--{{ dd($translations) }}--}}

            {!! Form::oneText('code', trans('privateDashBoardElements.code'), isset($dashBoardElementConfiguration) ? $dashBoardElementConfiguration->code : null, ['class' => 'form-control', 'id' => 'code']) !!}

            {!! Form::oneText('type', trans('privateDashBoardElements.type'), isset($dashBoardElementConfiguration) ? $dashBoardElementConfiguration->type : null, ['class' => 'form-control', 'id' => 'type']) !!}

            {!! Form::oneText('default_value', trans('privateDashBoardElements.default_value'), isset($dashBoardElementConfiguration->default_value) ? $dashBoardElementConfiguration->default_value : null, ['class' => 'form-control', 'id' => 'default_value']) !!}


            @if(count($languages) > 0)
                @foreach($languages as $language)
                    @php $form->openTabs('tab-translation-' . $language->code, $language->name); @endphp
                    <div style="padding: 10px;">
                        {!! Form::oneText('title_'.$language->code,
                        trans('privateDashBoardElements.title'), isset($translations->{$language->code}) ? $translations->{$language->code}->title : null,
                                           ['class' => 'form-control', 'id' => 'title_'.$language->code, $language->code == 'en' ? 'required' : '']) !!}
                        {!! Form::oneText('description_'.$language->code,
                        trans('privateDashBoardElements.description'), isset($translations->{$language->code}) ? $translations->{$language->code}->description : null,
                                           ['class' => 'form-control', 'id' => 'description_'.$language->code, $language->code == 'en' ? 'required' : '']) !!}

                    </div>
                @endforeach
                @php $form->makeTabs(); @endphp
            @endif



            {!! $form->make() !!}
        </div>
    </div>

@endsection


