<!-- NOTIFICATION -->
@if(isset($stream))
<header>
    <div class="container-fluid StreamBanner">
        <div class="row StreamBanner-row">

                <div class="col-sm-8 StreamBanner-col-left">
                    <div style=''><h2 id="stream-titulo2">{{$stream->title}}</h2></div>
                    <div style=''><h1 id="stream-titulo1">{{trans('home.followTheLiveStream')}}</h1></div>
                </div>
                <div class="col-sm-4 StreamBanner-col-right">
                    <div><a href="./" style='font-size: 1.5rem; font-weight: bold; padding: 20px 20px; background-color: #FFFFFF; color: #8cc53f; text-transform: uppercase' class='pull-right'>{{trans('home.watchHere')}} ({{trans('home.availableSoon')}})</a></div>
                </div>
                <div class="col-sm-8 StreamBanner-col-left">
                    <div style=''><h1 id="stream-titulo1">{{trans('home.noLiveStreamAvailable')}}</h1></div>
                </div>

        </div>
    </div>
</header>
@endif