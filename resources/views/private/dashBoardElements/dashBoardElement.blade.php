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
            @php $form = ONE::form('dashBoardElements')
                ->settings(["model" => isset($dashBoardElement->id) ? $dashBoardElement->id : null, 'id' => isset($dashBoardElement->id) ? $dashBoardElement->id : null])
                ->show('DashBoardElementsController@edit', 'DashBoardElementsController@delete', ['id' => isset($dashBoardElement->id) ? $dashBoardElement->id : null], 'DashBoardElementsController@index', ['id' => isset($dashBoardElement->id) ? $dashBoardElement->id : null])
                ->create('DashBoardElementsController@store', 'DashBoardElementsController@index', ['id' => isset($dashBoardElement->id) ? $dashBoardElement->id : null])
                ->edit('DashBoardElementsController@update', 'DashBoardElementsController@show', ['id' => isset($dashBoardElement->id) ? $dashBoardElement->id : null])
                ->open();
            @endphp
            {{--{{ dd($translations) }}--}}

            {!! Form::oneText('code', trans('privateDashBoardElements.code'), isset($dashBoardElement) ? $dashBoardElement->code : null, ['class' => 'form-control', 'id' => 'code']) !!}

            {!! Form::oneNumber('default_position', trans('privateDashBoardElements.default_position'), isset($dashBoardElement) ? $dashBoardElement->default_position : null, ['class' => 'form-control', 'id' => 'default_position']) !!}
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

            @if(ONE::actionType('dashBoardElements') != 'create')
                <div class="form-group" style="padding-bottom: 20px">
                    <label for="dashBoardElementConfigurations">{{trans('privateDashBoardElements.dashBoardElementConfigurations')}}</label>
                    <div for="dashBoardElementConfigurations"
                         style="font-size:x-small;margin-bottom: 5px;">{{trans('privateDashBoardElements.dashBoardElementConfigurationsDescription')}}</div>
                    <select id="dashBoardElementConfigurations" name="dashBoardElementConfigurations[]"
                            class="form-control" multiple
                            @if(ONE::actionType('dashBoardElements') == 'show') disabled @endif>
                        @forelse($dashBoardElement->available_configurations as $key => $configuration)
                            <option value="{!! $configuration->id !!}"
                                    @if (!empty(collect($dashBoardElement->configurations)->where('id','=',$configuration->id)->first())) selected @endif>{!! $configuration->title !!}</option>
                        @empty
                            {{ trans('privateDashBoardElements.no_configurations_available') }}
                        @endforelse
                    </select>
                </div>
            @endif



            {!! $form->make() !!}
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        $("#dashBoardElementConfigurations").select2({
            'placeholder': "{{ trans('privateDashBoardElements.select_configurations') }}"
        });
    </script>



@endsection

