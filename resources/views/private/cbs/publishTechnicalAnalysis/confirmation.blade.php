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
                        </div>
                        <div class="col-12 col-md-6">
                            <h5 class="box-title">
                                {{ trans("privatePublishTechnicalAnalysis.topic_parameter") }}
                            </h5>
                        </div>
                        <div class="col-12">
                            <div class="row" id="questions-to-use">
                                @forelse($questions as $questionIndex => $question)
                                    <div class="col-12 col-md-6">
                                        {{ $question->question->question }}
                                    </div>
                                    <div class="col-12 col-md-6">
                                        {{ $parameters->{ $questionIndex }->parameter->parameter }}
                                    </div>
                                @empty
                                    <div class="col-12 col-md-6">
                                        {{ trans("privatePublishTechnicalAnalysis.no_new_status_for_passed_topics")}}
                                    </div>
                                    <div class="col-12 col-md-6">
                                        {{ trans("privatePublishTechnicalAnalysis.no_new_status_for_passed_topics")}}~
                                    </div>
                                @endforelse
                            </div>
                        </div>
                        <div class="col-12">
                            <hr>
                        </div>
                        <div class="col-12 col-md-6">
                            <h5 class="box-title">
                                {{ trans("privatePublishTechnicalAnalysis.passed_status") }}
                            </h5>
                            @if(!empty($passing->status->name))
                                {{ $passing->status->name }}
                            @else
                                {{ trans("privatePublishTechnicalAnalysis.no_new_status_for_passed_topics")}}
                            @endif
                            <br><br>
                            <div class="card">
                                <h6 class="card-header box-title" data-toggle="collapse" href="#passing-topics" aria-expanded="false" aria-controls="passing-topics">
                                    {{ trans("privatePublishTechnicalAnalysis.topics_to_pass") }}
                                    ({{ count($passing->topics) }})
                                </h6>
                                <div id="passing-topics" class="card-block collapse">
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
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <h5 class="box-title">
                                {{ trans("privatePublishTechnicalAnalysis.failed_status") }}<br>
                            </h5>
                            @if(!empty($failing->status->name))
                                {{ $failing->status->name }}
                            @else
                                {{ trans("privatePublishTechnicalAnalysis.no_new_status_for_failing_topics")}}
                            @endif
                            <br><br>
                            <div class="card">
                                <h6 class="card-header box-title" data-toggle="collapse" href="#failing-topics" aria-expanded="false" aria-controls="failing-topics">
                                    {{ trans("privatePublishTechnicalAnalysis.topics_to_fail") }}
                                    ({{ count($failing->topics) }})
                                </h6>
                                <div id="failing-topics" class="card-block collapse">
                                    <div class="list-group" style="max-height:200px;overflow-y:auto;">
                                        @forelse($failing->topics as $topic)
                                            <a class="list-group-item list-group-item-action"
                                                href="{{ action("TopicController@show",["type"=>$type,"cbKey"=>$cbKey,"topicKey"=>$topic->topic_key]) }}">
                                                {{ $topic->title }}
                                            </a>
                                        @empty
                                            {{ trans("privatePublishTechnicalAnalysis.no_topics_to_failing") }}
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <hr>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="card">
                                <h6 class="card-header box-title" data-toggle="collapse" href="#without-decision-topics" aria-expanded="false" aria-controls="without-decision-topics">
                                    {{ trans("privatePublishTechnicalAnalysis.topics_without_decision") }}
                                    ({{ count($noDecision) }})
                                </h6>
                                <div id="without-decision-topics" class="card-block collapse">
                                    <div class="list-group" style="max-height:200px;overflow-y:auto;">
                                        @forelse($noDecision as $topic)
                                            <a class="list-group-item list-group-item-action"
                                                href="{{ action("TopicController@show",["type"=>$type,"cbKey"=>$cbKey,"topicKey"=>$topic->topic_key]) }}">
                                                {{ $topic->title }}
                                            </a>
                                        @empty
                                            {{ trans("privatePublishTechnicalAnalysis.no_topics_without_decision") }}
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="card">
                                <h6 class="card-header box-title" data-toggle="collapse" href="#no-analysis-topics" aria-expanded="false" aria-controls="no-analysis-topics">
                                    {{ trans("privatePublishTechnicalAnalysis.topics_without_analysis") }}
                                    ({{ count($noAnalysis) }})
                                </h6>
                                <div id="no-analysis-topics" class="card-block collapse">
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
                </div>
            </div>

            <div class="box-footer">
            <input class="btn btn-flat empatia" type="submit" value="{{ trans("privatePublishTechnicalAnalysis.publish") }}">
            <a href="{{ action("CbsController@publishTechnicalAnalysisForm",["type"=>$type,"cbKey"=>$cbKey]) }}" class="btn btn-flat btn-default">
                {{ trans("privatePublishTechnicalAnalysis.cancel") }}
            </a>


            @forelse($questions as $questionIndex => $question)
                <input type="hidden" name="questionKeys[{{ $questionIndex }}]" value="{{ $question->question->tech_analysis_question_key }}">
            @empty
            @endforelse

            @forelse($parameters as $parameterIndex => $parameter)
                <input type="hidden" name="parameterIds[{{ $parameterIndex }}]" value="{{ $parameter->parameter->id }}">
            @empty
            @endforelse

            <input type="hidden" name="passedStatusKey" value="{{ $passedStatusKey }}">
            <input type="hidden" name="failedStatusKey" value="{{ $failedStatusKey }}">
            {!! csrf_field() !!}
        </div>
        </form>
    </div>
@endsection

