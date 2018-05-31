@extends('private._private.index')

@section('content')
    @php
        $form = ONE::form('publishTechnicalAnalysis', trans('privatePublishTechnicalAnalysis.title'))
            ->settings(["options" => ['publishTechnicalAnalysis' =>  $type, ONE::actionType('publishTechnicalAnalysis')]])
            ->create('CbsController@publishTechnicalAnalysisConfirmation', 'CbsController@show', ['type' => $type,'topicKey' => $cbKey])
            ->open();
    @endphp
    <div class="hidden" id="selects-container" data-number="0">
        <div class="col-12 col-md-6" style="padding-top:10px;">
            <select name="description-question" class="form-control">
                <option value="">{{ trans("privatePublishTechnicalAnalysis.no_question_migration")}}</option>
                @php $printedTechnicalAnalysisQuestion = false; @endphp
                @forelse($technicalAnalysisQuestions as $technicalAnalysisQuestion)
                    @if(!$technicalAnalysisQuestion->acceptable)
                        @php $printedTechnicalAnalysisQuestion = true; @endphp
                        <option value="{{ $technicalAnalysisQuestion->tech_analysis_question_key }}">
                            {{ $technicalAnalysisQuestion->question }}
                        </option>
                    @endif
                @empty
                @endforelse

                @if(!$printedTechnicalAnalysisQuestion)
                    <option value="" disabled>
                        {{ trans("privatePublishTechnicalAnalysis.no_questions_available") }}
                    </option>
                @endif
            </select>
        </div>
        <div class="col-12 col-md-6" style="padding-top:10px;">
            @php $printedCbParameters = false; @endphp
            <select name="description-parameter" class="form-control">
                <option value="">{{ trans("privatePublishTechnicalAnalysis.no_question_migration")}}</option>
                @forelse($cbParameters as $cbParameter)
                    @if($cbParameter->code=="text_area" || $cbParameter->code=="text")
                        @php $printedCbParameters = true; @endphp
                        <option value="{{ $cbParameter->id }}"
                            @if(old("description-parameter")==$cbParameter->id) selected @endif>
                            {{ $cbParameter->parameter }}
                        </option>
                    @endif
                @empty
                @endforelse

                @if(!$printedCbParameters && true)
                    <option value="" disabled>
                        {{ trans("privatePublishTechnicalAnalysis.no_parameters_available") }}
                    </option>
                @endif
            </select>
        </div>
    </div>

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
            <div class="col-12">
                <a href="#" class="btn-seemore pull-right" onclick="javascript:createNewDescriptionQuestion()">
                    <i class="fa fa-plus" aria-hidden="true"></i>
                </a>
            </div>
            <div class="col-12 col-md-6">
                <h5 class="box-title">{{ trans("privatePublishTechnicalAnalysis.question_to_use") }}</h5>
            </div>
            <div class="col-12 col-md-6">
                <h5 class="box-title">{{ trans("privatePublishTechnicalAnalysis.parameter_to_use") }}</h5>
            </div>
            <div class="col-12">
                <div class="row" id="questions-to-use">
                </div>
            </div>
            <div class="col-12">
                <hr>
            </div>
            <div class="col-12">
                <h5 class="box-title">{{ trans("privatePublishTechnicalAnalysis.new_status_to_set") }}</h5>
            </div>
            <div class="col-12 col-md-6">
                <h6 class="box-title">
                    {{ trans("privatePublishTechnicalAnalysis.new_status_for_passed_topics") }}
                </h6>
                <select name="status-passed" id="status-passed" class="form-control">
                    <option value="">
                        {{ trans("privatePublishTechnicalAnalysis.no_new_status_for_passed_topics")}}
                    </option>
                    @forelse($cbStatusTypes as $cbStatusType)
                        @if($loop->first && !empty(old("status-passed")))
                            <option selected>
                                {{ trans("privatePublishTechnicalAnalysis.select_option") }}
                            </option>
                        @endif
                        <option value="{{ $cbStatusType->status_type_key }}"
                            @if(old("status-passed")==$cbStatusType->status_type_key) selected @endif>
                            {{ $cbStatusType->name }}
                        </option>
                    @empty
                        <option disabled selected>
                            {{ trans("privatePublishTechnicalAnalysis.no_status_types_available") }}
                        </option>
                    @endforelse
                </select>
            </div>
            <div class="col-12 col-md-6">
                <h6 class="box-title">
                    {{ trans("privatePublishTechnicalAnalysis.new_status_for_failed_topics") }}
                </h6>
                <select name="status-failed" id="status-failed" class="form-control">
                <option value="">{{ trans("privatePublishTechnicalAnalysis.no_new_status_for_failed_topics")}}</option>
                    @forelse($cbStatusTypes as $cbStatusType)
                        @if($loop->first && !empty(old("status-failed")))
                            <option selected>
                                {{ trans("privatePublishTechnicalAnalysis.select_option") }}
                            </option>
                        @endif
                        <option value="{{ $cbStatusType->status_type_key }}"
                            @if(old("status-failed")==$cbStatusType->status_type_key) selected @endif>
                            {{ $cbStatusType->name }}
                        </option>
                    @empty
                        <option disabled selected>
                            {{ trans("privatePublishTechnicalAnalysis.no_status_types_available") }}
                        </option>
                    @endforelse
                </select>
            </div>
        </div>
    </div>

    {!! $form->make() !!}
@endsection


@section("scripts")
    <script>
        $(document).ready(function() {
            createNewDescriptionQuestion();
        });

        function createNewDescriptionQuestion() {
            originalContainer = $("#selects-container");
            
            newNumber = parseInt(originalContainer.attr("data-number"))+1;
            originalContainer.attr("data-number",newNumber);
            
            newContainer = originalContainer.clone().find("> ");

            newContainer
                .find("select[name='description-question']")
                .attr("name", "description-question[" + newNumber + "]");
            newContainer
                .find("select[name='description-parameter']")
                .attr("name", "description-parameter[" + newNumber + "]");

            $("#questions-to-use").append(newContainer);
        }
    </script>
@endsection