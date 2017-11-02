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
            @php $form = ONE::form('flags')
                ->settings(["model" => isset($flag->id) ? $flag->id : null, 'id' => isset($flag->id) ? $flag->id : null])
                ->show('FlagsController@edit', 'FlagsController@delete', ['id' => isset($flag->id) ? $flag->id : null,'type' => $type, 'cbKey' => $cbKey], 'FlagsController@index', ['type' => isset($type) ? $type : null,'cbKey' => isset($cbKey) ? $cbKey : null])
                ->create('FlagsController@store', 'FlagsController@index', ['id' => isset($flag->id) ? $flag->id : null, 'type' => $type, 'cbKey' => $cbKey])
                ->edit('FlagsController@update', 'FlagsController@show', ['id' => isset($flag->id) ? $flag->id : null,'type' => $type, 'cbKey' => $cbKey])
                ->open();
            @endphp

            {!! Form::hidden('type',$type, ['id' => 'type']) !!}
            {!! Form::hidden('cbKey',$cbKey, ['id' => 'cbKey']) !!}


            @if(ONE::actionType('flags') != 'show')
                <div class="form-group ">
                    <label for="flag_type">{{trans('privateFlags.flag_type')}}</label>
                    <div for="flag_type"  style="font-size:x-small">{{trans('privateFlags.flag_type_description')}}</div>
                    <select id="flag_type" class="form-control" name="flag_type">
                        <option selected="selected" value="">{{trans('privateFlags.select_value')}}</option>
                        @foreach($flagTypes as $flagType)

                            <option value="{{$flagType->id}}" {{ isset($flag) ? (($flag->flag_type_id == $flagType->id) ? 'selected' : '') : '' }}>{{collect($flagType->current_language_translation)->first()->title}}</option>
                        @endforeach
                    </select>
                </div>
            @else
                {!! Form::oneText('flag_type', array("name"=>trans('privateFlags.flag_type'),"description"=>trans('privateFlags.flag_type_description')), isset($flagType) ? collect($flagType->current_language_translation)->first()->title : null, ['class' => 'form-control', 'id' => 'flag_type', 'required' => 'required']) !!}
            @endif
            {!! Form::oneSwitch("private_flag", array("name"=>trans('privateFlags.private_flag'),"description"=>trans('privateFlags.private_flag_description')), isset($flag->private_flag) ? (($flag->private_flag == true) ? 'checked':''):'', ["id"=>"private_flag"]) !!}

            {!! Form::oneSwitch("public_visible", array("name"=>trans('privateFlags.public_visible'),"description"=>trans('privateFlags.public_visible_description')), isset($flag->public_visible) ? (($flag->public_visible == true) ? 'checked':''):'', ["id"=>"public_visible"]) !!}

            {!! Form::oneSwitch("flag_visible", array("name"=>trans('privateFlags.flag_visible'),"description"=>trans('privateFlags.flag_visible_description')), isset($flag->flag_visible) ? (($flag->flag_visible == true) ? 'checked':''):'', ["id"=>"flag_visible"]) !!}

            @if(count($languages) > 0)
                @foreach($languages as $language)
                    @php $form->openTabs('tab-translation-' . $language->code, $language->name); @endphp
                    <div style="padding: 10px;">
                        {!! Form::oneText('title_'.$language->code,
                        trans('privateFlags.title'), isset($translations[$language->code]) ? $translations[$language->code]->title : null,
                                           ['class' => 'form-control', 'id' => 'title_'.$language->code, $language->code == 'en' ? 'required' : '']) !!}
                        {!! Form::oneText('description_'.$language->code,
                        trans('privateFlags.description'), isset($translations[$language->code]) ? $translations[$language->code]->description : null,
                                           ['class' => 'form-control', 'id' => 'description_'.$language->code, $language->code == 'en' ? 'required' : '']) !!}

                    </div>
                @endforeach
                @php $form->makeTabs(); @endphp
            @endif



            {!! $form->make() !!}
        </div>
    </div>

@endsection


