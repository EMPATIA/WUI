@extends('public._layouts.index')

@section('content')

    <div class="row">
        <div class="col-md-12">
            <?php $form = ONE::form('topic')
                    ->settings(["model" => isset($topic) ? $topic : null])
                    ->show('PublicTopicController@edit', 'PublicTopicController@delete', ['id' => isset($topic) ? $topic->id : null, 'cbId' => $cbId], 'PublicTopicController@index', ['cbId' => $cbId])
                    ->create('PublicTopicController@store', 'PublicTopicController@index', ['id' => isset($topic) ? $topic->id : null, 'cbId' => $cbId])
                    ->edit('PublicTopicController@update', 'PublicTopicController@show', ['id' => isset($topic) ? $topic->id : null, 'cbId' => $cbId])
                    ->open()

            ?>
            {!! Form::oneText('title', trans('topic.title'), isset($topic) ? $topic->title : null, ['class' => 'form-control', 'id' => 'title']) !!}
            {!! Form::oneTextArea('contents', trans('topic.contents'), isset($post) ? $post->contents : null, ['class' => 'form-control', 'id' => 'contents', 'size' => '30x5', 'style' => 'resize: vertical']) !!}

            {!! $form->make() !!}
        </div>
    </div>
@endsection