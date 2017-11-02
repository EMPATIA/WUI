<section>
    <div class="container news-container">
        <div class="row">
            <div class="col-md-4" id="empaville-map" style="background-image :url('{{asset('images/empaville_map.png')}}'); background-repeat: no-repeat; background-size: auto 100%; background-position: center top; height: 330px">
            </div>
            <div class="col-md-8">
                <div style="">
                    <div  style="">
                        <h1 style="color: #8dc73d;  font-size: 28px; font-size: 1.75em; font-weight: 700; margin-bottom: 15px; margin-top: 10px;  text-transform: uppercase;">EMPAVILLE</h1>

                        <hr style="margin: 10px 0px; color: #cccccc">
                        <div class="row">
                            <div class="col-md-12 empavilleDescription">
                                {{trans("empavilleHome.firstParagraph")}}
                                <br>
                                <br>
                                {{trans("empavilleHome.secondParagraph")}}
                                <ul>
                                    <li>
                                        {{trans("empavilleHome.firstTextInList")}}
                                    </li>
                                    <li>
                                        {{trans("empavilleHome.secondTextInList")}}
                                    </li>
                                    <li>
                                        {{trans("empavilleHome.thirdTextInList")}}
                                    </li>
                                </ul>
                                {{trans("empavilleHome.thirdParagraph")}}
                                <br>

                            </div>
                            <div class="col-md-12 empavilleDescription">
                                <b>{{trans("empavilleHome.tools")}}:</b>
                                <br>
                                {{trans("empavilleHome.qrCodeScanner")}}:
                                [ <a href="https://play.google.com/store/apps/details?id=com.google.zxing.client.android" target="_blank">Android</a> ]
                                [ <a href="https://itunes.apple.com/us/app/qr-code-reader-and-scanner/id388175979?mt=8" target="_blank">Apple</a> ]
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>