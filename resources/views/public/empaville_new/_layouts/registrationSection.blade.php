<!-- Registration Section -->

<section>
    @if ( Session::has("SITE-CONFIGURATION.lets_play_idea_key") )
        <a href="{{action('PublicCbsController@show',['cbKey' => Session::get("SITE-CONFIGURATION.lets_play_idea_key") , 'type' => 'idea']) }}">
            <div class="sign-up-container text-center">
                <div class="signUpTxt">
                    {{trans('empavilleHome.lets_play')}}
                </div>
            </div>
        </a>
    @else
        <div class="sign-up-container text-center">
            <div class="signUpTxt">
                {{trans('empavilleHome.lets_play')}}
            </div>
        </div>
    @endif
</section>
