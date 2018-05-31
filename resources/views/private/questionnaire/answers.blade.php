@extends('private._private.index')

@section('header_styles')
    <style>
        .statisticsIcon i{
            color:#3c8dbc!important;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box" style="border-top-color: #62a351;">
                <div class="box-header">
                    <div class="box-tools">
                        <a href="{{action('QuestionnairesController@downloadPdfAnswer', ['key' => $formKey, 'answer_key' => $formReply->form_reply_key])}}" target="_blank" class="btn btn-flat btn-sm btn-success" ><i class="fa fa-download"></i>{{trans('privateQAnswers.donwload_pdf')}}</a>
                    </div>
                </div>
                <div class="box-body">
                    <form role="form" action="{{action('PublicQController@store')}}" method="post" id="formQuestion" name="formQuestion" onsubmit="removeHistory()">
                        <input id="questionnaire_id" type="hidden" value="{{$formKey}}" name="questionnaire_id">
                        @php $i =1; @endphp
                        @foreach ($questionsAll as $item)
                            <div class="row setup-content" id="step-{{$i}}">
                                <div class="col-md-12">
                                    @if($i == 1)
                                        <div style="border-bottom: 1px solid #f4f4f4;margin-bottom: 20px;padding-left: 40px; padding-right: 40px;">
                                            <h2>{{$titleQuestionnaire}}</h2>
                                                <h5 style="text-align: justify;"><i>Status: {{$formReply->completed == 1? 'Completed ('.$formReply->updated_at.')': 'Incomplete'}}</i></h5>
                                        </div>
                                    @endif
                                    <h3 style="text-align: center">{{$item->title}}</h3><br>

                                    <!-- Questions -->
                                    @foreach ($item->questions as $question)
                                        @if(strtoupper(preg_replace('/\s+/', '', $question->question_type->name)) == 'TEXT')
                                            <div class="form-group" style="padding-bottom: 20px;">
                                                <label>{{$question->question}}
                                                    @if($question->mandatory == 1)
                                                        <span style="color:red">*</span>
                                                    @endif
                                                </label>
                                                <input type="text" name="text_{{$question->id}}" class="form-control" id="text_{{$question->id}}" value="{{$question->reply}}" {{($question->mandatory == 1)? 'required':''}}>
                                            </div>
                                        @elseif(strtoupper(preg_replace('/\s+/', '', $question->question_type->name)) == 'FILE')
                                            @if( !empty($question->reply) && $question->reply!="null" )
                                                @foreach(json_decode($question->reply)  as $file )
                                                    <a href="{{ action("FilesController@downloadFile",["id" => $file->id, "code" => $file->code]) }}" target="_blank" class="statisticsIcon">
                                                        {!! ONE::fileIconByFilename($file->name) !!}
                                                        {{ $file->name }}
                                                    </a><br>
                                                @endforeach
                                            @endif
                                        @elseif(strtoupper(preg_replace('/\s+/', '', $question->question_type->name)) == 'TEXTAREA')
                                            <div class="form-group" style="padding-bottom: 20px;">
                                                <label>{{$question->question}}
                                                    @if($question->mandatory == 1)
                                                        <span style="color:red">*</span>
                                                    @endif
                                                </label>
                                                <textarea  name="textarea_{{$question->id}}" rows="4" style="resize:vertical;"  class="form-control" id="textarea_{{$question->id}}" {{($question->mandatory == 1)? 'required':''}}>{{$question->reply}}</textarea>
                                            </div>
                                        @elseif(strtoupper(preg_replace('/\s+/', '', $question->question_type->name)) == 'DROPDOWN')
                                            <div class="form-group">
                                                <label>{{$question->question}}
                                                    @if($question->mandatory == 1)
                                                        <span style="color:red">*</span>
                                                    @endif
                                                </label>
                                                <p></p>
                                                <select class="form-control" id="optionSelect" name="optionsDropdown_{{$question->id}}" {{($question->mandatory == 1)? 'required':''}} disabled>
                                                    @foreach ($question->question_options as $option) {
                                                        @if($question->reply == $option->id)
                                                            <option  value="{{$option->id}}" selected>{{$option->label}}</option>
                                                        @else
                                                            <option  value="{{$option->id}}">{{$option->label}}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                        @elseif(strtoupper(preg_replace('/\s+/', '', $question->question_type->name)) == 'RADIOBUTTONS')
                                            <div class="form-group">
                                                <label>{{$question->question}}
                                                    @if($question->mandatory == 1)
                                                        <span style="color:red">*</span>
                                                    @endif
                                                </label>

                                                <div>
                                                    <div class="btn-group" data-toggle="buttons">
                                                        @foreach($question->question_options as $option)

                                                            @if($question->reply == $option->id)
                                                                <label class="btn btn-secondary active" id="radio_label_{{$option->id}}" title="{{$option->label}}">
                                                                    <input type="radio" name="optionsRadios_{{$question->id}}" id="radio_{{$option->id}}" value="radio_{{$option->id}}" checked autocomplete="off" {{($question->mandatory == 1)?'required':''}}>
                                                            @else
                                                                <label class="btn btn-secondary" id="radio_label_{{$option->id}}" title="{{$option->label}}">
                                                                    <input type="radio" name="optionsRadios_{{$question->id}}" id="radio_{{$option->id}}" value="radio_{{$option->id}}" autocomplete="off" {{($question->mandatory == 1)?'required':''}}>
                                                            @endif

                                                            @if($option->icon != null)
                                                                <img src="{{URL::action("FilesController@download",[$option->icon->file_id, $option->icon->file_code, 1])}}"  id="questionOptionImage" style="height:50px">
                                                            @else
                                                                {{$option->label}}
                                                            @endif

                                                                </label>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        @elseif(strtoupper(preg_replace('/\s+/', '', $question->question_type->name)) == 'CHECKBOX')
                                            <div class="form-group">
                                                <label>{{$question->question}}
                                                    @if($question->mandatory == 1)
                                                        <span style="color:red">*</span>
                                                    @endif
                                                </label>

                                                <div>

                                                    @foreach ($question->question_options as $option)

                                                        <div class="checkbox">
                                                            <label id="radio_label_{{$option->id}}" title="{{$option->label}}">

                                                                @if($option->icon != null)
                                                                    <img src="{{URL::action("FilesController@download",[$option->icon->file_id, $option->icon->file_code, 1])}}"  id="questionOptionImage" style="height:50px">
                                                                @endif

                                                                @if(in_array($option->id, $question->reply))
                                                                    <input type="checkbox" name="optionsCheck_{{$question->id}}[]" id="check_{{$option->id}}" value="check_{{$option->id}}" autocomplete="off" checked {{($question->mandatory == 1)?'required':''}}>
                                                                @else
                                                                    <input type="checkbox" name="optionsCheck_{{$question->id}}[]" id="check_{{$option->id}}" value="check_{{$option->id}}" autocomplete="off" {{($question->mandatory == 1)?'required':''}}>
                                                                @endif

                                                                @if($option->icon == null)
                                                                    {{$option->label}}
                                                                @endif

                                                            </label>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>

                                        @endif
                                    @endforeach
                                </div>
                            </div>
                            @php $i++@endphp
                        @endforeach

                        {{--
                            {!!$questions!!}
                        --}}
                    </form>
                </div>
                <div class="box-footer">
                    <a href="{{action('QuestionnairesController@show', $formKey)}}" class="btn btn-flat empatia"><i class="fa fa-arrow-left"></i> {{trans('privateQAnswers.back')}}</a>
                </div>
                <!-- /.box-body -->
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
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
        });

    </script>
@endsection
