@extends('private._private.index')


@section('content')
    <div class="row">
        <div class="col-md-12">
            @php
            $form = ONE::form('question', trans('privateQuestions.details'), 'q', 'q')
                    ->settings(["model" => isset($question) ? $question : null,'key'=>isset($question) ? $question->question_key : null])
                    ->show('QuestionsController@edit', 'QuestionsController@delete', ['key' => isset($question) ? $question->question_key : null], 'QuestionGroupsController@show', isset($question) ? $question->question_group->question_group_key : null )
                    ->create('QuestionsController@store', 'QuestionGroupsController@show', ['key' => isset($question) ? $question->question_group->question_group_key : (isset($questionGroupKey) ? $questionGroupKey : null)])
                    ->edit('QuestionsController@update', 'QuestionsController@show', ['key' => isset($question) ? $question->question_key : null])
                    ->open();
            @endphp

            <hr style="margin: 10px 0 10px 0">

            <input type="hidden" id="correctOption" name="correctOption" value="">
            @if( !empty($languages) && count($languages) > 0)
                <div class="row">
                    <div class="col-12">
                        @php
                        $strInputIds = "";
                        $inputId = 0;
                        $i = 0;
                        @endphp
                        @foreach($languages as $language)
                            @php $form->openTabs('tab-translation-' . $language->code, $language->name); @endphp
                            <div style="padding:10px;">
                                <!--Question-->
                            {!! Form::oneText($language->default == true ? 'question_'.$language->code: 'question_'.$language->code,
                            trans('privateQuestionnaire.question'),
                            isset($translations[$language->code]) ? $translations[$language->code]->question : null,
                            ['class' => 'form-control', 'id' => 'question_'.$language->code]) !!}
                            <!--Description-->
                                {!! Form::oneText($language->default == true ? 'description_'.$language->code: 'description_'.$language->code,
                                        trans('privateQuestionnaire.description'),
                                        isset($translations[$language->code]) ? $translations[$language->code]->description : null,
                                        ['class' => 'form-control', 'id' => 'description_'.$language->code, 'size' => '30x2', 'style' => 'resize: vertical']) !!}
                            </div>

                            @if(ONE::actionType('question') == 'edit' or ONE::actionType('question') == 'create')
                            <!--
                                <hr style="margin: 10px 0 10px 0">
                                -->
                                <div class="box box-primary options" style="margin-top:10px; border: 1px solid #cecece; border-top:3px solid #3c8dbc;">
                                    <div id="tabOptionLoader" class="tabOptionLoader" style="display:none;height:100%;width:100%;position:absolute;top:0;left:0;background-color: rgba(250, 250, 250, 0.75);z-index:100;">
                                        <div  class="text-center" style="display:flex;justify-content:center;align-items:center;height:100%;width:100%;">
                                            <img src="{{ asset('images/spinner.gif') }}" alt="Loading" >
                                        </div>
                                    </div>

                                    <div class="box-header">
                                        <div class="box-title">
                                            {{trans('privateQuestionGroup.questionOptions')}}
                                        </div>
                                        <div class="box-tools pull-right">
                                            <a id="addOption" class="btn btn-flat btn-create btn-sm addOption">
                                                <i class="fa fa-plus"></i>
                                            </a>
                                        </div>
                                    </div>

                                    @php
                                    $languageCode = $language->code;
                                    @endphp
                                    <div id="questionOptionsList-{!! $languageCode !!}" style="padding:10px">
                                        @if($i == 0)
                                            @php
                                            $strInputIds = "";
                                            $inputId = 0;
                                            $defaultLang = $languageCode;
                                            @endphp
                                            @foreach( !empty($questionOptions) ? $questionOptions : [] as $questionOption)
                                                @include('private.questionnaire.addQuestionOption')
                                                @php
                                                $strInputIds .= $inputId.(count($questionOptions)-1 > $inputId ? ",":"");
                                                $inputId ++;
                                                @endphp
                                            @endforeach
                                        @else
                                            @php
                                            $inputId = 0;
                                            @endphp
                                            @foreach( !empty($questionOptions) ? $questionOptions : [] as $questionOption)
                                                @include('private.questionnaire.addQuestionOptionTranslation')
                                                @php
                                                $inputId ++;
                                                @endphp
                                            @endforeach
                                        @endif
                                        @php $i++ @endphp
                                    </div>

                                </div>
                            @endif


                        @endforeach
                        @php $form->makeTabs(); @endphp
                    </div>
                </div>
            @endif

            @if(ONE::actionType('question') == 'show')
                {!! Form::oneText('question', trans('privateQuestionnaire.question'), isset($question) ? $question->question : null, ['class' => 'form-control', 'key' => 'question']) !!}
                {!! Form::oneTextArea('description', trans('privateQuestionnaire.description'), isset($question) ? $question->description : null, ['class' => 'form-control', 'id' => 'description', 'size' => '30x2', 'style' => 'resize: vertical']) !!}
            @endif

            {!! Form::oneSelect('question_type_key', trans('privateQuestionnaire.question_type'), isset($listType) ? $listType : null, isset($question) ? $question->question_type->question_type_key : null, isset($listType[isset($question->question_type->question_type_key) ? $question->question_type->question_type_key : '']) ? $listType[$question->question_type->question_type_key]: null, ['class' => 'form-control', 'id' => 'question_type_key']) !!}
            {!! Form::oneCheckbox('mandatory', trans('privateQuestionnaire.mandatory'), null, ((isset($question) ? $question->mandatory : null) == 1 ? true : false), ['id' => 'mandatory']) !!}
            {!! Form::hidden('question_group_key', isset($question) ? $question->question_group->question_group_key: (isset($questionGroupKey) ? $questionGroupKey : null)) !!}
            {!! Form::hidden('position', null) !!}
            @if(ONE::actionType('question') == 'show' && (strtoupper(preg_replace('/\s+/', '', $question->question_type->name)) != 'TEXTAREA') && (strtoupper(preg_replace('/\s+/', '', $question->question_type->name)) != 'TEXT'))
                <div class="">
                    <a href="" class="uppercase" data-toggle="modal" data-target="#optionsModal">{{trans("privateQuestionnaire.reuseOptions")}}</a>
                </div>

                <!-- Reuse Options Modal -->
                <div class="modal fade" tabindex="-1" role="dialog" id="optionsModal" >
                    <form role="form" action="{{action('QuestionOptionsController@useOptions')}}" method="post" name="useOptions">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="question_key" value="{{ $question->question_key  }}">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="card-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title">{{trans('privateQuestionOption.useOptions')}}</h4>
                                </div>
                                <div class="modal-body">
                                    <div class="card flat">
                                        <div class="card-header">{{trans('privateQuestionOption.selectOption')}}</div>
                                        <div class="card-body">
                                            <div class="form-group ">
                                                <label for="reuse_question_key">{{trans('privateQuestionOption.reuseQuestionOptions')}}</label>
                                                <select id="reuse_question_key" class="form-control" name="reuse_question_key" required>
                                                    <option selected="selected" value="">{{trans('privateQuestionOption.selectValue')}}</option>
                                                    @foreach($reuseOptions as $key => $option)
                                                        <option value="{{$key}}">{{strlen($option) > 50 ? substr($option, 0, 50).'...' : $option}}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{trans("privateQuestionnaire.close")}}</button>
                                    <button type="submit" class="btn btn-primary" id="buttonSubmit">{{trans("privateQuestionnaire.useOptions")}}</button>
                                </div>
                            </div><!-- /.modal-content -->
                        </div><!-- /.modal-dialog -->
                    </form>
                </div><!-- /.modal -->
            @endif

            @if(ONE::actionType('question') == 'show' and strtoupper(preg_replace('/\s+/', '', $question->question_type->name)) != 'TEXT' and strtoupper(preg_replace('/\s+/', '', $question->question_type->name)) != 'TEXTAREA')
                <div class="box box-primary" style="margin-top: 35px; border: 1px solid #cecece; border-top:3px solid #3c8dbc;">
                    <div class="box-header">
                        <div class="box-title">
                            {{trans('privateQuestionGroup.questionOptions')}}
                        </div>
                        <div class="box-tools pull-right">
                            @if($question->reuse_question_options == false)
                                <a class="btn btn-flat btn-xs btn-submit padding-inherit" href=" {!!  action('QuestionsController@reuseOptions',$question->question_key) !!}">
                                    {!! trans('privateQuestionOption.reuse') !!}
                                </a>
                            @elseif($question->reuse_question_options == true)
                                <a class="btn btn-flat btn-sm btn-danger " href=" {!!  action('QuestionsController@reuseOptions',$question->question_key) !!}">
                                    {!! trans('privateQuestionOption.reuse') !!}
                                </a>
                            @endif
                            {!! ONE::actionButtons($question->question_key, ['create' => 'QuestionOptionsController@create']) !!}
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="dd" id="nestable" style="padding: 10px">

                        </div>
                    </div>
                </div>
            @elseif(ONE::actionType('question') == 'edit' or ONE::actionType('question') == 'create')

            <!-- Hidden inputs -->
                <input id="questionOptionsIds" name="questionOptionsIds" value="{!! $strInputIds !!}" type="hidden" >
                <input id="questionOptionsRemove" name="questionOptionsRemove" value="" type="hidden" >

            @endif
            {!! $form->make() !!}
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        var value = "";
        $(function () {

            $("form").on('submit', function(){
                var length = $('.options').find('input:checkbox').length;
                var array = [];
                $('.options').find('input:checkbox').each(function(i){
                    if($(this).is(':checked')){
                        @if( ONE::actionType('question') == 'create' )
                            array.push(i+1);
                        @else
                            array.push(i);
                        @endif
                    }
                })
                $("#correctOption").val(array.toString());
            })

            @if(ONE::actionType('question') == 'show' and strtoupper(preg_replace('/\s+/', '', $question->question_type->name)) != 'TEXT' and strtoupper(preg_replace('/\s+/', '', $question->question_type->name)) != 'TEXTAREA')
                $.get('{{ URL::action('QuestionOptionsController@getQuestionOptions', $question->question_key)}}',
                    function (data) {
                        $("#nestable").html(data);
                    })
                    .fail(function (xhr, status, error) {
                        alert("An AJAX error occured: " + status + "\nError: " + error);
                    });
            @endif
        });

        @if(ONE::actionType('question') == 'show' and strtoupper(preg_replace('/\s+/', '', $question->question_type->name)) != 'TEXT' and strtoupper(preg_replace('/\s+/', '', $question->question_type->name)) != 'TEXTAREA')
            $('.dd').nestable({
            maxDepth:1,
            dropCallback: function (details) {
                var order = [];
                $(".dd-list").find('.dd-item').each(function (index, elem) {
                    order[index] = $(elem).attr('data-id');
                });
                $.post('{{ URL::action('QuestionOptionsController@updateOrder')}}',
                        {
                            _token: "{{ csrf_token() }}",
                            order: JSON.stringify(order)
                        },
                        function (data) {
                            // console.log('data '+data);
                        })
                        .done(function ($result) {
                            //console.log($result);
                            if($result == 'false'){
                                toastr.error('{{ trans('questionnaire.failedInOrderQuestionOptions') }}', '', {timeOut: 3000,positionClass: "toast-bottom-right"});
                                location.reload();
                            }

                        })
                        .fail(function () {
                            //alert('fail');
                            toastr.error('{{ trans('questionnaire.failedInOrderQuestionOptions') }}', '', {timeOut: 3000,positionClass: "toast-bottom-right"});
                            location.reload();
                        });
            }
        });
                @elseif( (ONE::actionType('question') == 'edit' or ONE::actionType('question') == 'create'))

        var inputId = {{ !empty($inputId ) ? $inputId : 0 }};

        $(".addOption").click(function() {

            $(".tabOptionLoader").show();

            // questionOptionsIds array
            var questionOptionsIds = $("#questionOptionsIds").val();
            questionOptionsIds = (questionOptionsIds == '') ? inputId : (questionOptionsIds+","+inputId);
            $("#questionOptionsIds").val( questionOptionsIds );

            $.ajax({
                @if(!empty($question))
                url: '{{ URL::action('QuestionOptionsController@addQuestionOption', $question->question_key) }}',
                @else
                url: '{{ URL::action('QuestionOptionsController@addQuestionOption') }}',
                @endif
                method: 'POST',
                data: {
                    inputId: inputId,
                    _token: "{{ csrf_token()}}"
                },
                success: function(response){
                    $(".tabOptionLoader").fadeOut();
                    // Check if response is what expected
                    if( typeof(response["{!! $defaultLang !!}"]) !== 'undefined' ){
                        window.tmp = response;
                        @foreach($languages as $language)
                            $("#questionOptionsList-{!! $language->code !!}").append(response["{!! $language->code !!}"]);
                        @endforeach
                        $(".select2").select2();
                        $("#label_"+inputId).focus();
                    } else {
                        // You aren't currently logged
                        location.reload();
                    }
                },
                error: function(msg){
                    console.log(msg);
                }
            });

            inputId = inputId+1;

        });

        function removeQuestionOption(id){
            var key2Remove = $("#question_option_key_"+id).val();

            if( $("#questionOptionsRemove").val() == ""){
                $("#questionOptionsRemove").val(key2Remove);
            } else {
                $("#questionOptionsRemove").val($("#questionOptionsRemove").val()+","+key2Remove);
            }

            $(".div"+id).remove();
            var questionOptionsIds = $("#questionOptionsIds").val();
            if(questionOptionsIds!=""){
                var arrayIds = questionOptionsIds.split(",");
                var strOptionsIds = "";

                for(i = 0; i < arrayIds.length; i++){
                    if( arrayIds[i]>=id ) {
                        strOptionsIds += arrayIds[i]-1 +",";
                    }
                }

                if(strOptionsIds!=""){
                    strOptionsIds = strOptionsIds.substring(0, strOptionsIds.length-1);
                }
                inputId = inputId-1;
                $("#questionOptionsIds").val(strOptionsIds);
            }
        }
        @endif

        $(".select2").select2();

        function changeQuestionIcon(inputId,imageSrc) {
            $("#questFaPicture"+inputId).hide();
            $("#questIconImage"+inputId).show();
            $('#questRemovePicture'+inputId).show();
            $('#questIconImage'+inputId).attr('src', imageSrc);
            $('#iconsModal'+inputId).modal('toggle');
        }
        function removeQuestionIcon(inputId,imageSrc) {
            $("#questFaPicture"+inputId).show();
            $("#questIconImage"+inputId).hide();
            $('#questRemovePicture'+inputId).hide();
            $('#questIconImage'+inputId).attr('src', '');
            $('.inputQuestionIcon'+inputId).prop('checked', false);
            $('.inputQuestionLabel'+inputId).removeClass("active");
        }
    </script>
@endsection


