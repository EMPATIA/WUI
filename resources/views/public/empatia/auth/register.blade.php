@extends('public.empatia._layouts.index')
@section('header_styles')
    <link rel="stylesheet" href="{{asset('css/empatia/auth-register.css')}}">
@endsection
@section('content')

    <!-- Header -->
    <div class="container register-container">
        <div class="row">
            <div class="col-md-offset-2 col-md-8 col-xs-12 col-sm-12 text-center">
                <div class="page-title">{{ trans("empatiaAuth.register") }}</div>
            </div>
        </div>
        <div class="row register-box top-buffer">
            <form action="{{ URL::action('AuthController@verifyRegisterAndLogin') }}" method="POST" onsubmit="register()">
                <div class="col-md-offset-2 col-md-8 col-xs-12">
                    <div class="row">
                        <div class="col-md-4 col-xs-12 hidden-sm hidden-xs">
                            <div class="text-center">
                                <div class="user-img">
                                    <i class="fa fa-user"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8 col-xs-12">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <div class="form-group">
                                <label for="name">{{ trans('empatiaAuth.nameSurname') }}:</label>
                                <input id="name" type="text" name="name" class="form-control" placeholder="Name" value="{{ old('name') }}" required autofocus>
                            </div>
                            <div class="form-group">
                                <label for="email">
                                    {{ trans('empatiaAuth.email') }}:
                                    <span id="email-verify" class="pull-right"><span></span><i class="fa fa-circle hidden"></i></span>
                                </label>
                                <input id="email" type="text" name="email" class="form-control" placeholder="Email" required value="{{ old('email') }}">
                            </div>
                            <div class="form-group">
                                <label for="password">{{ trans('empatiaAuth.password') }}:</label>
                                <input id="password" type="password" name="password" class="form-control" placeholder="{{ trans('empatiaAuth.password') }}" required>
                            </div>
                            <div class="form-group">
                                <label for="password_confirmation">{{ trans('empatiaAuth.confirm_password') }}:</label>
                                <input id="password_confirmation" type="password" name="password_confirmation" class="form-control" placeholder="{{ trans('empatiaAuth.confirm_password') }}">
                            </div>
                            <div class="register-message">
                                <div class="acceptTermsConditions">
                                    <input type="checkbox" value="1" id="checkboxAcceptTerms" name="checkboxAcceptTerms" required/>&nbsp;<label for="checkboxAcceptTerms"> {{ trans('empatiaAuth.accept') }}
                                        <a data-toggle="modal" href="#terms_and_conditions" class="u-style">{{ trans('empatiaAuth.terms_and_conditions') }}</a></label>
                                </div>
                            </div>
                            <div class=" register-button-div">
                                <a href="/" class="cancel-btn">{{ trans('empatiaAuth.cancel') }}</a>
                                <button type="submit" class="registerSubmitBtn"><span class="glyphicon glyphicon-ok"></span> {{ trans('empatiaAuth.register') }}</button>
                            </div>
                        </div>
                        <br>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="modal fade" id="terms_and_conditions" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header no-border">
                    <div class="pull-right"><a data-dismiss="modal"><i class="fa fa-times my-custom-fa-close"></i></a></div>
                    <h3 class="modal-title terms-conditions-modal-title">{{trans('empatiaAuth.terms_and_conditions')}}</h3>
                </div>
                <div class="modal-body terms-conditions-wrapper">
                    {!! html_entity_decode(ONE::getSiteEthic('use_terms')) !!}
                </div>
            </div>
        </div>
    </div>
    <div class="bottom-buffer"></div>

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
                    emailVerify.find("i").css("color", "#FF0").removeClass("hidden").attr("title","{{ trans("empatiaAuth.verifying_email") }}");
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
                                    text = "{{ trans("empatiaAuth.already_registered_email") }}";
                                    $(e.target).css("border","1px solid red");
                                } else {
                                    color = "#0E0";
                                    text = "{{ trans("empatiaAuth.valid_email") }}";
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
                        emailVerify.find("span").text("{{ trans("empatiaAuth.invalid_email") }}");
                        $(e.target).css("border","1px solid red");
                    }
                }, 1000);
            });
        })
    </script>
@endsection