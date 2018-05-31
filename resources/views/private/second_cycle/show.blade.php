@extends('private._private.index')

@section('header_scripts')
    <!-- Maps -->
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCn-K_QLK1mNPM6SjCjnUl2e3neuQ9FX6Q&libraries=places"
            type="text/javascript"></script>
    <!-- Plupload Javascript fix and bootstrap fix @ start -->
    <link rel="stylesheet" href="/bootstrap/plupload-fix/bootstrap.css">
    <script src="/bootstrap/plupload-fix/bootstrap.min.js"></script>
    <!-- Plupload Javascript fix and bootstrap fix @ End -->
    <script src="{{ asset('vendor/jildertmiedema/laravel-plupload/js/plupload.full.min.js') }}"></script>
    <script src="{{ asset("js/cropper.min.js") }}"></script>
@endsection
@section('content')
    @php
        $form = ONE::form('secondCycle', trans('secondCycle.details'), 'cb','project_2c')
            ->show('SecondCycleController@edit', 'SecondCycleController@delete', ['cbKey' => $cbKey ?? null, "topicKey" => $topicKey ?? null], 'SecondCycleController@index', ['cbKey' => $cbKey ?? null, "topicKey" => $topicKey ?? null])
            ->create('SecondCycleController@store', 'SecondCycleController@index', ['cbKey' => $cbKey ?? null, "topicKey" => $topicKey ?? null])
            ->edit('SecondCycleController@update', 'SecondCycleController@internalShow', ['cbKey' => $cbKey ?? null, "topicKey" => $topicKey ?? null])
            ->open();
    @endphp
    <div id="form-2c-container">
        <div class="box-header">
            <h3 class="box-title">{{trans('secondCycle.details')}}</h3>
        </div>
        <div class="box-body">
            <div class="form-group">
                {!! Form::oneText('title',trans('secondCycle.titleRequired'),$space->getAttribute($topicKey,'title'), ['class' => 'form-control', 'required' => 'required']) !!}
            </div>

            <div class="form-group">
                {!! Form::oneTextArea('description',trans('secondCycle.descriptionRequired'),$space->getAttribute($topicKey,'description'), ['class' => 'form-control', 'rows' => 2, 'required' => 'required']) !!}
            </div>

            <div class="form-group">
                {!! Form::oneText('created_on_behalf',trans('secondCycle.created_on_behalf'),$space->getAttribute($topicKey,'created_on_behalf'), ['class' => 'form-control']) !!}
            </div>

            <div class="form-group">
                {!! Form::oneText('start_date',trans('secondCycle.startDate'),$space->getAttribute($topicKey,'start_date'), ['class' => 'oneDatePicker form-control', "date-format" => "yyyy-mm-dd"]) !!}
            </div>

            <div class="form-group">
                {!! Form::oneText('end_date',trans('secondCycle.endDate'),$space->getAttribute($topicKey,'end_date'), ['class' => 'oneDatePicker form-control', "date-format" => "yyyy-mm-dd"]) !!}
            </div>
            @php foreach($parameters as $p): @endphp
            <div class="form-group">
                @if (in_array($p['type'],array('coin','numeric')))
                    {!! Form::oneNumber($p['code'],(($p['mandatory'] == 1)?"* ".$p['name']:$p['name']),$space->getAttribute($topicKey,$p['code']), ['class' => 'form-control', (($p['mandatory'] == 1)?"required " : "") ]) !!}
                @elseif (in_array($p['type'],array('text')))
                    <div class="form-group">
                        {!! Form::oneText($p['code'],(($p['mandatory'] == 1)?"* ".$p['name']:$p['name']),$space->getAttribute($topicKey,$p['code']), ['class' => 'form-control', (($p['mandatory'] == 1)?"required " : "") ]) !!}
                    </div>
                @elseif (in_array($p['type'],array('date')))
                    <div class="form-group">
                        {!! Form::oneText($p['code'],(($p['mandatory'] == 1)?"* ".$p['name']:$p['name']),$space->getAttribute($topicKey,$p['code']), ['class' => 'oneDatePicker form-control', "date-format" => "yyyy-mm-dd", (($p['mandatory'] == 1)?"required " : "") ]) !!}
                    </div>
                @elseif (in_array($p['type'],array('text_area')))
                    <div>
                        {!! Form::oneTextArea($p['code'],(($p['mandatory'] == 1)?"* ".$p['name']:$p['name']),$space->getAttribute($topicKey,$p['code']), ['class' => 'oneDatePicker form-control', "date-format" => "yyyy-mm-dd", (($p['mandatory'] == 1)?"required " : "") ]) !!}
                    </div>
                @elseif(in_array($p['type'],array('radio_buttons')))
                    {!! Form::label($p['code'], (($p['mandatory'] == 1)?"* ".$p['name']:$p['name']), ['class' => 'form-control-label']) !!}
                    <div>
                        @if(ONE::isEdit())
                            @foreach ($p['options'] as $k => $v)
                                <div class="radio">
                                    <label for="radio_{{$k}}">
                                        @if ($p['mandatory'] == 1)
                                            {!! Form::radio($p['code'], $k, ($space->getAttribute($topicKey,$p['code']) == $v)?true:false, ['id'=>'radio_'.$k, 'required' => 'required']) !!}
                                        @else
                                            {!! Form::radio($p['code'], $k, ($space->getAttribute($topicKey,$p['code']) == $v)?true:false, ['id'=>'radio_'.$k]) !!}
                                        @endif
                                        {{$v}}
                                    </label>
                                </div>
                            @endforeach
                        @else
                            {{ $space->getAttribute($topicKey,$p['code']) }}
                        @endif
                    </div>
                @elseif (in_array($p['type'],array('dropdown')))
                    {!! Form::label($p['code'], (($p['mandatory'] == 1)?"* ".$p['name']:$p['name']), ['class' => 'form-control-label']) !!}
                    <div>
                        @if (ONE::isEdit())
                            @php $opt = array_flip($p['options']); @endphp
                            @if ($p['mandatory'] == 1)
                                {!! Form::select($p['code'], $p['options'], ((!$space->getAttribute($topicKey,$p['code']))?null:$opt[$space->getAttribute($topicKey,$p['code'])]), ['class' => 'form-control', 'required' => 'required']) !!}
                            @else
                                {!! Form::select($p['code'], $p['options'], ((!$space->getAttribute($topicKey,$p['code']))?null:$opt[$space->getAttribute($topicKey,$p['code'])]),['class' => 'form-control']) !!}
                            @endif
                        @else
                            {{ $space->getAttribute($topicKey,$p['code']) }}
                        @endif
                    </div>
                @elseif (in_array($p['type'],array('google_maps')))
                    <div>
                        {!! Form::oneMaps($p['code'],(($p['mandatory'] == 1)?"* ".$p['name']:$p['name']), ((!$space->getAttribute($topicKey,$p['code']))?null:$space->getAttribute($topicKey,$p['code'])),["required" => $p["mandatory"], "defaultLocation" => "38.7436213,-9.1952232", "enableSearch" => true, "readOnly" => (!ONE::isEdit())]) !!}
                    </div>
                @endif

            </div>
            @php endforeach @endphp

        <!-- Files -->

            @if(ONE::isEdit() && isset($configurations) && ((ONE::checkCBsOption($configurations, 'ALLOW-FILES')) || (ONE::checkCBsOption($configurations, 'ALLOW-PICTURES'))))
                <div class="form-group">
                    <div class="parameter">
                        <div class="form-group" id="attachments-container">
                            <label for="title">{{ trans("cb.add_files") }}</label>
                            {!! ONE::fileSimpleUploadBox("drop-zone", trans("cb.drag_and_drop_files_to_here") , trans('PublicCbs.files'), 'select-files', 'files-list', 'files') !!}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
    {!! $form->make() !!}
@endsection
@section('scripts')

    @include('private._private.functions') {{-- Helper Functions --}}

    <script>
        $(function () {
            var array = ["project_2c", "{{$cbKey}}"];
            getSidebar('{{ action("OneController@getSidebar") }}', 'second_cycle', array, 'padsType');
        });
    </script>


    <script>
        var url_updateFiles = '{{action('SecondCycleController@update_files',['cbKey' => $cbKey, 'cbKeyChild' => $cbKeyChild])}}';

        $('#form-2c-container form').on('submit', function (ev) {
            $('#submit-2c-form').attr('disabled', 'disabled');
        });

        $(document).on('files-updated', '#files', function () {
            $.get(url_updateFiles);
        });
    </script>
    <script>
        {!! ONE::fileUploader('fileUploader', action('FilesController@upload'), 'ideaFileUploaded', 'select-files', 'drop-zone', 'files-list', 'files', 1, isset($uploadKey) ? $uploadKey : "", $allowFiles) !!}
fileUploader.init();
        updateClickListener();
        updateFilesPostList('#files', 1);
    </script>
@endsection
