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
            @php $form = ONE::form('flagTypes')
                ->settings(["model" => isset($flagType->id) ? $flagType->id : null, 'id' => isset($flagType->id) ? $flagType->id : null])
                ->show('FlagTypesController@edit', 'FlagTypesController@delete', ['id' => isset($flagType->id) ? $flagType->id : null], 'FlagTypesController@index', ['id' => isset($flagType->id) ? $flagType->id : null])
                ->create('FlagTypesController@store', 'FlagTypesController@index', ['id' => isset($flagType->id) ? $flagType->id : null])
                ->edit('FlagTypesController@update', 'FlagTypesController@show', ['id' => isset($flagType->id) ? $flagType->id : null])
                ->open();
            @endphp
            {{--{{ dd($translations) }}--}}


            {!! Form::oneText('code', trans('privateFlagTypes.code'), isset($flagType) ? $flagType->code : null, ['class' => 'form-control', 'id' => 'code']) !!}
            @if(count($languages) > 0)
                @foreach($languages as $language)
                    @php $form->openTabs('tab-translation-' . $language->code, $language->name); @endphp
                    <div style="padding: 10px;">
                        {!! Form::oneText('title_'.$language->code,
                        trans('privateFlagTypes.title'), isset($translations->{$language->code}) ? $translations->{$language->code}->title : null,
                                           ['class' => 'form-control', 'id' => 'title_'.$language->code, $language->code == 'en' ? 'required' : '']) !!}
                        {!! Form::oneText('description_'.$language->code,
                        trans('privateFlagTypes.description'), isset($translations->{$language->code}) ? $translations->{$language->code}->description : null,
                                           ['class' => 'form-control', 'id' => 'description_'.$language->code, $language->code == 'en' ? 'required' : '']) !!}

                    </div>
                @endforeach
                @php $form->makeTabs(); @endphp
            @endif



            {!! $form->make() !!}
        </div>
    </div>

@endsection


