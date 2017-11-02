@extends('private._private.index')

@section('content')
    <div class="box-private">
            <form action="{{ action("CbsController@publishTechnicalAnalysisSubmit",["type"=>$type,"cbKey"=>$cbKey]) }}" method="POST">
            <div class="box-header">
                <h3 class="box-title">
                    {{ trans("privatePublishTechnicalAnalysis.title_confirmation") }}
                </h3>
            </div>
            <div class="box-body">
                <div class="container-fluid">
                    <div class="row">
                        @if(!empty(Session::get("publishTechnicalAnalysisErrors",[])))
                            <div class="col-12">
                                <div class="alert alert-danger" role="alert">
                                    <strong>{{ trans("privatePublishTechnicalAnalysis.errors_occurred") }}</strong>
                                    <ul>
                                        @foreach (Session::get("publishTechnicalAnalysisErrors",[]) as $error)
                                            @if($error=="questionNotDefined")
                                                <li>{{ trans("privatePublishTechnicalAnalysis.requested_question_not_defined") }}</li>
                                            @elseif($error=="parameterNotDefined")
                                                <li>{{ trans("privatePublishTechnicalAnalysis.requested_parameter_not_defined") }}</li>
                                            @elseif($error=="passedStatusNotDefined")
                                                <li>{{ trans("privatePublishTechnicalAnalysis.requested_passed_status_not_defined") }}</li>
                                            @elseif($error=="failedStatusNotDefined")
                                                <li>{{ trans("privatePublishTechnicalAnalysis.requested_failed_status_not_defined") }}</li>
                                            @elseif(!empty($error))
                                                <li>{{ $error }}</li>
                                            @endif
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        @endif
                        <div class="col-12 col-md-6">
                            <h5 class="box-title">
                                {{ trans("privatePublishTechnicalAnalysis.technical_analysis_question") }}
                            </h5>
                            {{ $question->question->question }}
                        </div>
                        <div class="col-12 col-md-6">
                            <h5 class="box-title">
                                {{ trans("privatePublishTechnicalAnalysis.topic_parameter") }}
                            </h5>
                            {{ $question->question->question }}
                        </div>
                        <div class="col-12">
                            <hr>
                        </div>
                        <div class="col-12 col-md-4">
                            <h5 class="box-title">
                                {{ trans("privatePublishTechnicalAnalysis.passed_status") }}
                            </h5>
                            {{ $passing->status->name }}
                            <br><br>
                            <h6 class="box-title">
                                {{ trans("privatePublishTechnicalAnalysis.topics_to_pass") }}
                                ({{ count($passing->topics) }})
                            </h6>
                            <div class="list-group" style="max-height:200px;overflow-y:auto;">
                                @forelse($passing->topics as $topic)
                                    <a class="list-group-item list-group-item-action"
                                        href="{{ action("TopicController@show",["type"=>$type,"cbKey"=>$cbKey,"topicKey"=>$topic->topic_key]) }}">
                                        {{ $topic->title }}
                                    </a>
                                @empty
                                    {{ trans("privatePublishTechnicalAnalysis.no_topics_to_pass") }}
                                @endforelse
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <h5 class="box-title">
                                {{ trans("privatePublishTechnicalAnalysis.failed_status") }}<br>
                            </h5>
                            {{ $failing->status->name }}
                            <br><br>
                            <h6 class="box-title">
                                {{ trans("privatePublishTechnicalAnalysis.topics_to_fail") }}
                                ({{ count($failing->topics) }})
                            </h6>
                            <div class="list-group" style="max-height:200px;overflow-y:auto;">
                                @forelse($failing->topics as $topic)
                                    <a class="list-group-item list-group-item-action"
                                       href="{{ action("TopicController@show",["type"=>$type,"cbKey"=>$cbKey,"topicKey"=>$topic->topic_key]) }}">
                                        {{ $topic->title }}
                                    </a>
                                @empty
                                    {{ trans("privatePublishTechnicalAnalysis.no_topics_to_pass") }}
                                @endforelse
                            </div>

                        </div>
                        <div class="col-12 col-md-4">
                            <h5 class="box-title">
                                {{ trans("privatePublishTechnicalAnalysis.topics_without_analysis") }}
                                ({{ count($noAnalysis) }})
                            </h5>
                            <div class="list-group" style="max-height:200px;overflow-y:auto;">
                                @forelse($noAnalysis as $topic)
                                    <a class="list-group-item list-group-item-action"
                                       href="{{ action("TopicController@show",["type"=>$type,"cbKey"=>$cbKey,"topicKey"=>$topic->topic_key]) }}">
                                        {{ $topic->title }}
                                    </a>
                                @empty
                                    {{ trans("privatePublishTechnicalAnalysis.no_topics_without_analysis") }}
                                @endforelse
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <div class="box-footer">
            <input class="btn btn-flat empatia" type="submit" value="{{ trans("privatePublishTechnicalAnalysis.publish") }}">
            <a href="{{ action("CbsController@publishTechnicalAnalysisForm",["type"=>$type,"cbKey"=>$cbKey]) }}" class="btn btn-flat btn-default">
                {{ trans("privatePublishTechnicalAnalysis.cancel") }}
            </a>

            <input type="hidden" name="questionKey" value="{{ $questionKey }}">
            <input type="hidden" name="parameterId" value="{{ $parameterId }}">
            <input type="hidden" name="passedStatusKey" value="{{ $passedStatusKey }}">
            <input type="hidden" name="failedStatusKey" value="{{ $failedStatusKey }}">
            {!! csrf_field() !!}
        </div>
        </form>
    </div>
@endsection

