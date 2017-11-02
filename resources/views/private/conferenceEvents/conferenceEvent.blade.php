@extends('private._private.index')
@section('header_styles')
    <link href="{{ asset("css/cropper.min.css") }}" rel='stylesheet' type='text/css'>
@endsection
@section('content')
    @php
    $form = ONE::form('conferenceEvents')
            ->settings(["model" => isset($event) ? $event->event_key : null,'id'=>isset($event) ? $event->event_key : null])
            ->show('ConferenceEventsController@edit', 'ConferenceEventsController@delete', ['id' => isset($event) ? $event->event_key : null], 'ConferenceEventsController@index', ['id' => isset($event) ? $event->event_key : null])
            ->create('ConferenceEventsController@store', 'ConferenceEventsController@index', ['id' => isset($event) ? $event->event_key : null])
            ->edit('ConferenceEventsController@update', 'ConferenceEventsController@show', ['id' => isset($event) ? $event->event_key : null])
            ->open();
    @endphp
    {!! Form::hidden('event_key', isset($event) ? $event->event_key : null) !!}
    {!! Form::hidden('fileId', isset($event) ? $event->file_id : null, ['id' => 'fileId']) !!}
    @if((ONE::actionType('conferenceEvents') == 'show') && (isset($file)))
        <div class="box-body">
            <div class="col-lg-3">
                <img class="img"  src="https://empatia-test.onesource.pt:5005/file/download/{!! $file['id']  !!}/{!! $file['code'] !!}"  id="image_sponsor">
            </div>
        </div>
    @endif
    @if(count($languages) > 0)
        @foreach($languages as $language)
            @php $form->openTabs('tab-translation-' . $language->code, $language->name); @endphp
            <div style="padding: 10px;">

                {!! Form::oneText('title_'.$language->code .'', trans('conferenceEvents.title_'.$language->code .''), isset($translations[$language->code]) ? $translations[$language->code]->name : null, ['class' => 'form-control', 'id' => 'title_'.$language->code , $language->default == 1 ? 'required' : '']) !!}
                {!! Form::oneTextArea('description_'.$language->code .'', trans('conferenceEvents.description_'.$language->code .''), isset($translations[$language->code]) ? $translations[$language->code]->description : null, ['class' => 'form-control wysiwyg', 'id' => 'summary_'.$language->code, $language->default == 1 ? 'required' : '']) !!}
                {!! Form::oneTextArea('footer_'.$language->code .'', trans('conferenceEvents.footer_'.$language->code .''), isset($translations[$language->code]) ? $translations[$language->code]->footer : null, ['class' => 'form-control wysiwyg', 'id' => 'footer_'.$language->code .'']) !!}
                
            </div>
        @endforeach
        @php $form->makeTabs(); @endphp
    @endif
    {!! Form::oneDate('startDate', trans('conferenceEvents.startDate'), isset($event) ? substr($event->start_date, 0, 10) : null, ['id' => 'startDate', 'readonly' => isset($event)?($event->start_date < date('Y-m-d') ? 'readonly' : null):null]) !!}
    {!! Form::oneDate('endDate', trans('conferenceEvents.endDate'), isset($event) ? substr($event->end_date, 0, 10) : null, ['id' => 'endDate']) !!}

    @if(ONE::actionType('conferenceEvents') != 'show')

        <div id="editImage">
            <p>{!! ONE::fileUploadBox("banner-drop-zone", trans('files.drop-zone'), trans('conferenceEvents.banners'), 'select-banner', 'banner-list', 'files_banner') !!}</p>
        </div>
        {!! ONE::imageCropModal('getCroppedCanvasModal', 'getCroppedCanvasTitle', trans('conferenceEvents.resize')) !!}
    @endif

    @if(ONE::actionType('conferenceEvents') == 'show')
        <div class="card flat">
            <div class="card-header">Sessions</div>
            <div class="box-body">
                <table id="session_list" class="table table-hover table-striped dataTable no-footer table-responsive">
                    <thead>
                    <tr>
                        <th>{{ trans('conferenceEvents.key') }}</th>
                        <th>{{ trans('conferenceEvents.name') }}</th>
                        <th>{!! ONE::actionButtons($event->event_key, ['create' => 'ConferenceEventSessionController@create']) !!}</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>

        <div class="card flat">
            <div class="card-header">Sponsors</div>
            <div class="box-body">
                <table id="sponsor_list" class="table table-hover table-striped dataTable no-footer table-responsive">
                    <thead>
                    <tr>
                        <th>{{ trans('conferenceEvents.key') }}</th>
                        <th>{{ trans('conferenceEvents.name') }}</th>
                        <th>{!! ONE::actionButtons(['eventKey' =>$event->event_key], ['create' => 'ConferenceEventSponsorsController@create']) !!}</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    @endif

    {!! $form->make() !!}

@endsection

@section('scripts')

    <script src="{{ asset("js/tinymce/tinymce.min.js") }}"></script>
    <script>
        $( document ).ready(function() {
            {!! ONE::addTinyMCE(".wysiwyg",array("plugins" => [])) !!}
         });
    </script>
    <!-- Plupload Javascript fix and bootstrap fix @ start -->
    <link rel="stylesheet" href="/bootstrap/plupload-fix/bootstrap.css">
    <script src="/bootstrap/plupload-fix/bootstrap.min.js"></script>
    <!-- Plupload Javascript fix and bootstrap fix @ End -->
    <script src="{{ asset('vendor/jildertmiedema/laravel-plupload/js/plupload.full.min.js') }}"></script>
    <script src="{{ asset("js/cropper.min.js") }}"></script>
    @include('private._private.functions') {{-- Helper Functions --}}
    <script>

        {!! ONE::imageUploader('bannerUploader', env('UPLOAD_API', 'https://empatia-dev.onesource.pt:5505/file/upload/'), 'imageEventUploaded', 'select-banner', 'banner-drop-zone', 'banner-list', 'files_banner', 'getCroppedCanvasModal', 0, 0, isset($uploadKey) ? $uploadKey : "") !!}
        bannerUploader.init();

        updateClickListener();
        updateFileEventList('#files_banner');


    </script>
    @if(ONE::actionType('conferenceEvents') == 'show')
        <script>


            $(function () {
                $('#session_list').DataTable({
                    language: {
                        url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}'
                    },
                    processing: true,
                    serverSide: true,
                    ajax: '{!! action('ConferenceEventSessionController@getIndexTable',['eventKey'=>$event->event_key]) !!}',
                    columns: [
                        { data: 'sessionKey', name: 'sessionKey', width: "20px" },
                        { data: 'title', name: 'title' },
                        { data: 'action', name: 'action', searchable: false, orderable: false, width: "30px" },
                    ],
                    order: [['1', 'asc']],
                    responsive: true                      
                });

            });


            $(function () {
                $('#sponsor_list').DataTable({
                    language: {
                        url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}'
                    },
                    processing: true,
                    serverSide: true,
                    ajax: '{!! action('ConferenceEventSponsorsController@getIndexTable',['eventKey'=>$eventKey]) !!}',
                    columns: [
                        { data: 'sponsor_key', name: 'sponsor_key', width: "20px" },
                        { data: 'title', name: 'title' },
                        { data: 'action', name: 'action', searchable: false, orderable: false, width: "30px" },
                    ],
                    order: [['1', 'asc']],
                    responsive: true                       
                });

            });

        </script>
    @endif
    <script>
        $('.oneDatePicker').datepicker({
            startDate: new Date()
        });

    </script>

@endsection


