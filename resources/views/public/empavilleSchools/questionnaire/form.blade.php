@section('header_styles')
    <!-- Questionnaire -->
    <link rel="stylesheet" href="{{asset('css/cml/form-css.css')}}">
    <link rel="stylesheet" href="{{asset('css/stepwizard.css')}}">
    <link rel="stylesheet" href="{{asset('css/flaticon/flaticon.css')}}">
@endsection
@extends('public.default._layouts.index')

@section('content')
    <section>
        <div class="container-fluid form">
            <div class="row" {{(count($questionsAll)== 1) ? 'hidden' : null}}>
                <div class="col-md-12 whiteBgnd">

                    <div id="wrapper">
                        <div class="setup-panel">
                            @for ($i = 1; $i <= count($questionsAll); $i++)
                                <a href="#step-{{$i}}" type="button" class='baricon {{($i == 1)? 'btn-circle-active':'disabled btn-circle-disabled'}}'><p>{{$i}}</p></a>
                                @if($i < count($questionsAll))
                                    <span id="bar{{$i}}" class='progress_bar'></span>
                                @endif
                            @endfor
                            <span ><p id="percentagem">0%</p></span>
                        </div>
                    </div>
                </div>
            </div>
            <form role="form" action="{{action('PublicQController@store')}}" method="post" id="formQuestion" name="formQuestion" onsubmit="removeHistory()">
                <input id="questionnaire_key" type="hidden" value="{{$formKey}}" name="questionnaire_key">

                <?php $i =1?>
                <?php $questionNumber =1?>
                @foreach ($questionsAll as $item)
                    <div class="setup-content" id="step-{{$i}}">
                        <div class="row form-subtitle">
                            <div class="col-md-12"  style="">
                                @if($i == 1)
                                    <h2>{{$titleQuestionnaire}}</h2>
                                @else
                                    <h2>{{$item->title}}</h2>
                                @endif
                            </div>
                        </div>

                        @if (count($item->description) > 0)
                            <div class="row form-description">
                                <div class="col-md-12"  style="">
                                    <p>{{$item->description}}</p>
                                </div>
                            </div>
                        @endif
                        <div class="row">
                            <div class="col-md-12"  style="">
                                <div class="container-fuid form-content">
                                    @foreach ($item->questions as $question)
                                        <div class="row parentHidden"  {{--$question->hidden == true ? 'hidden': ''--}} id="question_{{$question->question_key}}">
                                            @if(strtoupper(preg_replace('/\s+/', '', $question->question_type->name)) == 'TEXT')
                                                <div>
                                                    <div class="col-md-6">
                                                        <p style="margin: 5px 0 5px;"><b>{{$questionNumber}}.</b>&nbsp;{{$question->question}}
                                                            @if($question->mandatory == 1)
                                                                <span style="color:red">*</span>
                                                            @endif
                                                        </p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group" style=" margin-bottom: 0px">
                                                            <input type="text" name="text_{{$question->id}}" class="form-control" id="text_{{$question->id}}" value="{{$question->reply}}" {{($question->mandatory == 1)? 'required':''}}>
                                                        </div>
                                                    </div>
                                                </div>

                                            @elseif(strtoupper(preg_replace('/\s+/', '', $question->question_type->name)) == 'TEXTAREA')
                                                <div class="col-md-12">
                                                    <div class="form-group" style=" margin-bottom: 0px">
                                                        <label for="usr"><b>{{$questionNumber}}.</b>&nbsp;{{$question->question}}
                                                            @if($question->mandatory == 1)
                                                                <span style="color:red">*</span>
                                                            @endif
                                                        </label>
                                                        <textarea name="textarea_{{$question->id}}" class="form-control" id="textarea_{{$question->id}}" {{($question->mandatory == 1)? 'required':''}}>{{$question->reply}}</textarea>
                                                    </div>
                                                </div>
                                            @elseif(strtoupper(preg_replace('/\s+/', '', $question->question_type->name)) == 'DROPDOWN')
                                                <div>
                                                    <div class="col-md-6">
                                                        <p style="margin: 5px 0 5px;"><b>{{$questionNumber}}.</b>&nbsp;{{$question->question}}
                                                            @if($question->mandatory == 1)
                                                                <span style="color:red">*</span>
                                                            @endif
                                                        </p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <select class="form-control" id="optionsDropdown_{{$question->id}}" name="optionsDropdown_{{$question->id}}" {{($question->mandatory == 1)? 'required':''}} onchange="verifyDependencies()">
                                                                <option value="">{{ trans('PublicQuestionnaire.selectOption') }}</option>
                                                                @foreach ($question->question_options as $option)
                                                                    <option  value="{{$option->id}}" {{($question->reply == $option->id) ? 'selected' : ''}}>{{$option->label}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            @elseif(strtoupper(preg_replace('/\s+/', '', $question->question_type->name)) == 'RADIOBUTTONS')

                                                <div>
                                                    <div class="col-md-6 col-sm-6">
                                                        <p style="margin: 5px 0 5px;"><b>{{$questionNumber}}.</b>&nbsp;{{$question->question}}
                                                            @if($question->mandatory == 1)
                                                                <span style="color:red">*</span>
                                                            @endif
                                                        </p>
                                                    </div>
                                                    <div class="col-md-6 col-sm-6 radios-div">
                                                        <div>
                                                            @foreach ($question->question_options as $option)
                                                                @if($question->reply == $option->id)
                                                                    <input class="actionClick" type="radio" name="optionsRadios_{{$question->id}}" id="optionsRadios_{{$option->id}}" value="radio_{{$option->id}}" checked autocomplete="off" {{($question->mandatory == 1)?'required':''}}>
                                                                @else
                                                                    <input class="actionClick" type="radio" name="optionsRadios_{{$question->id}}" id="optionsRadios_{{$option->id}}" value="radio_{{$option->id}}" autocomplete="off" {{($question->mandatory == 1)?'required':''}}>
                                                                @endif

                                                                @if($option->icon != null)
                                                                    <label for="optionsRadios_{{$option->id}}"  id="radio_label_{{$option->id}}" style="margin-top: 5px;margin-bottom: 0px">
                                                                        {{$option->label}}
                                                                    </label>
                                                                <!--
                                                                    <img src="{{URL::action("FilesController@download",[$option->icon->file_id, $option->icon->file_code, 1])}}"  id="questionOptionImage" style="height:50px">
                                                                    -->
                                                                @else
                                                                    <label for="optionsRadios_{{$option->id}}" id="radio_label_{{$option->id}}" style="margin-top: 5px;margin-bottom: 0px">
                                                                        {{$option->label}}
                                                                    </label>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            @elseif(strtoupper(preg_replace('/\s+/', '', $question->question_type->name)) == 'CHECKBOX')

                                                <div>
                                                    <div class="col-md-6 col-sm-6">
                                                        <p style="margin: 5px 0 5px;"><b>{{$questionNumber}}.</b>&nbsp;{{$question->question}}
                                                            @if($question->mandatory == 1)
                                                                <span style="color:red">*</span>
                                                            @endif
                                                        </p>
                                                    </div>

                                                    <div class="col-md-6 col-sm-6 checkbox-div {{($question->mandatory == 1)?'required':''}}">
                                                        <div>

                                                            @foreach ($question->question_options as $option)
                                                                @if(isset($question->reply) && in_array($option->id, $question->reply))
                                                                    <input class="actionClick" type="checkbox" name="optionsCheck_{{$question->id}}[]" id="optionsCheck_{{$option->id}}" value="check_{{$option->id}}" autocomplete="off" checked>
                                                                @else
                                                                    <input class="actionClick" type="checkbox" name="optionsCheck_{{$question->id}}[]" id="optionsCheck_{{$option->id}}" value="check_{{$option->id}}" autocomplete="off">
                                                                @endif
                                                                @if($option->icon != null)
                                                                    <label for="optionsCheck_{{$option->id}}" id="checkbox_label_{{$option->id}}" style="margin-top: 5px;margin-bottom: 0px">
                                                                        {{$option->label}}
                                                                    </label>
                                                                <!--
                                                                    <img src="{{URL::action("FilesController@download",[$option->icon->file_id, $option->icon->file_code, 1])}}"  id="questionOptionImage" style="height:50px">
                                                                    -->
                                                                @else
                                                                    <label for="optionsCheck_{{$option->id}}" id="checkbox_label_{{$option->id}}" style="margin-top: 5px;margin-bottom: 0px">
                                                                        {{$option->label}}
                                                                    </label>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                        @if(end($item->questions) != $question)
                                            <hr>
                                        @endif
                                        <?php $questionNumber++?>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php $i++?>
                @endforeach
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
            </form>

            <div class="row buttons">
                <div class="col-md-12">
                    <button type="button" id="button-back" class="back-btn backBtn"><i class="fa fa-chevron-left" aria-hidden="true"></i> {{ trans('PublicQuestionnaire.back') }}</button>
                    <button type="button" id="button-next" class="next-btn nextBtn">{{ trans('PublicQuestionnaire.next') }} <i class="fa fa-chevron-right" aria-hidden="true"></i></button>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    <script>
        $(".actionClick").click(function(){
            verifyDependencies();
        });

        function verifyDependencies(){
            var type = '';
            var option = '';
            var question = '';
            var dropdown = '';
            var parentQuestionId = '';
            @foreach($questionsDependencies as $dependency)
                    type = '{!! $dependency['type'] !!}';
            option = '{!! $dependency['option_id'] !!}';
            parentQuestionId = '{!! $dependency['question_key'] !!}';
            switch(type){
                case 'RADIOBUTTONS':
                    if($('#optionsRadios_'+option).is(':checked') && ($('#question_'+parentQuestionId).attr('hidden') == false || typeof $('#question_'+parentQuestionId).attr('hidden') == "undefined")) {
                        //Show dependencies
                        @foreach($dependency['dependencies'] as $questions)
                                question = '{!! $questions !!}';
                        $("#question_"+question).removeAttr("hidden");
                        $("#question_"+question).next('hr').removeAttr("hidden");

                        @endforeach
                    }else{
                        //hide dependencies
                        @foreach($dependency['dependencies'] as $questions)
                                question = '{!! $questions !!}';
                        $("#question_"+question).attr("hidden",'true');
                        $("#question_"+question).next('hr').attr("hidden",'true');
                        @endforeach
                    }
                    break;
                case 'CHECKBOX':
                    if($('#optionsCheck_'+option).is(':checked') && ($('#question_'+parentQuestionId).attr('hidden') == false || typeof $('#question_'+parentQuestionId).attr('hidden') == "undefined")){
                        //Show dependencies
                        @foreach($dependency['dependencies'] as $questions)
                                question = '{!! $questions !!}';
                        $("#question_"+question).removeAttr("hidden");
                        $("#question_"+question).next('hr').removeAttr("hidden");
                        @endforeach
                    }else{
                        //hide dependencies
                        @foreach($dependency['dependencies'] as $questions)
                                question = '{!! $questions !!}';
                        $("#question_"+question).attr("hidden",'true');
                        $("#question_"+question).next('hr').attr("hidden",'true');
                        @endforeach
                    }
                    break;
                case 'DROPDOWN':
                    dropdown = '{!! $dependency['question_id'] !!}';
                    var selected_option = $('#optionsDropdown_'+dropdown).val();
                    var dependOptionId = '{!! $dependency['option_id'] !!}';
                    if(selected_option == dependOptionId && ($('#question_'+parentQuestionId).attr('hidden') == false || typeof $('#question_'+parentQuestionId).attr('hidden') == "undefined")) {
                        @foreach($dependency['dependencies'] as $questions)
                                question = '{!! $questions !!}';
                        $("#question_" + question).removeAttr("hidden");
                        $("#question_"+question).next('hr').removeAttr("hidden");
                        @endforeach
                    }else{
                        @foreach($dependency['dependencies'] as $questions)
                                question = '{!! $questions !!}';
                        $("#question_"+question).attr("hidden",'true');
                        $("#question_"+question).next('hr').attr("hidden",'true');
                        @endforeach
                    }
                    break;
            }
            @endforeach
        }


        $(document).ready(function () {
            var allDivs = $('.setup-content'),
                    navListItems = $('div.setup-panel a'),
                    allWells = $('.setup-content'),
                    nextBtn = $('.nextBtn'),
                    backBtn = $('.backBtn'),
                    formGroup = $('.form-group'),
                    pagesCount = allDivs.length;
            allWells.hide();

            formGroup.click(function (e) {
                var $item = $(this);
                if ($item.hasClass('has-error')) {
                    $item.removeClass('has-error');
                }
            });

            navListItems.click(function (e) {
                e.preventDefault();
                var $target = $($(this).attr('href')),
                        $item = $(this);

                if (!$item.hasClass('disabled')) {
                    navListItems.removeClass('btn-circle-active').addClass('btn-circle-disabled');
                    $item.addClass('btn-circle-active');
                    $item.removeClass('btn-circle-disabled');
                    allWells.hide();
                    $target.show();

                    setTimeout(function () {
                        $(window).scrollTop(0);
                    }, 25);

                    var currentId = parseInt($target.attr('id').replace('step-', ''));
                    if (currentId == 1) {
                        backBtn.hide();
                    } else {
                        backBtn.attr('href', '#step-' + (currentId - 1));
                        backBtn.show();
                    }

                    if (currentId == pagesCount) {
                        if(pagesCount > 4){
                            nextBtn.html('<b>{{ trans('PublicQuestionnaire.submit') }}</b>');
                        }else{
                            nextBtn.html('<b>{{ trans('PublicQuestionnaire.send') }}</b>');
                        }
                    } else {
                        nextBtn.attr('form', '');
                        nextBtn.attr('type', '');
                        nextBtn.html('<b>{{ trans('PublicQuestionnaire.next') }}</b> <i class="fa fa-arrow-right">');
                    }
                }
            });

            //Last step
            backBtn.click(function (e) {
                var $target = $($(this).attr('href'));

                allDivs.hide();
                $target.show();

                navListItems.removeClass('btn-circle-active').addClass('btn-circle-disabled');
                allWells.hide();
                $target.show();

                setTimeout(function () {
                    $(window).scrollTop(0);
                }, 25);

                var currentId = parseInt($target.attr('id').replace('step-', ''));

                var percentage = Math.floor(((currentId-1)/pagesCount)* 100);
                $('#percentagem').text(percentage+'%');
                $('div.setup-panel a[href="#step-' + (currentId) + '"]').addClass('btn-circle-active').removeClass('btn-circle-disabled').removeClass('disabled').trigger('click');

                if (currentId == 1) {
                    backBtn.hide();
                } else {
                    backBtn.attr('href', '#step-' + (currentId - 1));
                }

                nextBtn.attr('form', '');
                nextBtn.attr('type', '');
                nextBtn.html('<b>{{ trans('PublicQuestionnaire.next') }}</b> <i class="fa fa-arrow-right">');
                checkStepperText();

            });

            //Check all inputs on current visible div
            nextBtn.click(function () {
                var curStep = $(".setup-content:visible"),
                        curStepBtn = $(".setup-content:visible").attr("id"),
                        nextStepWizard = $('div.setup-panel a[href="#' + curStepBtn + '"]').parent().next().children("a"),
                        nextStepBtn = $(".setup-content:visible").next().attr("id"),
                        curInputs = curStep.find("input[type='text']"),
                        curTextAreas = curStep.find("textarea"),
                        curRadios = curStep.find("input[type='radio']"),
                        curCheckBoxes = curStep.find("input[type='checkbox']"),
                        curDropdown = curStep.find("select"),
                        isValid = true;

                $(".form-group").removeClass("has-error");

                // Check text inputs
                for (var i = 0; i < curInputs.length; i++) {
                    if ($('#' + curInputs[i].id).val().length < 1) {
                        var div = $('#' + curInputs[i].id).closest('.parentHidden').prop('hidden');
                        if ($('#' + curInputs[i].id).prop('required') && div == false) {
                            isValid = false;
                            $(curInputs[i]).closest(".form-group").addClass("has-error");
                        }else if ($('#' + curInputs[i].id).prop('required') && div == true) {
                            $('#' + curInputs[i].id).val('');
                        }

                    }
                }

                // Check text area inputs
                for (var i = 0; i < curTextAreas.length; i++) {
                    if ($('#' + curTextAreas[i].id).val().length < 1) {
                        var div = $('#' + curTextAreas[i].id).closest('.parentHidden').prop('hidden');
                        if ($('#' + curTextAreas[i].id).prop('required') && div == false) {
                            isValid = false;
                            $(curTextAreas[i]).closest(".form-group").addClass("has-error");
                        }else if ($('#' + curTextAreas[i].id).prop('required') && div == true) {
                            $('#' + curTextAreas[i].id).val('');
                        }
                    }
                }

                // Check radio inputs
                var radioButtons = [];
                for (var i = 0; i < curRadios.length; i++) {
                    if (radioButtons.indexOf(curRadios[i].name) < 0) {
                        var div = $('#' + curRadios[i].id).closest('.parentHidden').prop('hidden');
                        var required = $('#' + curRadios[i].id).prop('required');
                        if (required && div == false) {
                            var nameRadio = curRadios[i].name;
                            if ($("input[name='" + nameRadio + "']:checked").val() == undefined) {

                                isValid = false;
                                $(curRadios[i]).closest("div").addClass("radios-has-error");
                            }else{
                                $(curRadios[i]).closest("div").removeClass("radios-has-error");
                            }

                            radioButtons.push();
                        }else if (required && div == true) {
                            $('#' + curRadios[i].id).val('radio_0');
                        }
                    }
                }

                // Check check inputs
                var checkButtons = [];
                for (var i = 0; i < curCheckBoxes.length; i++) {
                    if (checkButtons.indexOf(curCheckBoxes[i].name) < 0) {
                        var div = $('#' + curCheckBoxes[i].id).closest('.parentHidden').prop('hidden');
                        var required = $('#' + curCheckBoxes[i].id).closest('div.checkbox-div').hasClass( "required" );

                        if (required && $('#' + curCheckBoxes[i].id).closest('div.checkbox-div :checkbox:checked').length == 0 && div == false) {

                            var nameCheck = curCheckBoxes[i].name;
                            if ($("input[name='" + nameCheck + "']:checked").val() == undefined) {
                                isValid = false;
                                $(curCheckBoxes[i]).closest("div").addClass("checks-has-error");

                            }else{
                                $(curCheckBoxes[i]).closest("div").removeClass("checks-has-error");

                            }

                            checkButtons.push();
                        }else if (required && div == true) {
                            $('#' + curCheckBoxes[i].id).val('check_0');

                        }
                    }
                }
                // Check dropdowns
                for (var i = 0; i < curDropdown.length; i++) {
                    var div = $('#' + curDropdown[i].id).closest('.parentHidden').prop('hidden');
                    if ($('#' + curDropdown[i].id).val().length < 1 && div == false) {
                        if ($('#' + curDropdown[i].id).prop('required')) {
                            isValid = false;
                            $(curDropdown[i]).closest(".form-group").addClass("has-error");
                        }else if ($('#' + curDropdown[i].id).prop('required') && div == true) {
                            $('#' + curDropdown[i].id).val('');
                        }
                    }
                }

                if (isValid) {

                    var currentId = parseInt(curStepBtn.replace('step-', ''));

                    if (currentId == pagesCount) {
                        @if(!$formPublic)
                            storeStep(true);
                        @else
                            $("#formQuestion").submit();
                        @endif
                    } else {
                        @if(!$formPublic)
                            storeStep(false);
                                @endif

                        var $target = $("#" + nextStepBtn),
                                $item = $(this);

                        if (!$item.hasClass('disabled')) {
                            allDivs.hide();
                            $target.show();

                            setTimeout(function () {
                                $(window).scrollTop(0);
                            }, 100);

                            if (nextStepBtn != 'step-1') {
                                backBtn.attr('href', '#' + curStepBtn);
                                backBtn.show();
                            } else {
                                backBtn.hide();
                            }

                            if (currentId == (pagesCount - 1)) {


                                if(pagesCount > 4){
                                    nextBtn.html('<b>{{ trans('PublicQuestionnaire.submit') }}</b>');
                                }else{
                                    nextBtn.html('<b>{{ trans('PublicQuestionnaire.send') }}</b>');
                                }
                            } else {
                                nextBtn.attr('form', '');
                                nextBtn.attr('type', '');
                                nextBtn.html('<b>{{ trans('PublicQuestionnaire.next') }}</b> <i class="fa fa-arrow-right">');
                            }
                            var percentage = Math.floor((currentId/pagesCount)* 100);
                            $('#percentagem').text(percentage+'%');
                            $('div.setup-panel a[href="#' + nextStepBtn + '"]').addClass('btn-circle-active').removeClass('btn-circle-disabled').removeClass('disabled').trigger('click');
                            checkStepperText();
                        }
                        saveInputs(currentId + 1);
                    }
                } else {
                    toastr.options = {
                        "closeButton": true,
                        "debug": false,
                        "newestOnTop": false,
                        "progressBar": false,
                        "positionClass": "toast-top-right",
                        "preventDuplicates": true,
                        "onclick": null,
                        "showDuration": "300",
                        "hideDuration": "1000",
                        "timeOut": "4000",
                        "extendedTimeOut": "1000",
                        "showEasing": "swing",
                        "hideEasing": "linear",
                        "showMethod": "fadeIn",
                        "hideMethod": "fadeOut"
                    };
                    toastr.error('{{ trans('PublicQuestionnaire.fillInAllRequiredFieldsOnForm') }}', '', {timeOut: 3000,positionClass: "toast-bottom-right"});
                }
            });


            //Trigger first content
            $('div.setup-panel a.btn-circle-active').trigger('click');

            $(window).resize(function () {
                checkWidthWindow();
            });
            // UnCheck radio button
            $('[id^="radio_label_"]').click(function () {
                var checked = $(this).children('input').is(":checked");
                if (checked) {
                    var id = $(this).attr('id');
                    setTimeout(function () {
                        $("#" + id).closest('.btn-group').find('label').removeClass('active')
                                .end().find('[type="radio"]').prop('checked', false);


                        var curStepBtn = $(".setup-content:visible").attr("id");
                        var currentId = parseInt(curStepBtn.replace('step-', ''));
                        var allWells = $('.setup-content');


                        for (var i = (currentId + 1); i < allWells.length; i++) {
                            if (!$(".stepwizard-step a[href=#step-" + i + "]").hasClass('disabled')) {
                                $(".stepwizard-step a[href=#step-" + i + "]").addClass('disabled').addClass('btn-circle-disabled');
                            }
                        }
                        storeStep(false);
                        saveInputs(currentId);
                    }, 50);
                }
            });
            @if(!ONE::isAuth())
                initForm();
            @endif
            checkWidthWindow();
            verifyDependencies();
        });

        function removeHistory() {

            @if(ONE::isAuth())
                setCookie('empatia-{{Session::get('user')->user_key}}', "", -1);
            @endif

        }

        function checkWidthWindow() {
            var navListItems = $('div.setup-panel div a').length;
            if (navListItems < 2) {
                $("#stepper_div").hide();
                $("#stepper_number_1").hide();
                return;
            }


            var width = $(this).width();

            if (width < 750) {
                $("#stepper_div").hide();
            } else {
                $("#stepper_div").show();
            }
            checkStepperText();

        }

        function checkStepperText() {
            var width = $(document).width();
            var id = $(".setup-content:visible").attr("id");

            if (width < 750) {
                $("#stepper_number_" + id).show();
            } else {
                $("#stepper_number_" + id).hide();
            }
        }

        function saveInputs(step) {
            var savedValues = getInputs();
            if (step == undefined) {
                step = 1;
            }
            savedValues['step'] = step;

            @if(Session::has('user'))
            setCookie('empatia-{{Session::get('user')->user_key}}', JSON.stringify(savedValues));
            @endif

        }

        function initForm() {
                    @if(Session::has('user'))
            var savedValuesString = getCookie('empatia-{{Session::get('user')->user_key}}');

            if (savedValuesString == null) {
                return;
            }
            var savedValues = JSON.parse(savedValuesString);

            var keys = Object.keys(savedValues);
            for (var i = 0; i < keys.length; i++) {
                var input = keys[i];
                var value = savedValues[input];
                if (input.indexOf('optionsRadios_') > -1) {
                    $('#' + value).click();
                } else if (input.indexOf('text_') > -1) {
                    $('#' + input).val(value);
                }
            }

            //Load actual steep
            var step = savedValues['step'];

            for (var i = 1; i <= step; i++) {
                $("a[href=#step-" + i + "]").removeClass('disabled');
                $("a[href=#step-" + i + "]").removeClass('btn-circle-disabled');
            }
            $("a[href=#step-" + (i - 1) + "]").trigger('click');
            @endif

        }

        function setCookie(key, value) {
            var expires = new Date();
            expires.setTime(expires.getTime() + (value * 24 * 60 * 60 * 1000));
            document.cookie = key + '=' + value + ';expires=' + expires.toUTCString();
        }


        function getCookie(key) {
            var keyValue = document.cookie.match('(^|;) ?' + key + '=([^;]*)(;|$)');
            return keyValue ? keyValue[2] : null;
        }

        function storeStep(complete){

            var data = getInputs();
            data["_token"] = "{{ csrf_token() }}";
            data["questionnaire_key"] = $("#questionnaire_key").val();
            data["formComplete"] = complete;
            $.ajax({
                method: 'POST', // Type of response and matches what we said in the route
                url: '{{action("PublicQController@storeStep")}}', // This is the url we gave in the route
                data: data, // a JSON object to send back
                success: function (response) { // What to do if we succeed

                    if(response == 'success'){
                        $("#formQuestion").submit();
                        window.location.href = '{{URL::action("PublicQController@success")}}';
                    }else if(response == 'false'){
                        toastr.error('{{ trans('PublicQuestionnaire.errorTryingToSaveFormStepTryAgainLater') }}', '', {timeOut: 3000,positionClass: "toast-bottom-right"});
                        location.reload();
                    }else if(response == 'unauthorized'){
                        location.reload();
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                    toastr.error('{{ trans('PublicQuestionnaire.errorTryingToSaveFormStepTryAgainLater') }}', '', {timeOut: 3000,positionClass: "toast-bottom-right"});
                    location.reload();
                }
            });
        }



        function getInputs(){

            var curStep = $(".setup-content:visible"),
                    curInputs = curStep.find("input[type='text']"),
                    curTextAreas = curStep.find("textarea"),
                    curRadios = curStep.find("input[type='radio']"),
                    curCheckboxes = curStep.find("input[type='checkbox']"),
                    curDropdown = curStep.find("select");
            var values = {};

            // Check text inputs
            for (var i = 0; i < curInputs.length; i++) {
                if ($('#' + curInputs[i].id).val().length > 1) {
                    values[curInputs[i].id] = $('#' + curInputs[i].id).val();
                }else{
                    values[curInputs[i].id] = "";
                }
            }
            // Check text area inputs
            for (var i = 0; i < curTextAreas.length; i++) {
                if ($('#' + curTextAreas[i].id).val().length > 1) {
                    values[curTextAreas[i].id] = $('#' + curTextAreas[i].id).val();
                }else{
                    values[curTextAreas[i].id] = "";
                }
            }
            // Check radio inputs
            var radioButtons = [];
            for (var i = 0; i < curRadios.length; i++) {
                if (radioButtons.indexOf(curRadios[i].name) < 0) {
                    var nameRadio = curRadios[i].name;

                    if ($("input[name='" + nameRadio + "']:checked").val() != undefined) {
                        values[nameRadio] = $("input[name='" + nameRadio + "']:checked").val();
                    }else{
                        values[nameRadio] = "";
                    }
                    radioButtons.push();
                }
            }

            // Check checkboxes
            var checkButtons = [];
            for (var i = 0; i < curCheckboxes.length; i++) {
                var nameCheckBox = curCheckboxes[i].name;

                if( values[nameCheckBox] === undefined){
                    values[nameCheckBox] = [];
                    values[nameCheckBox].push('null');
                }
                if (checkButtons.indexOf(curCheckboxes[i].name) < 0) {
                    if ($("input[id='" + curCheckboxes[i].id + "']:checked").val() != undefined) {
                        values[nameCheckBox].push($("input[id='" + curCheckboxes[i].id + "']:checked").val());
                        console.log('values:'+values[nameCheckBox]);
                    }
                    checkButtons.push();
                }
            }

            // Check dropdown
            for (var i = 0; i < curDropdown.length; i++) {
                if ($('#' + curDropdown[i].id).val().length > 1) {
                    values[curDropdown[i].id] = $('#' + curDropdown[i].id).val();
                }
                else{
                    values[curDropdown[i].id] = "";
                }
            }

            return values;
        }

    </script>
@endsection



