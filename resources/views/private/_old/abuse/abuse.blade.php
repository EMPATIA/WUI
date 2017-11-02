@extends('private._private.index')

@section('content')

    <div class="row">
        <div class="col-md-12">
            <?php $form = ONE::form('abuse')
                    ->settings(["model" => isset($abuse) ? $abuse : null])
                    ->show('AbuseController@edit', 'AbuseController@destroy', ['id' => isset($abuse) ? $abuse->id : null], 'TopicController@show', ['id' => isset($topic) ? $topic->cb_id : null, 'topic_id' => isset($post) ? $post->topic_id : null])
                    ->create('AbuseController@store', 'AbuseController@index', ['id' => isset($abuse) ? $abuse->id : null, 'post_id' => isset($post) ? $post->id : null])
                    ->edit('AbuseController@update', 'AbuseController@show', ['id' => isset($abuse) ? $abuse->id : null])
                    ->open();
            ?>

            @if(ONE::actionType('abuse') == "show")
                {!! Form::button(trans('privateAbuse.decline'), ['class' => 'btn btn-flat btn btn-danger btn-sm fa fa-remove pull-right', 'onclick' => "location.href='".action('AbuseController@declinePostAbuses', ['postId' => isset($abuse) ? $abuse->post_id : null])]) !!}
                {!! Form::button(trans('privateAbuse.accept'), ['class' => 'btn btn-flat btn btn-success btn-sm fa fa-check pull-right', 'onclick' => "location.href='".action('AbuseController@acceptPostAbuses', ['postId' => isset($abuse) ? $abuse->post_id : null])]) !!}
            @endif

            {!! Form::oneText('type', trans('privateAbuse.type'), isset($abuse) ? $abuse->comment : null, ['class' => 'form-control', 'id' => 'type']) !!}
            {!! Form::oneText('created_by', trans('privateAbuse.abuse_created_by'), isset($usersNames) ? $usersNames[$abuse->created_by] : null, ['class' => 'form-control', 'id' => 'created_by']) !!}
            {!! Form::oneText('post_by', trans('privateAbuse.post_by'), isset($usersNames) ? $usersNames[$post->created_by] : null, ['class' => 'form-control', 'id' => 'post_by']) !!}
            {!! Form::oneText('post', trans('privateAbuse.post'), isset($post) ? $post->contents : null, ['class' => 'form-control', 'contents' => 'post', 'size' => '30x5']) !!}

            {!! $form->make() !!}
        </div>
    </div>
@endsection