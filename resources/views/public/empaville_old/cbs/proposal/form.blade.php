@extends('public.empaville._layouts.index')

@section('content')


    <div class="BP-titleMain">
        <div class="container">
            <div class="col-md-9">
                <h1>{!! trans('PublicCbs.createProposal') !!}</h1>
            </div>

        </div>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <?php $form = ONE::form('topic')
                        ->settings(["model" => isset($topic) ? $topic : null])
                        ->show('PublicTopicController@edit', 'PublicTopicController@delete', ['cbKey' => $cbKey, 'id' => isset($topic) ? $topic->topic_key : null, 'type' => $type], 'PublicTopicController@index', ['cbKey' => $cbKey, 'type' => $type])
                        ->create('PublicTopicController@store', 'PublicCbsController@show', ['cbKey' => $cbKey, 'type' => $type])
                        ->edit('PublicTopicController@update', 'PublicTopicController@show', ['cbKey' => $cbKey, 'id' => isset($topic) ? $topic->topic_key : null, 'type' => $type])
                        ->open()
                ?>
                <div class="row">
                    <div class="col-md-6">
                        {!! Form::oneText('title', trans('PublicCbs.title'), isset($topic) ? $topic->title : null, ['class' => 'form-control', 'id' => 'title', 'required']) !!}
                        {!! Form::oneTextArea('summary', trans('PublicCbs.summary'), isset($topic) ? $topic->contents : null, ['class' => 'form-control', 'id' => 'summary', 'size' => '30x6', 'style' => 'resize: vertical','required']) !!}
                        {!! Form::oneTextArea('contents', trans('PublicCbs.description'), isset($post) ? $post->contents : null, ['class' => 'form-control', 'id' => 'contents', 'size' => '30x14', 'style' => 'resize: vertical','required']) !!}
                        {!! Form::hidden('cb_key', isset($topic) ? $topic->id : $cbKey, ['id' => 'cb_key']) !!}
                        {!! Form::hidden('type', isset($type) ? $type : '', ['id' => 'type']) !!}


                    </div>
                    <div class="col-md-6">
                        @foreach($parameters as $param)
                            @if($param['code'] == 'text' || $param['code'] == 'numeric')
                                {!! Form::oneText('parameter_'.$param['id'], $param['name'], isset($topicParameters[$param['id']])? $topicParameters[$param['id']]->pivot->value : null, ['class' => 'form-control',($param['mandatory'] == 1)?'Required':'']) !!}

                            @elseif($param['code'] == 'text_area')
                                {!! Form::oneTextArea('parameter_'.$param['id'], $param['name'], isset($topicParameters[$param['id']])? $topicParameters[$param['id']]->pivot->value : null, ['class' => 'form-control', 'size' => '30x2', 'style' => 'resize: vertical',($param['mandatory'] == 1)?'Required':'']) !!}

                            @elseif($param['code'] == 'dropdown' || $param['code'] == 'budget' || $param['code'] == 'category')
                                {!! Form::oneSelect('parameter_'.$param['id'], $param['name'], $param['options'], isset($topicParameters[$param['id']])? $topicParameters[$param['id']]->pivot->value : null, isset($topicParameters[$param['id']])? $param['id']['options'][$topicParameters[$param['id']]->pivot->value] : null, ['class' => 'form-control',($param['mandatory'] == 1)?'Required':''] ) !!}

                            @elseif($param['code'] == 'radio_buttons')
                                @if(isset($topicParameters[$param['id']]) or ONE::actionType('topic') != 'show')

                                    <div class="form-group">
                                        <label for="parameterRadio_{!! $param['id'] !!}"> {!! $param['name'] !!}</label>

                                        @foreach($param['options'] as $key => $option)
                                            <div class="form-group">
                                                <input type="radio" name="parameter_{!! $param['id'] !!}" value="{!!$key !!}"
                                                       {{($param['mandatory'] == 1)?'Required':''}}
                                                       {{isset($topicParameters[$param['id']])? ($topicParameters[$param['id']]->pivot->value == $key ? 'checked' : '') : ''}}
                                                       @if(ONE::actionType('topic') == 'show') disabled @endif><label> {!! $option !!}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            @elseif($param['code'] == 'check_box')
                                @if(ONE::actionType('topic') != 'show')
                                    <div class="form-group">
                                        <label> {!! $param['name'] !!}</label>
                                        @foreach($param['options'] as $key => $option)
                                            <div class="form-group">
                                                <input type="checkbox" name="parameter_{!! $param['id'] !!}[]" value="{!!$key !!}" {{($param['mandatory'] == 1)?'Required':''}}><label> {!! $option !!}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            @elseif($param['code'] == 'image_map')
                                {!! Form::hidden('marker_pos_x_'.$param['id'], isset($posX) ? $posX: null, ['id' => 'marker_pos_x']) !!}
                                {!! Form::hidden('marker_pos_y_'.$param['id'], isset($posY) ? $posY: null, ['id' => 'marker_pos_y']) !!}
                            @endif
                        @endforeach

                        @if(ONE::actionType('topic') != 'show')
                            <small style="display: block; color: #0097a7; text-align: center">{!! trans('PublicCbs.clickOnMapToSelectArea') !!}</small>
                        @endif
                        <div class="col-xs-offset-2 col-sm-offset-4 col-md-offset-3" id="wrapper" style="margin-top: 10px; text-align: center;height: 380px">
                            <img id="#empaville_map" src="{{ ONE::getEmpavilleImageMap() }}" class="pin" style="width: 265px">
                        </div>
                    </div>
                </div>

                {!! $form->make() !!}
            </div>
        </div>
    </div>


@endsection

<style>
    #wrapper { position: relative; }
    #wrapper img, #wrapper .marker { position: absolute; }
    #wrapper .marker { z-index: 100;top: 65px; width: 40px;}
    #wrapper img { top: 0px; left: 0; }
</style>

@section('scripts')

    @if(ONE::actionType('topic') != 'create')

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

            if(pos_x != undefined && pos_y != undefined){
                if(pos_x.length > 0 && pos_y.length > 0){
                    $('<img id="" src="/images/map_pin.png">').addClass('marker').css({
                        left: pos_x,
                        top: pos_y
                    }).appendTo($wrapper);
                }
            }

            @if(ONE::actionType('topic') != 'show')
                resizeImage();
            @endif

            //showLocationName(pos_y);
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

                //showLocationName((e.pageY-offset.top-65));
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