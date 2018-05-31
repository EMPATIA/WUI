@extends('public.empaville._layout.index')

@section('content')

    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-user"></i> {{trans("PublicAuth.registration")}}</h3>
        </div>

        <div class="box-body">
            <div class="row" style="margin-top: 20px;">
                <div class="col-md-10" style="float: none;margin: 0 auto;">


                    <div style="float: right;">
                        <a class="btn btn-flat btn-social btn-twitter">
                            <span class="fa fa-twitter"></span> {!!trans("PublicAuth.registerWithTwitter")!!}
                        </a>
                        <a class="btn btn-flat btn-social btn-facebook">
                            <span class="fa fa-facebook"></span> {!!trans("PublicAuth.registerWithFacebook")!!}
                        </a>
                        <a class="btn btn-flat btn-social btn-linkedin">
                            <span class="fa fa-linkedin"></span> {!!trans("PublicAuth.registerWithLinkedin")!!}
                        </a>
                        <a class="btn btn-flat btn-social btn-google">
                            <span class="fa fa-google"></span> {!!trans("PublicAuth.registerWithGoogle")!!}
                        </a>
                    </div>
                    <div style="clear: both"></div>
                    <hr>

                    <div class="form-group "><label for="name">{!!trans("PublicAuth.username")!!}</label><input class="form-control" type="text">
                        <span>{{trans("PublicAuth.usernameInfo")}}</span>
                    </div>
                    <div class="form-group "><label for="name">{!!trans("PublicAuth.password")!!}</label><input class="form-control" type="password"></div>
                    <div class="form-group "><label for="name">{!!trans("PublicAuth.confirmPassword")!!}</label><input class="form-control" type="password"></div>

                    <!--div class="form-group "><label for="name">Name</label><input class="form-control" type="text"></div>
                    <div class="form-group "><label for="name">Last Name</label><input class="form-control" type="text"></div-->
                    <div class="form-group "><label for="name">{!!trans("PublicAuth.email")!!}</label><input class="form-control" type="text"></div>

                    <!--div class="form-group "><label for="name">Street address</label><input class="form-control" type="text"></--div>
                    <div class="form-group "><label for="name">Postal Code</label><input class="form-control" type="text"></div>
                    <div class="form-group "><label for="name">City</label><input class="form-control" type="text"></div>
                    <div class="form-group "><label for="name">Phone</label><input class="form-control" type="text"></div>
                    <div class="form-group "><label for="name">Nationality</label><input class="form-control" type="text"></div-->

                    <hr>
                    <div class="form-group "><label for="name">Privacy policy</label>
                        <div style="background-color: #f4f4f4;padding: 10px;border: 1px solid gainsboro">
                            {!!trans("PublicAuth.privacyDisclaimer")!!}
                        </div>
                    </div>

                    <div class="form-group "><label for="name"></label>
                        <label><input type="hidden"  value="0"><input type="checkbox" name="data[User][agreement]" required="required" value="1" id="UserAgreement">  <strong>{{trans("PublicAuth.iAgreePrivacyDisclaimer")}}</strong></label>
                    </div>

                    <div style="color:red">
                        -------
                        <span><b>{!!trans("PublicAuth.addCaptcha")!!}</b></span>
                        -------
                    </div>
                </div>
                <div class="col-md-12" style="margin-left: 40px;">
                    <div style="margin-bottom: 50px;margin-top: 30px;">
                        <button class="btn btn-flat btn-primary" onclick="location.href='{{route("home")}}'" type="button">
                            {!!trans("PublicAuth.register")!!}
                        </button>
                    </div>

                </div>
            </div>


        </div>
    </div>

@endsection