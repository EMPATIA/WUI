<!-- TECHNOLOGY -->
<section>
    @if(!empty($homePageConfigurations) && property_exists($homePageConfigurations,'technology'))
        <div class="container-fluid tech-container whiteBgnd">
            <div class="row" style="max-width: 1280px; margin: auto;">
                @foreach($homePageConfigurations->technology as $technology)
                    <div class="paddingBlock col-xs-6 tech-inner-div">
                        <div class="equalHMWrap eqWrap">
                            <div class="eq">
                                <div class="inside-eq" style="">
                                    <div class="row" style="">
                                        <div class="col-sm-4 " style="font-size:15rem; ">
                                            <i class="fa fa-users" aria-hidden="true" style="color: #999999"></i>
                                        </div>
                                        <div class="col-sm-8" style="">
                                            <h2>{{$technology->title}}</h2>
                                            <p class="pilots-summary">{{isset($technology->description) ? $technology->description : null}}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="row tech-button-row">
                                    <div class="col-sm-12">
                                        <div class="tech-button" style=""><a  href="{{isset($technology->internal_link) ? url($technology->internal_link) : null}}" class="tech-button" style="color: #ffffff;">{{trans('home.knowMore')}}</a></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</section>