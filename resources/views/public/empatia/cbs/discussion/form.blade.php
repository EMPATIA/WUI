@extends('public.empatia._layouts.index')

@section('header_styles')
    <link rel="stylesheet" href="{{ asset('css/empatia/cbs.css')}}">
@endsection

@section('header_scripts')
    <!-- Maps -->
    <script   src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBJtyhsJJX_5DCp59m8sNsPlhHp8aQZHIE&libraries=places" type="text/javascript"></script>
@endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-solid">

                <div class="row menus-row">
                    <div class="menus-line col-sm-6 col-sm-offset-3"><i class="fa fa-commenting"></i> 
                    @if (strpos(array_get(Route::getCurrentRoute()->getAction(), 'as', ''), 'create') !== false)                        
                        {!! trans('PublicCbs.createTopic') !!}
                    @elseif(strpos(array_get(Route::getCurrentRoute()->getAction(), 'as', ''), 'edit') !== false)
                        {!! trans('PublicCbs.editTopic') !!}
                    @endif                    
                    </div>          
                </div>                  
                
                <br/>
                
                <div class="box-body" style="padding:20px;">
                    
                    <?php 
                    $form = ONE::form('topic')
                            ->settings(["model" => isset($topic) ? $topic : null])
                            ->show('PublicTopicController@edit', 'PublicTopicController@delete', ['cbKey' => $cbKey, 'id' => isset($topic) ? $topic->topic_key : null, 'type' => $type], 'PublicTopicController@index', ['cbKey' => $cbKey, 'type' => $type])
                            ->create('PublicTopicController@store', 'PublicCbsController@show', ['cbKey' => $cbKey, 'type' => $type])
                            ->edit('PublicTopicController@update', 'PublicTopicController@show', ['cbKey' => $cbKey, 'id' => isset($topic) ? $topic->topic_key : null, 'type' => $type])
                            ->open()
                    ?>

                    <!-- Form details -->
                    {!! Form::oneText('title', trans('PublicCbs.title'), isset($topic) ? $topic->title : null, ['class' => 'form-control', 'id' => 'title', 'required']) !!}
                    {!! Form::oneTextArea('summary', trans('PublicCbs.summary'), isset($topic) ? $topic->contents : null, ['class' => 'form-control', 'id' => 'summary', 'size' => '30x6', 'style' => 'resize: vertical','required']) !!}
                    {!! Form::oneTextArea('contents', trans('PublicCbs.description'), isset($post) ? $post->contents : null, ['class' => 'form-control', 'id' => 'contents', 'size' => '30x14', 'style' => 'resize: vertical','required']) !!}
                    
                    <!-- Hidden fields -->
                    {!! Form::hidden('cb_key', isset($topic) ? $topic->id : $cbKey, ['id' => 'cb_key']) !!}
                    {!! Form::hidden('type', isset($type) ? $type : '', ['id' => 'type']) !!}                    
                    
                    <div class="form-group">
                        <div class="row">
                            @foreach($parameters as $param)
                                @if($param["code"] == "dropdown" || $param['code'] == 'budget' || $param['code'] == 'category')
                                    <div class="col-sm-12">
                                        {!! Form::oneSelect('parameter_'.$param['id'], $param['name'], $param['options'], isset($topicParameters[$param['id']])? $topicParameters[$param['id']]->pivot->value : null, isset($topicParameters[$param['id']])? $param['id']['options'][$topicParameters[$param['id']]->pivot->value] : null, ['class' => 'form-control',($param['mandatory'] == 1)?'Required':''] ) !!}

                                    </div>
                                @elseif($param["code"] == "text")
                                    <div class="col-sm-12">
                                        {!! Form::oneText('parameter_'.$param['id'], $param['name'], isset($topicParameters[$param['id']])? $topicParameters[$param['id']]->pivot->value : null, ['class' => 'form-control',($param['mandatory'] == 1)?'Required':'']) !!}

                                    </div>
                                @elseif($param["code"] == "text_area")
                                    <div class="col-sm-12">
                                        {!! Form::oneTextArea('parameter_'.$param['id'], $param['name'], isset($topicParameters[$param['id']])? $topicParameters[$param['id']]->pivot->value : null, ['class' => 'form-control', 'size' => '30x2', 'style' => 'resize: vertical',($param['mandatory'] == 1)?'Required':'']) !!}
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    
                    @if(strpos(array_get(Route::getCurrentRoute()->getAction(), 'as', ''), 'edit') !== false)
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="title">{{ trans("cb.add_files") }}</label>
                            {!! ONE::fileSimpleUploadBox("drop-zone", trans("cb.drag_and_drop_files_to_here") , trans('cbs.files'), 'select-files', 'files-list', 'files') !!}
                        </div>
                    </div>      
                    @endif 
                    
                    @if((count($parameters) > 0))
                        @foreach($parameters as $parameter)
                            @if($parameter["code"] == 'google_maps')
                                {!! Form::oneMaps('parameter_'.(($parameter["mandatory"]==1) ? "required_" : "").$parameter['id'],"Maps",isset($parameter['value'])? $parameter['value'] : null,["required" => $parameter["mandatory"], "enableSearch" => true]) !!}
                            @endif
                        @endforeach
                    @endif     

                {!! $form->make() !!}
            </div>
        </div>
    </div>
</div>        
@endsection


@section('scripts')
    @if(strpos(array_get(Route::getCurrentRoute()->getAction(), 'as', ''), 'edit') !== false)
        <script src="{{ asset('vendor/jildertmiedema/laravel-plupload/js/plupload.full.min.js') }}"></script>
        <script src="{{ asset("js/cropper.min.js") }}"></script>

        @include('private._private.functions') {{-- Helper Functions --}}
        <script>
            {!! ONE::fileUploader('fileUploader', action('FilesController@upload'), 'ideaFileUploaded', 'select-files', 'drop-zone', 'files-list', 'files', 1, isset($uploadKey) ? $uploadKey : "", $allowFiles) !!}
            fileUploader.init();

            updateClickListener();

            updateFilesPostList('#files',1);
        </script>
    @endif

@endsection    
