@extends('private._private.index')

@section('content')
    <div class="card flat topic-data-header">
        <p><label for="contentStatusComment" style="margin-left:5px; margin-top:5px;">{{trans('privateCbs.pad')}}</label>  {{$cb_title}}<br></p>
        <p><label for="contentStatusComment" style="margin-left:5px;">{{trans('privateCbs.author')}}</label>  {{$author}}<br></p>
        <p><label for="contentStatusComment" style="margin-left:5px; margin-bottom:5px;">{{trans('privateCbs.start_date')}}</label>  {{$cb_start_date}}</p>
    </div>

    <hr style="margin: 10px 0 10px 0">

    <div class="container-fluid">
        <div class="row">
            <!-- Form -->
            @php
            $form = ONE::form('question', trans('privateCbs.details'), 'cb', 'questions')
                ->settings([
                    "model" => isset($question) ? $question : null,
                    'id' => isset($question) ? $question->tech_analysis_question_key : null])
                ->show('TechnicalAnalysisProcessesController@edit', 'TechnicalAnalysisProcessesController@delete',
                    ['type' => $type, 'cbKey' => $cbKey, 'techAnalysisQuestionKey' => isset($question) ? $question->tech_analysis_question_key : null],
                    'TechnicalAnalysisProcessesController@showQuestions',
                    ['type' => $type, 'cbKey' => isset($cbKey) ? $cbKey : null], $edit ?? null, $delete ?? null)
                ->create('TechnicalAnalysisProcessesController@store', 'TechnicalAnalysisProcessesController@showQuestions',
                    ['type' => $type, 'cbKey' => isset($cbKey) ? $cbKey : null, 'techAnalysisQuestionKey' => isset($question) ? $question->tech_analysis_question_key : null])
                ->edit('TechnicalAnalysisProcessesController@update', 'TechnicalAnalysisProcessesController@showQuestions',
                    ['type' => $type, 'cbKey' => $cbKey, 'techAnalysisQuestionKey' => isset($question) ? $question->tech_analysis_question_key : null])
                ->open();
            @endphp
            @if( !empty($languages) && count($languages) > 0)
                <div class="row">
                    <div class="col-12">
                        @foreach($languages as $language)
                            @php $form->openTabs('tab-translation-' . $language->code, $language->name); @endphp
                            <div style="padding:10px;">
                                {!! Form::oneTextArea($language->default == true ? 'required_question_'.$language->code:'question_'.$language->code,
                                                  trans('privateCbs.question'),
                                                  $question->translations->{$language->code}->question ?? null,
                                                  ['class' => 'form-control', 'id' => 'question_'.$language->code]) !!}
                            </div>
                        @endforeach
                        @php $form->makeTabs(); @endphp
                    </div>
                </div>
            @endif

            {!! Form::oneCheckbox('acceptable', trans('privateCbs.acceptable'), $question->acceptable ?? 0, $question->acceptable ?? 0, ['id' => 'acceptable']) !!}
            {!! Form::oneText('code', trans('privateCbs.code'), $question->code ?? null, ['id' => 'code']) !!}

            @if(ONE::actionType('question') == 'show')
                {!! Form::oneTextArea('question', trans('privateCbs.question'),  isset($question->question) ? $question->question : null,['class' => 'form-control', 'id' => 'question']) !!}
                {!! Form::oneText('created_by', trans('privateCbs.created_by'),  isset($question->created_by) ? $question->created_by : null,['class' => 'form-control', 'id' => 'created_by']) !!}
                {!! Form::oneText('updated_by', trans('privateCbs.updated_by'),  isset($question->updated_by) ? $question->updated_by : null,['class' => 'form-control', 'id' => 'updated_by']) !!}
            @endif

            {!! $form->make() !!}
        </div>
    </div>
@endsection

@section('scripts')

@endsection