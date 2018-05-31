<div class="container-fluid footer">
    <div class="row">
        <div class="col-12">
            <div class="container">
                <div class="row">
                    <div class="col-12 col-sm-6 col-md-3 footer-col">
                        @if(ONE::siteConfigurationExists("html_left_column"))
                            <div class="normal-text">
                                {!! Session::get("SITE-CONFIGURATION.html_left_column") !!}
                            </div>
                        @endif
                    </div>
                    <div class="col-12 col-sm-6 col-md-3 footer-col">

                        @if(ONE::siteConfigurationExists("html_mid_column"))
                            <div class="normal-text">
                                {!! Session::get("SITE-CONFIGURATION.html_mid_column") !!}
                            </div>
                        @endif
                    </div>
                    <div class="col-12 col-sm-6 col-md-3 footer-col">
                        @if(ONE::siteConfigurationExists("html_right_column"))
                            <div class="normal-text">
                                {!! Session::get("SITE-CONFIGURATION.html_right_column") !!}
                            </div>
                        @endif
                    </div>
                    <div class="col-12 col-sm-6 col-md-3 footer-col col-flex">
                        @if(ONE::verifyModuleAccess('orchestrator','newsletter_subscriptions'))
                            <div class="subscribe">
                                <form>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <input type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter email" style="background-color: #3e3e3e !important">
                                            </div>
                                            <div class="subscribe-btn">
                                                <button type="button" class="ml-auto" data-toggle="modal" data-target="#myModal">
                                                    Subscribe<i class="fa fa-paper-plane" aria-hidden="true"></i>
                                                </button>
                                            </div>
                                            <!-- The Modal -->
                                            <div class="modal fade" id="myModal">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <!-- Modal Header -->
                                                        <div class="modal-header">
                                                            <div class="modal-title">Modal Heading</div>
                                                            <button type="button" class="close" data-dismiss="modal">×</button>
                                                        </div>
                                                        <!-- Modal body -->
                                                        <div class="modal-body">
                                                            Etiam tempus, eros id bibendum ullamcorper, lacus ex scelerisque quam, a mattis mi tortor ac neque.
                                                        </div>
                                                        <!-- Modal footer -->
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        @endif
                        <div class="social-btns text-right">
                            @if(ONE::siteConfigurationExists("url_social_facebook"))
                                <a href="{{ ONE::getSiteConfiguration("url_social_facebook") }}" class="social-media-btn">
                                    <i class="fa fa-facebook-official" aria-hidden="true"></i>
                                </a>
                            @endif
                            @if(ONE::siteConfigurationExists("url_social_instagram"))
                                <a href="{{ ONE::getSiteConfiguration("url_social_instagram") }}" class="social-media-btn">
                                    <i class="fa fa-instagram-square" aria-hidden="true"></i>
                                </a>
                            @endif
                            @if(ONE::siteConfigurationExists("url_social_twitter"))
                                <a href="{{ ONE::getSiteConfiguration("url_social_twitter") }}" class="social-media-btn">
                                    <i class="fa fa-twitter-square" aria-hidden="true"></i>
                                </a>
                            @endif
                            @if(ONE::siteConfigurationExists("url_social_youtube"))
                                <a href="{{ ONE::getSiteConfiguration("url_social_youtube") }}" class="social-media-btn">
                                    <i class="fa fa-youtube" aria-hidden="true"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="container-fluid empatia-footer dark-grey-bg">
    <div class="row">
        <div class="col-12">
            <div class="container">
                <div class="row">
                    <div class="col-12 col-sm-6 my-auto">
                        <div class="terms">
                            <a href="{{ action("SiteEthicsController@showPublicSiteEthic",'use_terms') }}">
                                {{ ONE::transSite("terms_of_service") }}
                            </a>
                            <a href="{{ action("SiteEthicsController@showPublicSiteEthic",'privacy_policy') }}">
                                {{ ONE::transSite("privacy_policy") }}
                            </a>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6">
                        <a href="https://www.empatia-project.eu/" class="by-empatia float-right">
                            <span>by</span>
                            <img src="/images/demo/LogoEmpatia-l-02.png">
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if(false)
    <?php
    $layoutSections = App\Http\Controllers\PublicContentManagerController::getSections("footer");
    if ($layoutSections===FALSE)
        abort(404,"no-sections-for-layout");

    $logo1 = collect($layoutSections)->where("code","=","footer_logo1")->first();
    $logo2 = collect($layoutSections)->where("code","=","footer_logo2")->first();
    $footerAddress = collect($layoutSections)->where("code","=","footer_address")->first();
    if (!is_null($footerAddress)) {

        $footerAddress = collect($footerAddress->section_parameters)->first()->value;

    }

    $footerContacts = collect($layoutSections)->where("code","=","footer_contacts")->first();
    if (!is_null($footerContacts)) {

        $footerContacts = collect($footerContacts->section_parameters)->first()->value;

    }
    $footerEmail = collect($layoutSections)->where("code","=","footer_email")->first();
    if (!is_null($footerEmail)) {

        $footerEmail = collect($footerEmail->section_parameters)->first()->value;

    }
    $footerWebsite = collect($layoutSections)->where("code","=","footer_website")->first();
    if (!is_null($footerWebsite)) {

        $footerWebsite = collect($footerWebsite->section_parameters)->first()->value;

    }
    if (!is_null($logo1)) {
        $logo1 = collect($logo1->section_parameters)->first()->value;
        if (!is_null($logo1)){
            $logo1 = json_decode($logo1)[0];
    //        $image = action('FilesController@download',["id"=>$image[0]->id, "code" => $image[0]->code, 1]);

        }
    }

    if (!is_null($logo2)) {
        $logo2 = collect($logo2->section_parameters)->first()->value;
        if (!is_null($logo2)){
            $logo2 = json_decode($logo2)[0];
    //        $image = action('FilesController@download',["id"=>$image[0]->id, "code" => $image[0]->code, 1]);
        }
    }
    ?>

    <div class="container-fluid footer">
        <div class="row">
            <div class="col-12">
                <div class="container">
                    <div class="row">
                        <div class="col-12 col-sm-6 col-md-3 footer-col">
                            {{--<div class="title">--}}
                            {{--Praesent ultricies laoreet ex--}}
                            {{--</div>--}}
                            {{--<div class="normal-text">--}}
                            {{--Suspendisse erat--}}
                            {{--</div>--}}
                            {{--<div class="link">--}}
                            {{--<a href="#">Volutpat purus</a>--}}
                            {{--</div>--}}
                            {{--<div class="link">--}}
                            {{--<a href="#">Volutpat purus</a>--}}
                            {{--</div>--}}
                            {{--<div class="link">--}}
                            {{--<a href="#">Volutpat purus</a>--}}
                            {{--</div>--}}
                            {{--<div class="link">--}}
                            {{--<a href="#">Volutpat purus</a>--}}
                            {{--</div>--}}
                            <div>
                                <p>{{ONE::getSiteConfiguration("left_column")}}</p>
                                <p>{{Session::get('SITE-CONFIGURATION.text_footer_first_column_second_line')}}</p>
                                <p>{{Session::get('SITE-CONFIGURATION.text_footer_first_column_third_line')}}</p>
                                <p>{{Session::get('SITE-CONFIGURATION.text_footer_first_column_forth_line')}}</p>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-3 footer-col">
                            
				<p>
                                {{Session::get('SITE-CONFIGURATION.text_footer_second_column_first_line')}}
                            </p>
                            <p style="margin-top: 10px">
                                {{Session::get('SITE-CONFIGURATION.text_footer_second_column_second_line')}}
                            </p>
                            <p style="margin-top: 10px">
                                {{Session::get('SITE-CONFIGURATION.text_footer_second_column_third_line')}}
                            </p>
                            <p style="margin-top: 10px">
                                {{Session::get('SITE-CONFIGURATION.text_footer_second_column_forth_line')}}
                            </p>
                        </div>
                        <div class="col-12 col-sm-6 col-md-3 footer-col">
                            <p>
                                {{Session::get('SITE-CONFIGURATION.text_footer_third_column_first_line')}}
                            </p>
                            <p style="margin-top: 10px">
                                {{Session::get('SITE-CONFIGURATION.text_footer_third_column_second_line')}}
                            </p>
                            <p style="margin-top: 10px">
                                {{Session::get('SITE-CONFIGURATION.text_footer_third_column_third_line')}}
                            </p>
                            <p style="margin-top: 10px">
                                {{Session::get('SITE-CONFIGURATION.text_footer_third_column_forth_line')}}
                            </p>
                        </div>
                        <div class="col-12 col-sm-6 col-md-3 footer-col col-flex">
                            <div class="subscribe">
                                @if(ONE::verifyModuleAccess('orchestrator','newsletter_subscriptions'))
                                    <form>
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <input type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Inserir email">
                                                </div>
                                                <div class="subscribe-btn">
                                                    <button type="button" class="ml-auto" data-toggle="modal" data-target="#myModal">
                                                        Subscribe<i class="fa fa-paper-plane" aria-hidden="true"></i>
                                                    </button>
                                                </div>
                                                <!-- The Modal -->
                                                <div class="modal fade" id="myModal">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <!-- Modal Header -->
                                                            <div class="modal-header">
                                                                <div class="modal-title">Modal Heading</div>
                                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                            </div>
                                                            <!-- Modal body -->
                                                            <div class="modal-body">
                                                                Etiam tempus, eros id bibendum ullamcorper, lacus ex scelerisque quam, a mattis mi tortor ac neque.
                                                            </div>
                                                            <!-- Modal footer -->
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                @endif
                            </div>
                            <div class="social-btns text-right">
                                <a href="#" class="social-media-btn">
                                    <i class="fa fa-facebook-official" aria-hidden="true"></i>
                                </a>
                                <a href="#" class="social-media-btn">
                                    <i class="fa fa-twitter-square" aria-hidden="true"></i>
                                </a>
                                <a href="#" class="social-media-btn">
                                    <i class="fa fa-youtube" aria-hidden="true"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid empatia-footer dark-grey-bg">
        <div class="row">
            <div class="col-12">
                <div class="container">
                    <div class="row">
                        <div class="col-12 col-sm-6 my-auto">
                            <div class="terms">
                                <a href="#">Service terms</a>
                                <a href="#">Privacy policy</a>
                                <p>empatia-demo</p>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <a href="#" class="by-empatia float-right">
                                <img src="{{action('FilesController@download', ['id'=>$logo2->id,'code'=>$logo2->code, 'h' => 200, 'w' => 200])}}"/>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
