@extends('private._private.index')

@section('content')
    @php
    $selectedConfigs = array(
        '3gN7rsyzMvZrxBhZPiaFonMBpt9BcWZF' => array(
            'idea' => array(
                'security_public_access',
                'security_anonymous_comments',
                'security_create_topics',
                'topic_options_allow_pictures',
                'topic_options_allow_share',
                'topic_options_allow_follow',
                'topic_comments_allow_comments',
                'topic_comments_normal'
            )
        ),
    );

    $entityKey = Session::get('X-ENTITY-KEY');
    @endphp
    <div class="container-fluid">
        <div class="box box-primary">
            <div class="box-header">
                <h2 class="box-title box-title-idea-msg">{!! trans("privateCbs.createIdea") !!}</h2>
            </div>
            <div class="box-body" style="height: 90%;">
                <form role="form" action="{{action('CbsController@store', ['type' => $type])}}" method="post" name="formCb" id="formCb">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="cb_key" value="{{ isset($cbKey) ? $cbKey : null }}">
                    <input type="hidden" name="parent_cb_id" value="{{ isset($cb) ? $cb->parent_cb_id : 0 }}">
                    <input type="hidden" name="flagNewCb" value="1">
                    <input type="hidden" name="parameterItensIds" value="">
                    <input type="hidden" name="voteItensIds" value="">

                    <div class="row">
                        <div class="col-12">
                            <label for="title">{{trans("privateCbs.title")}}</label>
                            <input type="text" name="title" class="form-control" id="title" required>
                        </div>

                        <div class="col-12">
                            <button type="button" class="btn btn-flat btn-info" data-toggle="collapse" data-target="#configurations" style="margin: 10px 0 10px 0">{{trans('privateCbs.showConfigurations')}}</button><br>
                        </div>
                        <div class="col-12">
                            <div id="configurations" class="collapse" style="max-height: 500px; overflow: auto">
                                <div id="data">
                                    <label for="description">{{trans("privateCbs.description")}}</label>
                                    <textarea  name="description" rows="4" class="form-control"></textarea>
                                    <label for="start_date">{{trans("privateCbs.start_date")}}</label>
                                    <input type="text" name="start_date" class="form-control" value="{{(\Carbon\Carbon::now())->toDateString()}}">
                                    <label for="end_date">{{trans("privateCbs.end_date")}}</label>
                                    <input type="text" name="end_date" class="form-control" value="">
                                </div>
                                <br>
                                <label for="configurations">{{trans("privateCbs.configurations")}}</label>
                                <table class="table table-striped table-hover table-condensed">
                                    @foreach($configurations as $configuration)
                                        @foreach($configuration->configurations as $configurationValue)
                                            <tr>
                                                <td>
                                                    <div class="checkbox">
                                                        <label>
                                                            <input type="checkbox" name="configuration_{{$configurationValue->id}}"  @if(array_key_exists($entityKey , $selectedConfigs) && array_key_exists($type,$selectedConfigs[$entityKey]) && in_array($configurationValue->code, $selectedConfigs[$entityKey][$type])) checked @endif> {{$configurationValue->title}}
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                </table>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-flat empatia pull-right">{{trans("privateCbs.create")}} </button>
                </form>
            </div>
        </div>
    </div>

@endsection

