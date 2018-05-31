<!-- MAP -->
<style>
    #my-map {
        margin: 0;
        padding: 0;
        height: 400px;
        max-width: none;
    }
    #my-map img {
        max-width: none !important;
    }
    #my-map .gm-style-iw {
        width: 288px !important;
        top: 15px !important;
        left: 0px !important;
        background-color: #fff;
        box-shadow: 0 1px 6px rgba(178, 178, 178, 0.6);
        border: 1px solid rgb(255, 236, 0);
        border-radius: 0;
    }
    #iw-container {
        margin-bottom: 10px;
    }
    #iw-container .iw-title {
        padding: 10px;
        background-color: #ffec00!important;
        color: black;
        margin: 0;
        text-transform: uppercase;
        border-radius: 0;
        font-weight: 600;
        font-size: 13px;
        width: 288px !important;
    }
    #iw-container .iw-content {
        font-size: 13px;
        line-height: 18px;
        font-weight: 400;
        margin-right: 1px;
        padding: 15px 5px 20px 15px;
        max-height: 140px;
        overflow-y: auto;
        overflow-x: hidden;
    }
    .iw-content img {
        float: right;
        margin: 0 5px 5px 10px;
    }
    .iw-subTitle {
        font-size: 16px;
        font-weight: 700;
        padding: 5px 0;
    }
    .iw-bottom-gradient {
        position: absolute;
        width: 326px;
        height: 25px;
        bottom: 10px;
        right: 18px;
        background: linear-gradient(to bottom, rgba(255,255,255,0) 0%, rgba(255,255,255,1) 100%);
        background: -webkit-linear-gradient(top, rgba(255,255,255,0) 0%, rgba(255,255,255,1) 100%);
        background: -moz-linear-gradient(top, rgba(255,255,255,0) 0%, rgba(255,255,255,1) 100%);
        background: -ms-linear-gradient(top, rgba(255,255,255,0) 0%, rgba(255,255,255,1) 100%);
    }
</style>

<div class="">
    <div class="mapContainer" id="my-map">
        {!! Form::oneMapsLocations("mapsLocations", "", $locations, array("markerIcon" => asset('images/wuppertal/pins/construction.png'), "zoom" => 13, "folderIcons" => "/images/wuppertal/pins/", "defaultLocation" => "38.7259582, -9.1276295", "style" => "height:550px;width:100%;"), null, $totalNoMapTopics) !!}
    </div>
</div>