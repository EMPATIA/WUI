@extends('private._private.index')

@section('content')
    <!-- Latest compiled and minified JavaScript -->
    <div class="box">
        <div class="box-body">
            <!-- Download -->
            <div class="row">
                <div class="col-md-12">
                    <div class="pull-left">
                        <a href="{{action('QuestionnaireAnswersController@statisticsPdf', ['key' => $formKey])}}" type="button" class="btn btn-sm btn-flat btn-submit">
                            <i class="fa fa-download"></i> {!! trans('privateQuestionnaire.downloadPdf') !!}</a>
                    </div>
                </div>
            </div>

            <!-- Select -->
            <div class="row">
                <div class="col-md-12">
                <div class="margin-top-20">
                    <dt>{!! trans('privateQuestionnaire.selectQuestion') !!}</dt>
                    <dd style="width: 100%;">
                        @php
                            $i = 1; $x = 0; $pieChart = 0;
                        @endphp
                        <select id="selectQuestion" name="selectQuestion" class="select2-default form-control" style="width: 100%;">
                            <option value=""> -- {!! trans('privateQuestionnaire.chooseOption') !!} -- </option>
                            @foreach ($questionsAll as $item)
                                @if($x==0)
                                    {{--
                                        <li class="nav-item">
                                        <a class="nav-link active" data-toggle="tab" href="#group_{{$item->question_group_key}}">{{$item->title}}</a>
                                    </li>--}}
                                    <option value="{{$item->question_group_key}}">{{$item->title}}</option>
                                    @php $x++; @endphp
                                @else
                                    {{--
                                    <li class="nav-item">
                                        <a class="nav-link" data-toggle="tab" href="#group_{{$item->question_group_key}}">{{$item->title}}</a>
                                    </li>--}}
                                    <option value="{{$item->question_group_key}}">{{$item->title}}</option>
                                @endif
                            @endforeach
                        </select>
                    </dd>
                    <hr class="default-hr">
                    <br>
                </div>
                </div>
            </div>

            @foreach ($questionsAll as $item)
                @if($x==0)
                <div id="group_{{$item->question_group_key}}" class="question_groups" style="visibility: hidden;height: 0;">
                    @php $x++; @endphp
                @else
                <div id="group_{{$item->question_group_key}}" class="question_groups" style="visibility: hidden;height: 0;">
                @endif

                    <div class="row setup-content" id="step-{{$i}}">
                        <div class="col-md-12">
                            <!-- Questions -->
                            @foreach (!empty($item->questions) ? $item->questions :[] as $question)

                                @php
                                    $pieChart++;
                                    $printPieChart = false;
                                @endphp

                                @if(strtoupper(preg_replace('/\s+/', '', $question->question_type->name)) == 'TEXT')
                                    <div class="row">
                                        <div class="form-group col-md-12">
                                        <label>{{$question->question}}
                                            @if($question->mandatory == 1)
                                                <span style="color:red">*</span>
                                            @endif
                                        </label>
                                        <p></p>
                                        <div class="show-all btn empatia">
                                            <input type="hidden" value="{{$question->id}}">
                                            {{ trans('privateQuestionnaire.viewAll') }}
                                        </div>
                                    </div>
                                    </div>
                                @elseif(strtoupper(preg_replace('/\s+/', '', $question->question_type->name)) == 'TEXTAREA')
                                    <div class="row">
                                        <div class="form-group col-md-12">
                                        <label>{{$question->question}}
                                            @if($question->mandatory == 1)
                                                <span style="color:red">*</span>
                                            @endif
                                        </label>
                                        <p></p>
                                        <div class="show-all btn empatia">
                                            <input type="hidden" value="{{$question->id}}">
                                            {{ trans('privateQuestionnaire.viewAll') }}
                                        </div>
                                    </div>
                                    </div>
                                @elseif(strtoupper(preg_replace('/\s+/', '', $question->question_type->name)) == 'DROPDOWN')
                                    @php $printPieChart = true; @endphp
                                    <div class="row">
                                        <div class="form-group col-md-4">
                                        <label>{{$question->question}}
                                            @if($question->mandatory == 1)
                                                <span style="color:red">*</span>
                                            @endif
                                        </label>
                                        <p></p>

                                        <ul class="question-options">
                                            @foreach ($question->question_options as $option)
                                                <li>{{$option->label}}</li>
                                            @endforeach
                                        </ul>

                                    </div>
                                @elseif(strtoupper(preg_replace('/\s+/', '', $question->question_type->name)) == 'RADIOBUTTONS')
                                    @php $printPieChart = true; @endphp
                                    <div class="row">
                                        <div class="form-group col-md-4">
                                        <label>{{$question->question}}
                                            @if($question->mandatory == 1)
                                                <span style="color:red">*</span>
                                            @endif
                                        </label>
                                        <ul class="question-options">
                                            @foreach ($question->question_options as $option)
                                                <li>{{$option->label}}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @elseif(strtoupper(preg_replace('/\s+/', '', $question->question_type->name)) == 'CHECKBOX')
                                    @php $printPieChart = true; @endphp
                                    <div class="row">
                                        <div class="col-12 col-md-4">
                                        <label>{{$question->question}}
                                            @if($question->mandatory == 1)
                                                <span style="color:red">*</span>
                                            @endif
                                        </label>

                                        <ul class="question-options">
                                            @foreach ($question->question_options as $option)
                                                <li>
                                                    @if($option->icon != null)
                                                        <img src="{{URL::action("FilesController@download",[$option->icon->file_id, $option->icon->file_code, 1])}}"  id="questionOptionImage" style="height:50px">
                                                    @endif
                                                    {{$option->label}}
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                @if($printPieChart)
                                        @php $answers = 0; @endphp
                                        <div class="col-12 col-md-8 text-left">
                                        <div id="pie_chart_{{$pieChart}}" style="height:250px;margin-bottom:50px;"></div>
                                    </div>
                                    </div>
                                    <script>
                                        var data = [
                                                @foreach ($question->question_options as $option)
                                                @php $answers += $option->total; @endphp
                                            {"value": {{ $option->total }}, "name": "{{$option->label}}"},

                                                @endforeach
                                                @if($answers<$totalAnswers)
                                            {"value": {{ ($totalAnswers-$answers) }}, "name": "NR"}
                                            @endif
                                        ]
                                        @if($answers>0)

                                        d3plus.viz()
                                            .container("#pie_chart_{{$pieChart}}")
                                            .data(data)
                                            .type("pie")
                                            .id("name")
                                            .size("value")
                                            .font({"family": "Helvetica, Arial, sans-serif", "color": "#000"}).resize(true)
                                            .draw()
                                        @endif
                                    </script>

                                @endif
                            @endforeach

                            @if(empty($item->questions))
                                <div class="row">
                                    <div class="col-12 text-center">
                                        <br><br><br>
                                        <i class="fa fa-eye-slash" aria-hidden="true"></i> {!! trans('privateQuestionnaire.empty') !!}
                                        <br><br><br>
                                    </div>
                                </div>
                            @endif


                        </div>
                    </div>
                    @php $i++; @endphp

                </div>
            @endforeach


        </div>
        <div class="box-footer">
                        <a href="{{action('QuestionnairesController@show', $formKey)}}" class="btn btn-flat empatia"><i class="fa fa-arrow-left"></i> Back</a>
                    </div>
    </div>


    </div>

    <!-- Modal - S-how answers -->
    <div class="modal fade" role="dialog" id="showAnswers" >
        <div class="modal-dialog" style="overflow-y: scroll; max-height:85%;">
            <div class="modal-content">
                <div class="card-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">{{trans("privateQuestionnaireAnswers.showAllAnswers")}}</h4>
                </div>
                <div class="modal-body">
                    <div class="card flat">

                        <div class="card-header"></div>
                        <div class="card-body" id="card-body">


                        </div>
                    </div>
                </div>
                <div class="modal-footer">

                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
@endsection



@section('scripts')
    <script>
        $('.show-all').on('click', function () {
            var id = $(this).find("input[type='hidden']").val();
            $.ajax({
                url: '{!! action('QuestionnaireAnswersController@getListOfAnswers') !!}',
                method: 'POST',
                data:{
                    'id' : id
                },
                success: function(response)
                {
                    var input;
                    $.each(response, function (i, item) {
                        input = '<div class="col-sm-12 form-group"><input type="text" class="form-control" value="'+item.answer+'" readonly></div>';
                        $('#card-body').append(input);

                    });
                    $('#showAnswers').modal('show');
                }

            })
        });

        $(document).ready(function () {
            getSidebar('{{ action("OneController@getSidebar") }}', 'statistics', '{{ isset($questionnaire) ? $questionnaire->form_key : null }}', 'q' )

            var  curStep = $(".setup-content:visible"),
                curInputs = curStep.find("input[type='text']"),
                curTextAreas = curStep.find("textarea"),
                curRadios = curStep.find("input[type='radio']"),
                curCheckBoxes = curStep.find("input[type='checkbox']"),
                curDropdown = curStep.find("select");

            // Block text inputs
            for (var i = 0; i < curInputs.length; i++) {
                $('#' + curInputs[i].id).prop('disabled', true);
            }

            // Block text area inputs
            for (var i = 0; i < curTextAreas.length; i++) {
                $('#' + curTextAreas[i].id).prop('disabled', true);
            }

            // Block radio inputs
            for (var i = 0; i < curRadios.length; i++) {
                $('#' + curRadios[i].id).closest('.btn-group').css('pointer-events', 'none');
            }

            // Block check inputs
            for (var i = 0; i < curCheckBoxes.length; i++) {
                $('#' + curCheckBoxes[i].id).attr('disabled', true);;
                $('#' + curCheckBoxes[i].id).css('pointer-events', 'none');
            }
            // Check dropdowns
            for (var i = 0; i < curDropdown.length; i++) {

            }

            // selectQuestion change event
            $('#selectQuestion').on('change',function(){
                $(".question_groups").css("visibility","hidden");
                $(".question_groups").css("height","0");
                $("#group_"+$(this).val()).css("visibility","visible");
                $("#group_"+$(this).val()).css("height","100%");
            });

        });
    </script>
@endsection
