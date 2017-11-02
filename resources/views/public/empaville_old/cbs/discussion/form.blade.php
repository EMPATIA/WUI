@extends('public.empaville._layouts.index')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box box-solid"
                 style="box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);">

                <div class="box-header " style="color: #ffffff; background-color: #8DC640;">
                    <h3 style="padding-top: 5px;display: inline-block;margin: 0;line-height: 1;">{!! trans('PublicCbs.discussions') !!}</h3>
                </div>

                <div class="box-body">
                    @if (strpos(array_get(Route::getCurrentRoute()->getAction(), 'as', ''), 'create') !== false)
                        {{ Form::open(['action' => ['PublicTopicController@store', 'cbId' => isset($cbId) ? $cbId : null, 'id' => isset($topic) ? $topic->id : null, 'type' => isset($type) ? $type : null]]) }}
                    @elseif(strpos(array_get(Route::getCurrentRoute()->getAction(), 'as', ''), 'edit') !== false)
                        {{ Form::model(isset($topic) ? $topic : null, ['action' => ['PublicTopicController@update', 'cbId' => isset($cbId) ? $cbId : null, 'id' => isset($topic) ? $topic->id : null, 'type' => isset($type) ? $type : null], 'method' => 'PUT']) }}
                    @endif

                    <div class="row form-element" style="padding-top: 5px; padding-bottom: 5px; min-height: 25px;">
                        <div class="col-sm-2">
                            {{ Form::label('title', trans('PublicCbs.title'), ["style" => "display: inline-block; color: #888888; min-height: 25px; vertical-align: middle"]) }}
                        </div>
                        <div class="col-sm-10">
                            {{ Form::text('title', isset($topic) ? $topic->title : null, ["style" => "width: 100%; border: 1px solid #bbbbbb; border-radius: 5px; min-height: 25px;"])}}
                        </div>
                    </div>

                    <div class="row form-element" style="padding-top: 5px; padding-bottom: 5px;">
                        <div class="col-sm-2">
                            {{ Form::label('summary', trans('PublicCbs.summary'), ["style" => "display: inline-block; color: #888888; min-height: 25px; vertical-align: middle"]) }}
                        </div>
                        <div class="col-sm-10">
                            {{ Form::textarea('summary', isset($topic) ? $topic->contents : null, ["size" => "30x1", "style" => "resize: vertical; width: 100%; border: 1px solid #bbbbbb; border-radius: 5px; min-height: 25px;"])}}
                        </div>
                    </div>

                    <div class="row form-element" style="padding-top: 5px; padding-bottom: 5px;">
                        <div class="col-sm-2">
                            {{ Form::label('contents', trans('PublicCbs.description'), ["style" => "display: inline-block; color: #888888; min-height: 25px; vertical-align: middle"]) }}
                        </div>
                        <div class="col-sm-10">
                            {{ Form::textarea('contents', isset($post) ? $post->contents : null, ["size" => "30x2", "style" => "resize: vertical; width: 100%; border: 1px solid #bbbbbb; border-radius: 5px; min-height: 25px;"]) }}
                        </div>
                    </div>

                    <div class="form-group" style="padding-top: 5px; padding-bottom: 5px;">
                        <div class="row">
                            @foreach($parameters as $parameter)
                                @if($parameter["code"] == "dropdown" || $parameter['code'] == 'budget' || $parameter['code'] == 'category')
                                    <div class="col-sm-6"
                                         style="padding-top: 5px; padding-bottom: 5px; min-height: 25px;">
                                        {{ Form::label('parameter_'.$parameter['id'], $parameter['name'], ["style" => "display: inline-block; color: #888888; vertical-align: middle; padding-right: 5px;"]) }}
                                        {{ Form::select('parameter_'.$parameter['id'], $parameter['options'], ["style" => "border: 1px solid #bbbbbb; border-radius: 15px; min-height: 25px;"]) }}
                                    </div>
                                @elseif($parameter["code"] == "text")
                                    <div class="col-sm-12"
                                         style="padding-top: 5px; padding-bottom: 5px; min-height: 25px;">
                                        <div class="row">
                                            <div class="col-sm-2">
                                                {{ Form::label('parameter_'.$parameter['id'], $parameter['name'], ["style" => "display: inline-block; color: #888888; vertical-align: middle; padding-right: 5px;"]) }}
                                            </div>
                                            <div class="col-sm-10">
                                                {{ Form::text('parameter_'.$parameter['id'], isset($parameter['value'])? $parameter['value'] : null, ["style" => "width: 100%; border: 1px solid #bbbbbb; border-radius: 5px; min-height: 25px;"]) }}
                                            </div>
                                        </div>
                                    </div>
                                @elseif($parameter["code"] == "text_area")
                                    <div class="col-sm-12"
                                         style="padding-top: 5px; padding-bottom: 5px; min-height: 25px;">
                                        <div class="row">
                                            <div class="col-sm-2">
                                                {{ Form::label('parameter_'.$parameter['id'], $parameter['name'], ["style" => "display: inline-block; color: #888888; vertical-align: middle; padding-right: 5px;"]) }}
                                            </div>
                                            <div class="col-sm-10">
                                                {{ Form::textarea('parameter_'.$parameter['id'], isset($parameter['value'])? $parameter['value'] : null, ["size" => "30x1", "style" => "resize: vertical; width: 100%; border: 1px solid #bbbbbb; border-radius: 5px; min-height: 25px;"]) }}
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>

                </div>

                <div class="box-footer">
                    {{ Form::submit(trans('PublicCbs.submit'), ["class" => 'pull-right btn', "style" => "font-weight: bold; color: #ffffff; background-color: #8DC640;"]) }}
                </div>

                {{ Form::close() }}
            </div>
        </div>
    </div>
@endsection