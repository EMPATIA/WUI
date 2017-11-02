@extends('public.empavilleSchools._layouts.index')
@section('content')
    <!-- Header -->
    <div class="container registerContent">
            <div class="row pageSectionTitle">
                <div class="col-xs-12 col-sm-12">
                    <h1>{{ trans("defaultAuth.register") }}</h1>
                    <div class="pageSectionTitle-line"></div>
                </div>
            </div>
            <div class="row register-box top-buffer">

                <form action="{{ URL::action('AuthController@verifyRegisterAndLogin') }}" method="POST" onsubmit="register()">

                    <div class="col-md-6">
                        <div class="col-md-12 col-xs-12">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <div class="form-group">
                                <label for="name">{{ trans('defaultAuth.nameSurname') }}:</label>
                                <input id="name" type="text" name="name" class="form-control" placeholder="Name" value="{{ old('name') }}" required autofocus>
                            </div>
                            <div class="form-group">
                                <label for="email">
                                    {{ trans('defaultAuth.email') }}:
                                    <span id="email-verify" class="pull-right"><span></span><i class="fa fa-circle hidden"></i></span>
                                </label>
                                <input id="email" type="text" name="email" class="form-control" placeholder="Email" required value="{{ old('email') }}">
                            </div>
                            <div class="form-group">
                                <label for="password">{{ trans('defaultAuth.password') }}:</label>
                                <input id="password" type="password" name="password" class="form-control" placeholder="{{ trans('defaultAuth.password') }}" required>
                            </div>
                            <div class="form-group">
                                <label for="password_confirmation">{{ trans('defaultAuth.confirm_password') }}:</label>
                                <input id="password_confirmation" type="password" name="password_confirmation" class="form-control" placeholder="{{ trans('defaultAuth.confirm_password') }}">
                            </div>
                            <div class="row">
                                <div class="col-xs-12 register-message">
                                    <p class="registerFinalMessage">
                                    </p>
                                </div>
                                <div class="col-xs-12 text-right register-button-div">
                                    <button type="submit" class="registerSubmitBtn"><span class="glyphicon glyphicon-ok"></span> {{ trans('defaultAuth.register') }}</button>
                                </div>
                            </div>
                            <br>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title">{{trans('defaultAuth.terms_and_conditions')}}</h4>
                                </div>
                                <div class="modal-body terms-conditions-wrapper">
                                    {!! html_entity_decode($useTerms) !!}
                                </div>
                                <div class="modal-footer terms-conditions-footer">

                                    <div class="col-xs-12 acceptTermsConditions">
                                        <p><input type="checkbox" value="1" id="checkboxAcceptTerms" name="checkboxAcceptTerms" required/>&nbsp;<label for="checkboxAcceptTerms"> {{ trans('defaultAuth.accept') }}
                                            {{ trans('defaultAuth.terms_and_conditions') }}</label>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

            </div>
            <div class="bottom-buffer"></div>

    </div>

@endsection

@section('scripts')
    <script>

        function register(){
            $(".btn-register").css('opacity','0.5');
            $(".btn-register").css('pointer-events','none');
        }
        $(document).ready(function() {
            var timeout = 0;
            $("input[name='email']").on("keyup", function(e){
                clearTimeout(timeout);
                timeout = setTimeout(function() {
                    emailVerify = $("#email-verify");
                    emailVerify.find("i").css("color", "#FF0").removeClass("hidden").attr("title","{{ trans("register.verifying_email") }}");
                    emailVerify.find("span").text("");

                    $(e.target).css("border","");
                    if (/^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/.test($(e.target).val())) {
                        $.ajax({
                            method: 'POST', // Type of response and matches what we said in the route
                            url: '{{ action("AuthController@verifyEmailExists") }}', // This is the url we gave in the route
                            data: {email: $(e.target).val(), _token: "{{ csrf_token() }}"}, // a JSON object to send back
                            success: function (response) { // What to do if we succeed

                                if (response.exists == true) {
                                    color = "red";
                                    text = "{{ trans("register.already_registered_email") }}";
                                    $(e.target).css("border","1px solid red");
                                } else {
                                    color = "#0E0";
                                    text = "{{ trans("register.valid_email") }}";
                                }

                                emailVerify = $("#email-verify");
                                emailVerify.find("i").css("color", color).attr("title","");
                                emailVerify.find("span").text(text);
                            },
                            error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                                console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                            }
                        });
                    } else {
                        emailVerify.find("i").css("color","red").attr("title","");
                        emailVerify.find("span").text("{{ trans("register.invalid_email") }}");
                        $(e.target).css("border","1px solid red");
                    }
                }, 1000);
            });
        });
    </script>
@endsection