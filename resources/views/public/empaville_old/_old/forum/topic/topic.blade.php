@extends('public._layouts.index')

@section('content')

    <div class="row">
        <div class="col-md-12">
            <?php $form = ONE::form('topic')
                    ->settings(["model" => isset($topic) ? $topic : null])
                    ->show('PublicTopicController@edit', 'PublicTopicController@delete', ['id' => isset($topic) ? $topic->id : null, 'cbId' => $cbId], 'PublicTopicController@index', ['cbId' => $cbId])
                    ->create('PublicTopicController@store', 'PublicTopicController@index', ['cbId' => $cbId,'id' => isset($topic) ? $topic->id : null])
                    ->edit('PublicTopicController@update', 'PublicTopicController@show', ['id' => isset($topic) ? $topic->id : null, 'cbId' => $cbId])
                    ->open()
            ?>
            {!! Form::oneText('title', trans('forum.title'), isset($topic) ? $topic->title : null, ['class' => 'form-control', 'id' => 'title', 'required']) !!}
            {!! Form::oneTextArea('summary', trans('forum.summary'), isset($topic) ? $topic->contents : null, ['class' => 'form-control', 'id' => 'summary', 'size' => '30x2', 'style' => 'resize: vertical','required']) !!}
            {!! Form::oneTextArea('contents', trans('forum.contents'), isset($post) ? $post->contents : null, ['class' => 'form-control', 'id' => 'contents', 'size' => '30x5', 'style' => 'resize: vertical','required']) !!}
            {!! Form::hidden('cb_id', isset($topic) ? $topic->id : $cbId, ['id' => 'cb_id']) !!}

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
                @endif
            @endforeach
            {!! $form->make() !!}
        </div>
    </div>
@endsection
