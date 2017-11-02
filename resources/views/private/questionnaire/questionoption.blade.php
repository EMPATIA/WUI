@extends('private._private.index')
@section('header_styles')
    <link href="{{ asset("css/cropper.min.css") }}" rel='stylesheet' type='text/css'>
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12">


            @php $form = ONE::form('questionoption', trans('privateQuestionOption.details'),'q', 'q')
                    ->settings(["model" => isset($questionoption) ? $questionoption : null, 'id' => isset($questionoption) ? $questionoption->question_option_key : null])
                    ->show('QuestionOptionsController@edit', 'QuestionsController@delete', ['key' => isset($questionoption) ? $questionoption->question_option_key : null], 'QuestionsController@show', isset($questionoption) ? $questionoption->question->question_key : (isset($questionKey) ? $questionKey : null))
                    ->create('QuestionOptionsController@store', 'QuestionsController@show', ['key' => isset($questionoption) ? $questionoption->question->question_key : (isset($questionKey) ? $questionKey : null)])
                    ->edit('QuestionOptionsController@update', 'QuestionOptionsController@show', ['key' => isset($questionoption) ? $questionoption->question_option_key : null])
                    ->open();
            @endphp

            {!! Form::oneText('label', trans('privateQuestionOption.label'), isset($questionoption) ? $questionoption->label : null, ['class' => 'form-control', 'id' => 'label']) !!}
            {!! Form::hidden('question_key', isset($questionoption) ? $questionoption->question->question_key : (isset($questionKey) ? $questionKey : null)) !!}
            {!! Form::hidden('file_id', isset($questionoption) ? $questionoption->file_id : "", ['id' => 'question_file_id']) !!}
            {!! Form::hidden('file_code', isset($questionoption) ? $questionoption->file_code : "", ['id' => 'question_file_code']) !!}

            @if(ONE::actionType('questionoption') == 'show' and strtoupper(preg_replace('/\s+/', '', $question->question_type->name)) != 'DROPDOWN')

                @if(!empty($questionoption->icon))
                    <div class="box-body">
                        <img class="img-fluid" src="{{action('FilesController@download', ['id' => $questionoption->icon->file_id,'code' => $questionoption->icon->file_code,1] )}}"  id="questionOptionImage" style="height: 30px">
                    </div>
                @endif

            @elseif(strtoupper(preg_replace('/\s+/', '', $question->question_type->name)) != 'DROPDOWN')
                @if(isset($icons))
                    <div class="card flat">
                        <div class="card-header">{{trans('privateQuestionOption.icons')}}</div>
                        <div class="card-body">
                            <div class="btn-group" data-toggle="buttons">
                                @foreach($icons as $icon)
                                    <label class="btn btn-secondary {{isset($questionoption->icon->icon_key)? (($icon->icon_key == $questionoption->icon->icon_key)? 'active' :''):''}}" id="" title="">
                                        <input type="radio" name="icon_key" id="icon_key" value="{{$icon->icon_key}}" {{isset($questionoption->icon->icon_key)? (($icon->icon_key == $questionoption->icon->icon_key)? 'checked' :''):''}}>
                                        {{--<img src="http://placehold.it/20x20/35d/fff&text=f"  id="iconImage" style="height:30px">--}}
                                        <img class="img" src="{{action('FilesController@download', ['id' => $icon->file_id,'code' => $icon->file_code,1] )}}"  id="questIconImage" style="height: 30px">
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            @endif
            @if(isset($dependencies) && count($dependencies) > 0)
                <div class="card flat">
                    <div class="card-header">{{trans('privateQuestionOption.dependencies')}}</div>
                    <div class="card-body">
                        <div class="form-group">
                            @foreach($dependencies as $depend)
                                <div class="row">
                                    <input type="checkbox" name="dependencies[]" id="dependecy_id" value="{{$depend->question_key}}" {{isset($questionOptionDependencies[$depend->question_key])? 'checked':''}} @if(ONE::actionType('questionoption') == 'show') disabled @endif >
                                    {{--<img src="http://placehold.it/20x20/35d/fff&text=f"  id="iconImage" style="height:30px">--}}
                                    <label for="{{$depend->question}}">{{$depend->question}}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            {!! ONE::imageCropModal('getCroppedCanvasModal', 'getCroppedCanvasTitle', trans('files.resize')) !!}
            {!! $form->make() !!}

        </div>
    </div>
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

        {!! ONE::imageUploader('bannerUploader', env('UPLOAD_API', ONE::getUrlFile('upload')), 'imageQuestionOptionUploaded', 'select-banner', 'banner-drop-zone', 'banner-list', 'files_banner', 'getCroppedCanvasModal', 0, 0, isset($uploadKey) ? $uploadKey : "") !!}
        bannerUploader.init();
        updateClickListener();

        function myFunction(){
            alert($(this).attr("value"));
        }
    </script>
@endsection

