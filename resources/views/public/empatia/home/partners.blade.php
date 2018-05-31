<!-- Partners -->
<section>
    <div class="partnersHidden"></div>
    <div id="partners" class="hidden-xs my-partners">
        <div class="container partners_container">
                <div class="row menus-row" style="margin-bottom:45px">
                    <div class="menus-line col-sm-6 col-sm-offset-3 partners-menu"><i class="fa fa-cogs" aria-hidden="true"></i> {{trans('home.partners')}} </div>
                </div>
            <div class="row">
                <div style="width: 14.2%;" class="col-xs-1">
                    <div class="partner_container" onClick="window.open('http://www.ces.uc.pt', '_blank');">
                        <img title="CES" alt="CES logo" src="{{asset('images/empatia/logo_CES-2.jpg')}}" class="partner_img">
                    </div>
                </div>
                <div style="width: 14.2%;" class="col-xs-1">
                    <div class="partner_container" onClick="window.open('https://www.onesource.pt/', '_blank');">
                        <img title="OneSource" alt="OneSource logo" src="{{asset('images/empatia/logo_onesource.png')}}" class="partner_img" style="transition-delay: 0.1s">
                    </div>
                </div>
                <div style="width: 14.2%;" class="col-xs-12 col-sm-6 col-md-3 col-lg-1">
                    <div class="partner_container" onClick="window.open('https://www.d21.me/', '_blank');">
                        <img title="D21" alt="D21 logo" src="{{asset('images/empatia/logo_d21.png')}}" class="partner_img" style="transition-delay: 0.2s">
                    </div>
                </div>
                <div style="width: 14.2%;" class="col-xs-12 col-sm-6 col-md-3 col-lg-1">
                    <div class="partner_container" onClick="window.open('http://www.brunel.ac.uk/', '_blank');">
                        <img title="UBRUN" alt="UBRUN logo" src="{{asset('images/empatia/logo_brunel.jpg')}}" class="partner_img" style="transition-delay: 0.3s">
                    </div>
                </div>
                <div style="width: 14.2%;" class="col-xs-12 col-sm-6 col-md-3 col-lg-1">
                    <div class="partner_container" onClick="window.open('http://www.unimi.it/', '_blank');">
                        <img title="UNIMI" alt="UNIMI logo" src="{{asset('images/empatia/logo_universitaInterno.png')}}" class="partner_img" style="transition-delay: 0.4s">
                    </div>
                </div>
                <div style="width: 14.2%;" class="col-xs-12 col-sm-6 col-md-3 col-lg-1">
                    <div class="partner_container" onClick="window.open('http://www.zebralog.de/', '_blank');">
                        <img title="Zebralog" alt="Zebralog logo" src="{{asset('images/empatia/logo_zebralog.png')}}" class="partner_img" style="transition-delay: 0.5s">
                    </div>
                </div>
                <div style="width: 14.2%;" class="col-xs-12 col-sm-6 col-md-3 col-lg-1">
                    <div class="partner_container" onClick="window.open('http://www.in-loco.pt/', '_blank');">
                        <img title="In Loco" alt="In Loco logo" src="{{asset('images/empatia/logo_loco.png')}}" class="partner_img" style="transition-delay: 0.6s">
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Not working with Dot Dot Dot

<script>
    /* JavaScript for Sticky Headers  */
    $(document).ready(function(){
        $("nav").sticky({topSpacing:0});
    });

    /*  Inview -  */
    var inview = new Waypoint.Inview({
        element: $('#partners'),
        enter: function(direction) {
        },
        entered: function(direction) {
            $(".partner_img").css({"opacity": "1", "top": "0px"});
//                    $(".partner_container1").css({"opacity": "1", "top": "0px"});
        },
        exit: function(direction) {
        },
        exited: function(direction) {
        }
    });
</script> -->