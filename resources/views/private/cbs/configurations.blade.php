@extends('private._private.index')

@section('content')
    @include('private.cbs.tabs')

    <div class="card flat topic-data-header" >
        <p><label for="contentStatusComment" style="margin-left:5px; margin-top:5px;">{{trans('privateCbs.pad')}}</label>  {{$cb->title}}<br></p>
        <p><label for="contentStatusComment" style="margin-left:5px;">{{trans('privateCbs.author')}}</label>
            <a href="{{action('UsersController@show', ['userKey' => $author->user_key, 'role' => $author->role ?? null])}}">
                {{$author->name}}
            </a>
            <br>
        </p>
        <p><label for="contentStatusComment" style="margin-left:5px; margin-bottom:5px;">{{trans('privateCbs.start_date')}}</label>  {{$cb->start_date}}</p>
    </div>


    <div class="margin-top-20">
        @php
        $form = ONE::form('cbsConfigurations', trans('privateTopic.details'), 'cb', 'configurations')
            ->settings(["model" => isset($topic) ? $topic : null, 'id'=>isset($topic) ? $topic->topic_key : null])
            ->show('CbsController@editConfigurations', null,['type' => isset($type) ? $type : null, 'cbKey' =>isset($cb) ? $cb->cb_key : null], null)
            ->create('TopicController@store', 'CbsController@show' , ['type'=> $type, 'cbKey' => isset($cb) ? $cb->cb_key : null])
            ->edit('CbsController@update', 'CbsController@showConfigurations', ['type' => $type,'cbKey' =>isset($cb) ? $cb->cb_key : null, 'configurations_flag' => 1])
            ->open();
        @endphp

            {!! Form::hidden('title', isset($cb) ? $cb->title : null) !!}
            {!! Form::hidden('description', isset($cb) ? $cb->contents : null) !!}
            {!! Form::hidden('tag', isset($cb) ? $cb->tag : null) !!}
            {!! Form::hidden('start_date', isset($cb) ? $cb->start_date : date('Y-m-d')) !!}
            {!! Form::hidden('end_date', isset($cb) && $cb->end_date!=null ? $cb->end_date  : '') !!}
            {!! Form::hidden('cb_key', isset($cb) ? $cb->cb_key : 0, ['id' => 'cb_key']) !!}
            {!! Form::hidden('parent_cb_id', isset($cb) ? $cb->parent_cb_id : 0, ['id' => 'parent_cb_id']) !!}

        @if(Session::get('user_role') == 'admin' || ONE::verifyUserPermissionsShow('cb', 'configurations'))
            <!-- CB Configurations -->
            <div class="card flat">
                <div class="card-title" style="padding:10px">
                    {{trans('privateCbs.configurations')}}
                </div>
                <div class="card-body">
                    @foreach($configurations as $configuration)
                        <div class="col-12" style="padding-left: 0px;">
                            <div class="card flat">
                                <div class="card-header">
                                    <a class="collapsed block accordion-header" role="button" data-toggle="collapse"
                                       href="#collapse_{{$configuration->id}}" aria-expanded="false" aria-controls="collapse_{{$configuration->id}}">
                                        {{$configuration->title}}
                                    </a>
                                </div>
                                <div id="collapse_{{$configuration->id}}" class="panel-collapse collapse show" role="tabpanel">
                                    <div class="card-body">
                                    @foreach($configuration->configurations as $option)
                                            {!! Form::oneSwitch('configs['.$configuration->code.'][]',$option->title, in_array($option->id, (isset($cbConfigurations[$option->code]) ? array_keys($cbConfigurations[$option->code]) : []) ) , array("groupClass"=>"row", "labelClass" => "col-12", "switchClass" => "col-12", "value" => $option->id, "id" => "configuration_".$option->id ) ) !!}
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {!! $form->make() !!}

    </div>
@endsection
