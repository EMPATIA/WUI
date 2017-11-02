<!-- Registration Section -->
@if(!ONE::isAuth())
    <section>
        <a href="{{ action('AuthController@login') }}">
            <div class="sign-up-container text-center">
                <div class="signUpTxt">
                    {{trans('defaultHome.to_participate_sign_up')}}
                </div>
                <div class="sign-up-button-div">
                    {{trans("defaultHome.enter")}}
                </div>
            </div>
        </a>
    </section>
@else
    <section>
        <div class="participate-banner-container text-center">
            <div class="h3">
                <h2 class="">{{trans("defaultHome.middle_page_title")}}</h2>
                <h4 class="">{{trans("defaultHome.middle_page_description")}}</h4>
            </div>
        </div>
    </section>
@endif