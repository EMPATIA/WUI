@extends('public._layouts.index')

@section('content')

    <div class="row">
        <div class="col-md-12">

            <?php $form = ONE::form('idea')
                    ->settings(["model" => isset($topic) ? $topic : null])
                    ->show('PublicIdeasController@edit', 'PublicIdeasController@delete', ['cbId' => isset($cbId) ? $cbId : null, 'id' => isset($topic) ? $topic->id : null], 'PublicIdeasMessageController@index', ['id' => isset($topic) ? $topic->id : null])
                    ->create('PublicIdeasController@store', 'PublicIdeasController@index', ['cbId' => isset($cbId) ? $cbId : null, 'id' => isset($topic) ? $topic->id : null])
                    ->edit('PublicIdeasController@update', 'PublicIdeasMessageController@index',  ['cbId' => isset($cbId) ? $cbId : null, 'ideaId' => isset($topic) ? $topic->id : null])
                    ->open()
            ?>

            <div class="col-md-8" >
                {!! Form::oneText('title', trans('topic.title'), isset($topic) ? $topic->title : null, ['class' => 'form-control', 'id' => 'title']) !!}

                {!! Form::oneTextArea('summary', trans('topic.summary'), isset($topic) ? $topic->contents : null, ['class' => 'form-control', 'id' => 'summary', 'size' => '30x2', 'style' => 'resize: vertical']) !!}
                {!! Form::oneTextArea('contents', trans('topic.description'), isset($post) ? $post->contents : null, ['class' => 'form-control', 'id' => 'contents', 'size' => '30x10', 'style' => 'resize: vertical']) !!}

                {!! Form::hidden('cb_id', isset($topic) ? $topic->cb_id : $cbId, ['id' => 'cb_id']) !!}

                @foreach($parameters as $parameter)
                    @if($parameter['code'] == 'dropdown' || $parameter['code'] == 'budget' || $parameter['code'] == 'category')
                        {!! Form::oneSelect('parameter_'.$parameter['id'], $parameter['name'], $parameter['options'], isset($parameter['value'])? $parameter['value'] : null, isset($selectParameter)? $selectParameter : null, ['class' => 'form-control'] ) !!}
                    @elseif($parameter['code'] == 'image_map')
                        {!! Form::hidden('marker_pos_x_'.$parameter['id'], isset($posX) ? $posX: null, ['id' => 'marker_pos_x']) !!}
                        {!! Form::hidden('marker_pos_y_'.$parameter['id'], isset($posY) ? $posY: null, ['id' => 'marker_pos_y']) !!}
                    @endif
                @endforeach
            </div>
            <div class="col-md-4">
                <div class="form-group" style="text-align: center">
                    <label for="title" style="margin-bottom: 0px;">{{trans('ideas.map')}}</label>
                    @if(ONE::actionType('idea') != 'show')
                        <small style="display: block; color: #62a351; text-align: center">Click on map to select the area</small>
                    @endif

                    <div id="wrapper" style="margin-top: 10px; text-align: center; margin-left: 35px;">
                        <img id="#empaville_map" src="{{URL::action('FilesController@download',[$fileId, $fileCode, 1])}}" class="pin" style="width: 265px">
                    </div>
                </div>

                <div class="form-group" style="padding-top: 350px">
                    <label for="title">Location</label>
                    <input class="form-control" id="location" name="location" type="text" value="" disabled>
                </div>
            </div>


            @if(ONE::actionType('idea') != 'create')
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="title">Add Files</label>
                        {!! ONE::fileSimpleUploadBox("drop-zone", 'Drag and drop files to here', trans('ideas.files'), 'select-files', 'files-list', 'files') !!}
                    </div>
                </div>
            @endif


            {!! $form->make() !!}
        </div>
    </div>

    <svg src="/images/pin/MapPin2.svg" width="100" height="100">

    </svg>
@endsection

<style>
    #wrapper { position: relative; }
    #wrapper img, #wrapper .marker { position: absolute; }
    #wrapper .marker { z-index: 100;top: 65px; }
    #wrapper img { top: 0px; left: 0; }
</style>

@section('scripts')

    @if(ONE::actionType('idea') != 'create')

        <script src="{{ asset('vendor/jildertmiedema/laravel-plupload/js/plupload.full.min.js') }}"></script>
        <script src="{{ asset("js/cropper.min.js") }}"></script>



        @include('private._private.functions') {{-- Helper Functions --}}
        <script>
            {!! ONE::fileUploader('fileUploader', action('FilesController@upload'), 'ideaFileUploaded', 'select-files', 'drop-zone', 'files-list', 'files', 1, isset($uploadKey) ? $uploadKey : "") !!}
            fileUploader.init();

            updateClickListener();

            updateFilesPostList('#files');
        </script>
    @endif
    <script>
        var $wrapper = $('#wrapper');

        $( document ).ready(function() {

            var pos_x = $("#marker_pos_x").val();
            var pos_y = $("#marker_pos_y").val();

            if(pos_x.length > 0 && pos_y.length > 0){
                $('<img id="" src="/images/map_pin.png">').addClass('marker').css({
                    left: pos_x,
                    top: pos_y

                }).appendTo($wrapper);
            }

            @if(ONE::actionType('idea') != 'show')
                resizeImage();
            @endif


            showLocationName(pos_y);


        });

        function resizeImage(){
            $('#wrapper img').click(function(e) {
                var $this = $(this);
                $( ".marker" ).remove();
                //}
                var offset = $this.offset();
                $('<img id="" src="/images/map_pin.png">').addClass('marker').css({
                    left: e.pageX-offset.left-20,
                    top: e.pageY-offset.top-65

                }).appendTo($wrapper);

                $("#marker_pos_x").val((e.pageX-offset.left - 20));
                $("#marker_pos_y").val((e.pageY-offset.top-65));

                showLocationName((e.pageY-offset.top-65));
            });
        }


        function showLocationName(pos_Y){

            var locationName = 'Without location set';

            if(pos_Y != ''){
                if( pos_Y > 153 ) {
                    locationName = "DownTown";
                }else if ( pos_Y < 60 ){
                    locationName = "UpTown";
                }else{
                    locationName = "MiddleTown";
                }
            }
            $("#location").val(locationName);
        }
    </script>


@endsection