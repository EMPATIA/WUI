{{ Html::style('/build/css/stepwizard.css') }}
{{ Html::style('/build/css/flaticon/flaticon.css') }}

@extends('public.empaville._layouts.index')

@section('content')
    <section>
        <div class="container-fluid aboutBanner">
            <div class="row menus-row">
                <div class="menus-line col-sm-6 col-sm-offset-3"><i class="fa fa-check-square-o" style="color: #b3b3b3"></i>{{$titleQuestionnaire}}</div>
            </div>
        </div>

        <div class="container-fluid form">
            <div class="row">
                <div class="col-md-12 whiteBgnd"  style="">
                    <div id="scroller-anchor"></div>
                    <div id="wrapper">
                        <span class='baricon'><p>1</p></span>
                        <span id="bar1" class='progress_bar'></span>
                        <span class='baricon'><p>2</p></span>
                        <span id="bar2" class='progress_bar'></span>
                        <span class='baricon'><p>3</p></span>
                        <span ><p id="percentagem">0%</p></span>
                    </div>
                </div>
            </div>


            <?php $i =1?>
            @foreach ($questionsAll as $item)
                <div class="row form-subtitle">
                    <div class="col-md-12"  style="">
                        <h2>{{$item->title}}</h2>
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
                                <div class="row">
                                    @if(strtoupper(preg_replace('/\s+/', '', $question->question_type->name)) == 'TEXT')
                                        <div class="col-md-6" >
                                            <p style="">{{$question->question}}
                                                @if($question->mandatory == 1)
                                                    <span style="color:red">*</span>
                                                @endif
                                            </p>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <input type="text" name="text_{{$question->id}}" class="form-control" id="text_{{$question->id}}" value="{{$question->reply}}" {{($question->mandatory == 1)? 'required':''}}>
                                            </div>
                                        </div>

                                    @elseif(strtoupper(preg_replace('/\s+/', '', $question->question_type->name)) == 'TEXTAREA')
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="usr">{{$question->question}}
                                                    @if($question->mandatory == 1)
                                                        <span style="color:red">*</span>
                                                    @endif
                                                </label>
                                                <input type="text" name="text_{{$question->id}}" class="form-control" id="text_{{$question->id}}" value="{{$question->reply}}" {{($question->mandatory == 1)? 'required':''}}>
                                            </div>
                                        </div>
                                    @elseif(strtoupper(preg_replace('/\s+/', '', $question->question_type->name)) == 'DROPDOWN')
                                        <div class="col-md-6" >

                                        </div>
                                    @elseif(strtoupper(preg_replace('/\s+/', '', $question->question_type->name)) == 'RADIOBUTTONS')
                                        <div class="col-md-6" >

                                        </div>
                                    @elseif(strtoupper(preg_replace('/\s+/', '', $question->question_type->name)) == 'CHECKBOX')
                                        <div class="col-md-6" >

                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>


                <?php $i++?>
            @endforeach
        </div>
    </section>


    <div class="row">

        <div class="col-md-12">
            <div class="stepwizard" style="margin-left:5%;margin-bottom: 10px;display: none;" id="stepper_div">
                <div class="stepwizard-row setup-panel">
                    @for ($i = 1; $i < 20; $i++)
                        <div class="stepwizard-step"><a href="#step-{{$i}}" type="button" class="btn btn-primary btn-circle {{($i == 1)? '':'disabled btn-circle-disabled'}}">{{$i}}</a></div>
                    @endfor
                </div>
            </div>
            <div class="box" style="border-top-color: #62a351;">
                <div class="box-body">
                    <form role="form" action="{{action('PublicQController@store')}}" method="post" id="formQuestion" name="formQuestion" onsubmit="removeHistory()">
                        <input id="questionnaire_id" type="hidden" value="{{$formKey}}" name="questionnaire_id">
                        <?php $i =1?>
                        @foreach ($questionsAll as $item)
                            <div class="row setup-content" id="step-{{$i}}">
                                <div class="col-md-12">
                                    <div style="border-bottom: 1px solid #f4f4f4; padding-bottom: 15px; margin-top: 15px; margin-bottom: 20px;padding-left: 40px; padding-right: 40px;">
                                        <h2>{{$titleQuestionnaire}}</h2>
                                        @if (count($item->description) > 0)
                                            <h5 style="text-align: justify;">{{$item->description}}</h5>
                                        @endif
                                        <p id="stepper_number_step-{{$i}}" style="display:none">Step {{$i}} of {{count($questionsAll)}}</p>
                                    </div>
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
                                                <select class="form-control" id="optionSelect" name="optionsDropdown_{{$question->id}}" {{($question->mandatory == 1)? 'required':''}}>
                                                @foreach ($question->question_options as $option) {
                                                    <option  value="{{$option->id}}">{{$option->label}}</option>
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
                                                        @foreach ($question->question_options as $option)

                                                            @if($question->reply == $option->id)
                                                                <label class="btn btn-default active" id="radio_label_{{$option->id}}" title="{{$option->label}}">
                                                                <input type="radio" name="optionsRadios_{{$question->id}}" id="radio_{{$option->id}}" value="radio_{{$option->id}}" checked autocomplete="off" {{($question->mandatory == 1)?'required':''}}>
                                                            @else
                                                                <label class="btn btn-default" id="radio_label_{{$option->id}}" title="{{$option->label}}">
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
                            <?php $i++?>
                        @endforeach

                        {{--
                            {!!$questions!!}
                        --}}

                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    </form>
                </div>
                <div class="box-footer">
                    <a class="btn btn-flat pull-left backBtn" id="button-back" style="display: none; background-color: #62a351; color:white"><i
                                class="fa fa-arrow-left"></i> <b>{{trans('PublicQuestionnaire.back')}}</b></a>

                    <a class="btn btn-flat pull-right nextBtn" id="button-next" style="background-color: #62a351; color:white"><b>{{trans('PublicQuestionnaire.next')}}</b> <i
                                class="fa fa-arrow-right"></i></a>
                </div>
                <!-- /.box-body -->
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>

        $(document).ready(function () {

            var allDivs = $('.setup-content'),
                    navListItems = $('div.setup-panel div a'),
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
                    navListItems.removeClass('btn-primary').addClass('btn-default');
                    $item.addClass('btn-primary');
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
                        nextBtn.attr('form', 'formQuestion');
                        nextBtn.attr('type', 'submit');
                        if(pagesCount > 4){
                            nextBtn.html('<b>Submit</b>');
                        }else{
                            nextBtn.html('<b>Enviar</b>');
                        }
                    } else {
                        nextBtn.attr('form', '');
                        nextBtn.attr('type', '');
                        nextBtn.html('<b>Next</b> <i class="fa fa-arrow-right">');
                    }
                }
            });

            //Last step
            backBtn.click(function (e) {
                var $target = $($(this).attr('href'));

                allDivs.hide();
                $target.show();

                navListItems.removeClass('btn-primary').addClass('btn-default');
                allWells.hide();
                $target.show();

                setTimeout(function () {
                    $(window).scrollTop(0);
                }, 25);

                var currentId = parseInt($target.attr('id').replace('step-', ''));
                $("a[href=#step-" + (currentId) + "]").addClass('btn-primary');

                if (backBtn.attr('href') == '#step-1') {
                    backBtn.hide();
                } else {
                    backBtn.attr('href', '#step-' + (currentId - 1));
                }

                nextBtn.attr('form', '');
                nextBtn.attr('type', '');
                nextBtn.html('<b>Next</b> <i class="fa fa-arrow-right">');
                checkStepperText();

            });

            //Check all inputs on current visible div
            nextBtn.click(function () {
                var curStep = $(".setup-content:visible"),
                        curStepBtn = $(".setup-content:visible").attr("id"),
                        nextStepWizard = $('div.setup-panel div a[href="#' + curStepBtn + '"]').parent().next().children("a"),
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
                        if ($('#' + curInputs[i].id).prop('required')) {
                            isValid = false;
                            $(curInputs[i]).closest(".form-group").addClass("has-error");
                        }
                    }
                }

                // Check text area inputs
                for (var i = 0; i < curTextAreas.length; i++) {
                    if ($('#' + curTextAreas[i].id).val().length < 1) {
                        if ($('#' + curTextAreas[i].id).prop('required')) {
                            isValid = false;
                            $(curTextAreas[i]).closest(".form-group").addClass("has-error");
                        }
                    }
                }

                // Check radio inputs
                var radioButtons = [];
                for (var i = 0; i < curRadios.length; i++) {
                    if (radioButtons.indexOf(curRadios[i].name) < 0) {
                        if ($('#' + curRadios[i].id).prop('required')) {
                            var nameRadio = curRadios[i].name;
                            if ($("input[name='" + nameRadio + "']:checked").val() == undefined) {

                                isValid = false;
                                $(curRadios[i]).closest(".form-group").addClass("has-error");
                            }

                            radioButtons.push();
                        }
                    }
                }

                // Check check inputs
                var checkButtons = [];
                for (var i = 0; i < curCheckBoxes.length; i++) {
                    if (checkButtons.indexOf(curCheckBoxes[i].name) < 0) {
                        if ($('#' + curCheckBoxes[i].id).prop('required')) {
                            var nameCheck = curCheckBoxes[i].name;
                            if ($("input[name='" + nameCheck + "']:checked").val() == undefined) {
                                isValid = false;
                                $(curCheckBoxes[i]).closest(".form-group").addClass("has-error");
                            }

                            checkButtons.push();
                        }
                    }
                }
                // Check dropdowns
                for (var i = 0; i < curDropdown.length; i++) {
                    if ($('#' + curDropdown[i].id).val().length < 1) {
                        if ($('#' + curDropdown[i].id).prop('required')) {
                            isValid = false;
                            $(curDropdown[i]).closest(".form-group").addClass("has-error");
                        }
                    }
                }

                if (isValid) {
                    var currentId = parseInt(curStepBtn.replace('step-', ''));

                    if (currentId == pagesCount) {
                        $("#formQuestion").submit();
                    } else {
                        var $target = $("#" + nextStepBtn),
                                $item = $(this);

                        if (!$item.hasClass('disabled')) {
                            allDivs.hide();
                            $target.show();

                            setTimeout(function () {
                                $(window).scrollTop(0);
                            }, 25);

                            if (nextStepBtn != 'step-1') {
                                backBtn.attr('href', '#' + curStepBtn);
                                backBtn.show();
                            } else {
                                backBtn.hide();
                            }

                            if (currentId == (pagesCount - 1)) {
                                nextBtn.attr('form', 'formQuestion');
                                nextBtn.attr('type', 'submit');
                                if(pagesCount > 4){
                                    nextBtn.html('<b>Submit</b>');
                                }else{
                                    nextBtn.html('<b>Enviar</b>');
                                }
                            } else {
                                nextBtn.attr('form', '');
                                nextBtn.attr('type', '');
                                nextBtn.html('<b>Next</b> <i class="fa fa-arrow-right">');
                            }

                            nextStepWizard.removeClass('disabled').removeClass('btn-circle-disabled').trigger('click');
                            checkStepperText();
                        }
                        storeStep();
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
                    toastr.error('Please fill in all required fields on the form.');
                }
            });


            //Trigger first content
            $('div.setup-panel div a.btn-primary').trigger('click');

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
                        storeStep();
                        saveInputs(currentId);
                    }, 50);
                }
            });
            @if(!ONE::isAuth())
                initForm();
            @endif
            checkWidthWindow();
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

        function storeStep(){
            var data = getInputs();
            data["_token"] = "{{ csrf_token() }}";
            data["questionnaire_id"] = $("#questionnaire_id").val();

            $.ajax({
                method: 'POST', // Type of response and matches what we said in the route
                url: '{{action("PublicQController@storeStep")}}', // This is the url we gave in the route
                data: data, // a JSON object to send back
                success: function (response) { // What to do if we succeed
                    console.log(response);
                },
                error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                }
            });
        }



        function getInputs(){
            var curInputs = $("input[type='text']"),
                    curRadios = $("input[type='radio']"),
                    curTextAreas = $("textarea"),
                    curCheckboxes = $("input[type='checkbox']"),
                    curDropdown = $("select");

            var values = {};

            // Check text inputs
            for (var i = 0; i < curInputs.length; i++) {
                if ($('#' + curInputs[i].id).val().length > 1) {
                    values[curInputs[i].id] = $('#' + curInputs[i].id).val();
                }
            }
            // Check text area inputs
            for (var i = 0; i < curTextAreas.length; i++) {
                if ($('#' + curTextAreas[i].id).val().length > 1) {
                    values[curTextAreas[i].id] = $('#' + curTextAreas[i].id).val();
                }
            }
            // Check radio inputs
            var radioButtons = [];
            for (var i = 0; i < curRadios.length; i++) {
                if (radioButtons.indexOf(curRadios[i].name) < 0) {
                    var nameRadio = curRadios[i].name;

                    if ($("input[name='" + nameRadio + "']:checked").val() != undefined) {
                        values[nameRadio] = $("input[name='" + nameRadio + "']:checked").attr('id');
                    }
                    radioButtons.push();
                }
            }

            // Check checkboxes
            var checkButtons = [];
            for (var i = 0; i < curCheckboxes.length; i++) {
                if (checkButtons.indexOf(curCheckboxes[i].name) < 0) {
                    var nameCheckBox = curCheckboxes[i].name;
                    if ($("input[name='" + nameCheckBox + "']:checked").val() != undefined) {
                        values[nameCheckBox] = $("input[name='" + nameCheckBox + "']:checked").attr('id');
                    }
                    checkButtons.push();
                }
            }

            // Check dropdown
            for (var i = 0; i < curDropdown.length; i++) {
                if ($('#' + curDropdown[i].id).val().length > 1) {
                    values[curDropdown[i].id] = $('#' + curDropdown[i].id).val();
                }
            }

            return values;
        }

    </script>
@endsection



