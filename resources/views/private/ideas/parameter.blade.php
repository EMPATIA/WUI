@extends('private._private.index')

@section('header_styles')
    <link href="{{ asset("css/cropper.min.css") }}" rel='stylesheet' type='text/css'>
@endsection

@section('content')

    <div class="row">
        <div class="col-md-9">

            @php $form = ONE::form('parameters')
                    ->settings(["model" => isset($parameter) ? $parameter : null,'id'=>isset($parameter) ? $parameter->id : null ])
                    ->show('IdeaParametersController@edit', 'IdeaParametersController@delete', ['cbId' => $cbId,'paramId' => isset($parameter) ? $parameter->id : null], 'IdeasController@show', ['id' => isset($cbId) ? $cbId : null])
                    ->create('IdeaParametersController@store', 'IdeasController@show', ['cbId' => $cbId,'id' => isset($parameter) ? $parameter->id : null])
                    ->edit('IdeaParametersController@update', 'IdeaParametersController@show', ['cbId' => $cbId,'id' => isset($parameter) ? $parameter->id : null])
                    ->open();
            @endphp

            @if(ONE::actionType('parameters') == 'show' || ONE::actionType('parameters') == 'edit')
                {!! Form::hidden('cbId', isset($cbId) ? $cbId : 0, ['id' => 'cbId']) !!}
                {!! Form::hidden('paramSelect', isset($paramId) ? $paramId : 0, ['id' => 'paramSelect']) !!}
                {!! Form::oneText('paramName', trans('form.paramName'), isset($parameter) ? $parameter->parameter : null, ['class' => 'form-control', 'id' => 'paramName', 'required' => 'required','readonly'=>'readonly']) !!}
                {!! Form::oneText('description', trans('form.description'), isset($parameter) ? $parameter->description : null, ['class' => 'form-control', 'id' => 'description','readonly'=>'readonly']) !!}
                {!! $html !!}


                <div id="editImage">
                    <p>{!! ONE::fileUploadBox("banner-drop-zone", trans('files.drop-zone'), trans('files.banners'), 'select-banner', 'banner-list', 'files_banner') !!}</p>
                </div>
            @elseif(ONE::actionType('parameters') == 'create')

                <div class="box-body ">
                    <div class="btn-group-vertical">
                        <select class="form-control" id="paramSelect" name="paramSelect" onchange="getParamOptions()" required>
                            <option value="">Select one parameter</option>
                            @foreach($parameters as $param)
                                <option value="{{$param->id}}">{{$param->parameter}}</option>
                            @endforeach
                        </select>
                        <!-- Multi-select parameter options -->
                        <div class="btn-group" id="parameterOptions">

                        </div>
                    </div>

                    <div class="uploadImage" id="uploadImage">
                        <p>{!! ONE::fileUploadBox("banner-drop-zone", trans('files.drop-zone'), trans('files.banners'), 'select-banner', 'banner-list', 'files_banner') !!}</p>
                    </div>
                </div>

            @endif
            {!! ONE::imageCropModal('getCroppedCanvasModal', 'getCroppedCanvasTitle', trans('files.resize')) !!}

            {!! $form->make() !!}
        </div>

    </div>
@endsection
@section('scripts')

    {{--<script>--}}
    {{--{!! ONE::addTinyMCE("#content_en", "en_GB", action('ContentManagerController@getTinyMCE')) !!}--}}
    {{--{!! ONE::addTinyMCE("#content_pt", "pt_PT", action('ContentManagerController@getTinyMCE')) !!}--}}
    {{--</script>--}}
    <!-- Plupload Javascript fix and bootstrap fix @ start -->
    <link rel="stylesheet" href="/bootstrap/plupload-fix/bootstrap.css">
    <script src="/bootstrap/plupload-fix/bootstrap.min.js"></script>
    <!-- Plupload Javascript fix and bootstrap fix @ End -->
    <script src="{{ asset('vendor/jildertmiedema/laravel-plupload/js/plupload.full.min.js') }}"></script>
    <script src="{{ asset("js/cropper.min.js") }}"></script>
    @include('private._private.functions') {{-- Helper Functions --}}
    <script>

        {!! ONE::imageUploader('bannerUploader', env('UPLOAD_API', 'https://empatia-test.onesource.pt:5005/file/upload/'), 'imageMapUploaded', 'select-banner', 'banner-drop-zone', 'banner-list', 'files_banner', 'getCroppedCanvasModal', 0, 0, isset($uploadKey) ? $uploadKey : "") !!}
        bannerUploader.init();

        updateClickListener();


    </script>
    <script>
        $( document ).ready(function(){
            var val = $('#imageMap').length;

            if(val > 0){

                $("#editImage").css('visibility', 'visible');
            }
            else {
                $("#editImage").css('visibility', 'hidden');
            }


            var up = $("#uploadImage").length;
            if(up > 0) {
                $("#uploadImage").css('visibility', 'hidden');
            }
        });

        function getParamOptions() {

            var idParam = $('#paramSelect').val();

            if (idParam == "") {
                $('#parameterOptions').html("");
                $("#uploadImage").css('visibility', 'hidden');
                return;
            }

            $.ajax({
                method: 'POST', // Type of response and matches what we said in the route
                url: '{{action('IdeaParametersController@getParameterOptions')}}', // This is the url we gave in the route
                data: {postId: idParam, _token: "{{ csrf_token() }}"}, // a JSON object to send back
                success: function (response) { // What to do if we succeed
                    console.log(response);
                    $("#parameterOptions").html(response);
                    var $result = $(response).filter('#imageMap');
                    if($result.length >0){
                        $("#uploadImage").css('visibility', 'visible');
                    }
                    else{
                        $("#uploadImage").css('visibility', 'hidden');
                    }

                },
                error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                }

            });
        }

        function addOption(){
            var scntDiv = $('#newOption');
            $('<p><label class="btn-group"><input type="text" id="newOptions" size="20" name="newOptions[]" value="" placeholder="Option Value" required /></label></p>').appendTo(scntDiv);
            $("#optionSelect").removeAttr('required');
        }
        $('#remScnt').on('click', function() {
            if( i > 2 ) {
                $(this).parents('p').remove();
                i--;
            }
            return false;
        });

    </script>

@endsection

