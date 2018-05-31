@php
        $defaultConfigurations = array(
            'country'   => 'pt',
            'currency'  => 'EUR',
            'language'  => 'en',
            'layout'    => 'default',
        );
@endphp

@section('header_styles')
    <!-- select2 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
@endsection

<style>
    .form-control:disabled{
        color:black;
    }
    
    .select2-container{
        width:100% !important;
    }

    </style>

<div class="container">
    <form role="form" action="{{action('PublicController@storeWizard')}}" method="post" id="formWizard">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <input type="hidden" name="layout" value="{{$defaultConfigurations['layout']}}">
        
        <div class="row box-buffer">

            <div class="col-xs-12 col-md-12 col-md-offset-2 text-center" id="alert">
                <div class="alert alert-danger" role="alert" ID="alert">
                    <strong>{{trans("wizard.please_fill_all_fields")}}</strong>
                </div>
            </div>

            <div class="col-xs-12 col-md-12 col-md-offset-2 text-center" id="step1">

                <div class="text-left" style="margin-top:15px;">
                    <label for="name">{{trans("wizard.name")}}</label>
                    <input type="text" name="nameEntity" class="form-control" id="nameEntity" required/>
                </div>
                <div class="text-left" style="margin-top:15px;">
                    <label for="designation">{{trans("wizard.code")}}</label>
                    <input type="text" name="designation" class="form-control" id="designation" maxlength="15" onkeyup="valid(this)" onblur="valid(this)" required/>
                </div>
                <div class="text-left" style="margin-top:15px;">
                    <label for="name">{{trans("wizard.url")}}</label>
                    <input type="text" name="url" class="form-control" id="url" disabled>
                </div>
                <div class="text-left" style="margin-top:15px;">
                    <label for="noreplyemail">{{trans("wizard.noreply")}}</label>
                    <input type="email" name="noreplyemail" class="form-control" id="noreplyemail" required/>
                </div>

                <div class="text-left" style="margin-top:30px">
                    <button type="button" class="btn btn-flat empatia pull-right" id="nextStep1">
                        {{trans("wizard.next")}}
                    </button>
                </div>

            </div>

            <div class="col-xs-12 col-md-12 col-md-offset-2 text-center" id="step2">

                <div class="text-left" style="margin-top:15px;">
                    <label for="languages">{{trans("wizard.languages")}}</label>
                    <select class="form-control" name="languages" id="languages">
                        @foreach($languages as $language)
                            <option value="{{ $language->id }}" data-code="{{ $language->code }}">
                                {{ $language->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="text-left" style="margin-top:15px;">
                    <label for="country">{{trans("wizard.countries")}}</label>
                    <select class="form-control" name="country" id="countries">
                        @foreach($countries as $country)
                            <option value="{{ $country->id }}">
                                {{ $country->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="text-left" style="margin-top:15px;">
                    <label for="currency">{{trans("wizard.currencies")}}</label>
                    <select class="form-control" name="currency" id="currencies">
                        @foreach($currencies as $currency)
                            <option value="{{ $currency->id }}">
                                {{ $currency->currency }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="text-left" style="margin-top:15px;">
                    <label for="timezone">{{trans("wizard.timezones")}}</label>
                    <select class="form-control" name="timezone" id="timezones">
                        @foreach($timezones as $timezone)
                            <option value="{{ $timezone->id }}">
                                {{ $timezone->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="text-left" style="margin-top:30px">
                    <button type="button" class="btn btn-flat empatia pull-left" id="previousStep2">
                        {{trans("wizard.previous")}}
                    </button>
                    <button type="button" class="btn btn-flat empatia pull-right" id="nextStep2">
                        {{trans("wizard.next")}}
                    </button>
                </div>

            </div>

            <div class="col-xs-12 col-md-12 col-md-offset-2 text-center" id="step3">

                <div class="text-left" style="margin-top:15px;">
                    <label for="name">{{trans("wizard.name")}}</label>
                    <input type="text" name="name" class="form-control" id="name" required/>
                </div>
                <div class="text-left" style="margin-top:15px;">
                    <label for="surname">{{trans("wizard.surname")}}</label>
                    <input type="text" name="surname" class="form-control" id="surname" required/>
                </div>
                <div class="text-left" style="margin-top:15px;">
                    <label for="email">{{trans("wizard.email")}}</label>
                    <input type="email" name="email" class="form-control" id="email" required/>
                </div>
                <div class="text-left" style="margin-top:15px;">
                    <label for="password">{{trans("wizard.password")}}</label>
                    <input type="password" name="password" class="form-control" id="password" required/>
                </div>
                <div class="text-left" style="margin-top:15px;">
                    <label for="confirmPassword">{{trans("wizard.confirmPassword")}}</label>
                    <input type="password" name="confirmPassword" class="form-control" id="confirmPassword" required/>
                    <span id='message'></span>
                </div>
        
                <div class="text-left" style="margin-top:30px">
                    <button type="button" class="btn btn-flat empatia pull-left" id="previousStep3">
                        {{trans("wizard.previous")}}
                    </button>
                    <button type="submit" class="btn btn-flat empatia pull-right">
                        {{trans("wizard.save")}}
                    </button>
                </div>
                    
            </div>

        </div>
    </form>
</div>

<script>

    $( document ).ready(function() {
        $("#languages").select2();
        $("#countries").select2();
        $("#currencies").select2();
        $("#timezones").select2();
        var url = "{{$url}}";
        var code = $("#designation").val();
        console.log(url);
        console.log(code);
        $("#url").val(url+code); 
    });

    $("#alert").hide();
    $("#step2").hide();
    $("#step3").hide();

    function toggleDiv(hide,show) {
        $(hide).hide();
        $(show).show();
    }

    $("#nextStep1").click(function(){
        if($("#nameEntity").val()=="" || $("#designation").val()==""){
            $("#alert").show();
        }
        else{
            $("#alert").hide();            
            toggleDiv("#step1","#step2");
        }
    });

    $("#previousStep2").click(function(){
        $("#alert").hide(); 
        toggleDiv("#step2","#step1");
    });

    $("#nextStep2").click(function(){           
        toggleDiv("#step2","#step3");
    });

    $("#previousStep3").click(function(){
        $("#alert").hide(); 
        toggleDiv("#step3","#step2");
    });

    $('#password, #confirmPassword').on('keyup', function () {
        if ($('#password').val() == $('#confirmPassword').val()) {
            $('#message').html('Matching').css('color', 'green');
        } else 
            $('#message').html('Not Matching').css('color', 'red');
        
    });

    function valid(f) {
        !(/^[A-z&#209;&#241;0-9-\-]*$/i).test(f.value)?f.value = f.value.replace(/[^A-z&#209;&#241;0-9-\-]/ig,''):null;
        var url = "{{$url}}";
        var code = $("#designation").val();
        $("#url").val(url+code);
    } 

    function myFunction() {
    }

    var timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
    $("#timezones option:contains('" + timezone + "')").prop('selected', true);
    
    var userLang = navigator.language || navigator.userLanguage; 
    var language = userLang.split("-");
    $("#languages option[data-code='" + language[0] + "']").prop('selected', true);


</script>