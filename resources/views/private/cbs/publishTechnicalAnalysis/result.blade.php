@extends('private._private.index')

@section('content')
    <div class="box-private">
        <div class="box-header">
            <h3 class="box-title">
                {{ trans("privatePublishTechnicalAnalysis.result_title") }}
            </h3>
        </div>
        <div class="box-body">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12 col-md-6">
                        <h5 class="box-title">
                            {{ trans("privatePublishTechnicalAnalysis.passing_topics") }}
                        </h5>
                        <br>
                        <h6 class="box-title">
                            {{ trans("privatePublishTechnicalAnalysis.passing_topics_successful") }}
                            {{ count($publishResult->passing->topics??[]) }}
                        </h6>
                        <div class="list-group" style="max-height:200px;overflow-y:auto;">
                            @forelse($publishResult->passing->topics??[] as $topic)
                                <a class="list-group-item list-group-item-action"
                                   href="{{ action("TopicController@show",["type"=>$type,"cbKey"=>$cbKey,"topicKey"=>$topic->topic_key]) }}">
                                    {{ $topic->title }}
                                </a>
                            @empty
                                {{ trans("privatePublishTechnicalAnalysis.no_passing_topics_successful") }}
                            @endforelse
                        </div>
                        <br>
                        <h6 class="box-title">
                            {{ trans("privatePublishTechnicalAnalysis.passing_topics_failed") }}
                            {{ count($publishResult->passing->failure??[]) }}
                        </h6>
                        <div class="list-group" style="max-height:200px;overflow-y:auto;">
                            @forelse($publishResult->passing->failure??[] as $topic)
                                <a class="list-group-item list-group-item-action"
                                   href="{{ action("TopicController@show",["type"=>$type,"cbKey"=>$cbKey,"topicKey"=>$topic->topic_key]) }}">
                                    {{ $topic->title }}
                                </a>
                            @empty
                                {{ trans("privatePublishTechnicalAnalysis.no_passing_topics_failed") }}
                            @endforelse
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <h5 class="box-title">
                            {{ trans("privatePublishTechnicalAnalysis.failing_topics") }}
                        </h5>
                        <br>
                        <h6 class="box-title">
                            {{ trans("privatePublishTechnicalAnalysis.failing_topics_successful") }}
                            {{ count($publishResult->failing->topics??[]) }}
                        </h6>
                        <div class="list-group" style="max-height:200px;overflow-y:auto;">
                            @forelse($publishResult->failing->topics??[] as $topic)
                                <a class="list-group-item list-group-item-action"
                                   href="{{ action("TopicController@show",["type"=>$type,"cbKey"=>$cbKey,"topicKey"=>$topic->topic_key]) }}">
                                    {{ $topic->title }}
                                </a>
                            @empty
                                {{ trans("privatePublishTechnicalAnalysis.no_failing_topics_successful") }}
                            @endforelse
                        </div>
                        <br>
                        <h6 class="box-title">
                            {{ trans("privatePublishTechnicalAnalysis.failing_topics_failed") }}
                            {{ count($publishResult->failing->failure??[]) }}
                        </h6>
                        <div class="list-group" style="max-height:200px;overflow-y:auto;">
                            @forelse($publishResult->failing->failure??[] as $topic)
                                <a class="list-group-item list-group-item-action"
                                   href="{{ action("TopicController@show",["type"=>$type,"cbKey"=>$cbKey,"topicKey"=>$topic->topic_key]) }}">
                                    {{ $topic->title }}
                                </a>
                            @empty
                                {{ trans("privatePublishTechnicalAnalysis.no_failing_topics_failed") }}
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

