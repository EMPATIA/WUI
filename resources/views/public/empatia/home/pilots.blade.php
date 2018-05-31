<!-- PILOTS -->
<section>
    <div class="container-fluid pilots-container">
        <div class="row menus-row">
            <div class="menus-line col-sm-6 col-sm-offset-3"><span class="fa fa-tasks" style="color: #b3b3b3"></span> {{trans('home.pilots')}}</div>
        </div>
        <div class="row" style="max-width: 1280px; margin: auto;">
        @if(!empty($homePageConfigurations) && property_exists($homePageConfigurations,'pilots'))
            <?php $i=0; ?>
            @foreach($homePageConfigurations->pilots as $key => $pilot)
                {{--@if(count($))--}}
                <div class="col-sm-6 col-md-4">
                    <div class="container-fluid pilots margin-top2">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="pilots-div-img" style="background-image:url('{{(isset($pilot->pilot_image) ? url($pilot->pilot_image) : null)}}')">
                                    <img src='{{ (isset($pilot->pilot_image) ? url($pilot->pilot_image) : null) }}' />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="container-fluid" style="padding: 2em; background-color: #fafafa">
                                    <div class="row"><div class="col-sm-12"><h2>{{(isset($pilot->pilot_country) ? $pilot->pilot_country : null)}}</h2></div></div>
                                    <div class="row"><div class="col-sm-12"><h1>{{(isset($pilot->pilot_city) ? $pilot->pilot_city : null)}}</h1></div></div>
                                    <div class="row"><div class="col-sm-12"><p class="pilots-summary">{{isset($pilot->pilot_description) ? $pilot->pilot_description : null}}</p></div></div>
                                    <div class="row"><div class="col-sm-12">
                                            <div class="pilots-line"></div>
                                        </div>
                                    </div>
                                    <div class="row"><div class="col-sm-12" style=""><h3>{{trans('home.status')}}</h3></div></div>
                                    <div class="row">
                                        <div class="col-sm-12 pilots-status">
                                            <ul style="margin: 0; padding: 0; list-style: none">
                                                <li><p><i class="fa fa-angle-right" style="color: #8cc53f;"></i> {{(isset($pilot->pilot_status) ? $pilot->pilot_status : null)}}</p></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="pilots-button" style=""><a href="{{(isset($pilot->pilot_internal_link) ? url($pilot->pilot_internal_link) : null) }}?type=pilots&key={{$key}}" class="pilots-button" style="color: #8cc53f;">{{trans('home.viewPilotPage')}}</a></div>
                            </div>
                        </div>

                    </div>
                </div>
            <?php $i++; ?>
            @endforeach
            @endif

        </div>
        <!-- OTHER PILOTS -->
        <div class="row" style="max-width: 1280px; margin: auto; margin-top: 30px">

            <!-- PILOT 6 -->
            <div class="col-md-12">
                <div class="container-fluid" style="background-color:white ; box-shadow: 0 0 1em #CCCCCC; overflow: hidden;">
                    <div class="row  otherPilots-btn2" style="">
                        <div class="col-sm-12 otherPilots-btn2-txt" style=""><a href="./" style="color: #ffffff">{{trans('home.becomePartner')}} ({{trans('home.availableSoon')}})</a></div>

                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="pilots-button" style=""><a href="./" style="color: #8cc53f;">{{trans('home.knowMore')}} ({{trans('home.availableSoon')}})</a></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

