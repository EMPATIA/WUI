<footer id="pageFooter">
    <div id="pre-footer" class="container-fluid">
        {{--<div class="col-lg-3 col-md-6 col-sm-6 col-xs-12 margin-top2">
            <div class="row">
                <div class="col-sm-12 menu-titulo"><span class="typcn typcn-flow-children" style="font-size: 4rem;     margin-right: 0.5rem"></span>{{trans('home.siteMap')}}</div>
            </div>
            <div class="row">
                <div class="col-sm-12 menu-titulo-line"></div>
            </div>
            <div class="row sub-menus-row">
                <div class="col-sm-12">
                    @foreach($menus as $menu)
                        <ul>
                            @if(empty($menu['id']) )
                                <li>
                                    <p>{{$menu[0]['title']}}</p>
                                    <ul>
                                        @foreach ($menu as $subMenu)
                                            @if (empty($subMenu['id']))
                                                <li><p>{{$subMenu[0]['title']}}</p>
                                                    <ul>
                                                        @foreach ($subMenu as $subSubMenu)
                                                            @if($subSubMenu['id'] != $subMenu[0]['id'])
                                                                <li><p><a href="{{ONE::getActionMenu($subMenu)}}" >{{$subSubMenu['title']}}</a></p></li>
                                                            @endif
                                                        @endforeach
                                                    </ul>
                                                </li>
                                            @elseif($subMenu['id'] != $menu[0]['id'])
                                                <li><p><a href="{{ONE::getActionMenu($subMenu)}}" >{{$subMenu['title']}}</a> </p></li>
                                            @endif
                                        @endforeach
                                    </ul>

                                </li>
                            @else
                                <li><p><a href="{{ONE::getActionMenu($menu)}}" >{{$menu['title']}}</a></p></li>
                            @endif
                        </ul>
                @endforeach

                <!--<div style="background-color: #5bc0de; height: 100px"></div>-->
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12 margin-top2" style="">
            <div class="row">
                <div class="col-sm-12 menu-titulo"><i class="fa fa-thumbs-up" aria-hidden="true"  ></i>{{trans('home.followUs')}}</div>
            </div>
            <div class="row">
                <div class="col-sm-12 menu-titulo-line"></div></div>
            <div id="footerFacebook" class="row sub-menus-row">
                <div class="col-sm-12">
                    <p><i class="fa fa-facebook-official" aria-hidden="true" style="font-size:1.1em; margin:0; margin-right:1rem; color: #555555"></i> <a href="https://www.facebook.com/empatia2016/" target="_blank" >{{trans('home.likeOurFacebookPage')}}</a></p>
                </div>
            </div>
        </div>
        <div class="clearfix visible-xs"></div>
        <div class="clearfix visible-sm"></div>
        <div class="clearfix visible-md"></div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12 margin-top2"  style="">
            <div class="row">
                <div class="col-sm-12 menu-titulo"><span class="typcn typcn-mail" style="font-size: 4rem; color: #555555"></span>{{trans('home.newsletter')}}</div>
            </div>
            <div class="row">
                <div class="col-sm-12 menu-titulo-line"></div>
            </div>
            <div class="row sub-menus-row">
                <div class="col-sm-12" id="newsletterDiv">
                    <p>{{trans('home.registerToOurNewsletter')}}</p>
                    </br>
                        --}}{{--<div class="email-btn">{{trans('home.availableSoon')}}</div>--}}{{--
                        <input id="emailNewsletter" class="email-btn" type="email" placeholder="{{trans('home.insertEmail')}}" />
                        <button  class="subscribe-btn" onclick="subscribeNL()"><i class="fa fa-paper-plane-o" aria-hidden="true" style=""></i>  {{trans('home.subscribeNewLetter')}}</button>
                </div>
                <div class="col-sm-12" id="newsletterSuccess" style="visibility: hidden">
                    <div class="subscribe-btn-success" style="color: white">{{trans('home.newsletterSuccessMsg')}}</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12 margin-top2" style="">
            <div class="row">
                <div class="col-sm-12 menu-titulo"><i class="fa fa-bolt" aria-hidden="true" style="font-size: 4rem; margin-right: 1rem"></i>{{trans('home.quickContacts')}}</div>
            </div>
            <div class="row">
                <div class="col-sm-12 menu-titulo-line"></div>
            </div>
--}}
        <div class="text-center visible-xs">
            <a href="https://github.com/empatia" target="_blank" class="social-link"><i class="fa fa-git-square fa-4x" aria-hidden="true"></i></a>
            <a href="https://twitter.com/search?q=empatia_project" target="_blank" class="social-link"><i class="fa fa-twitter-square fa-4x" aria-hidden="true"></i></a>
            <a href="https://www.facebook.com/empatia2016" target="_blank" class="social-link"><i class="fa fa-facebook-square fa-4x" aria-hidden="true"></i></a>
        </div>
        <div class="row-eq-height">
            <div class="col-md-6 col-xs-12">
                <h3>{{trans('home.mainProjectContact')}}</h3>
                <p>empatia@empatia-project.eu</p>
                <h3>Centro de Estudos Sociais</h3>
                <p>Colégio de S.Jerónimo</p>
                <p>Largo D. Dinis</p>
                <p>Apartado 3087</p>
                <p>3000-995 Coimbra, Portugal</p>
                {{--<h3>{{trans('home.phone')}}</h3>
                <p>+351 239 855 570</p>
                <h3>Fax</h3>
                <p>+351 239 855 589</p>--}}
            </div>
            <div class="col-md-6 col-xs-12">

                <div class="hidden-xs">
                    <a href="https://github.com/empatia" target="_blank" class="pull-right social-link"><i class="fa fa-git-square fa-4x" aria-hidden="true"></i></a>
                    <a href="https://twitter.com/search?q=empatia_project" target="_blank" class="pull-right social-link"><i class="fa fa-twitter-square fa-4x" aria-hidden="true"></i></a>
                    <a href="https://www.facebook.com/empatia2016" target="_blank" class="pull-right social-link"><i class="fa fa-facebook-square fa-4x" aria-hidden="true"></i></a>
                </div>
                <div class="funding-row">
                    <img src="{{asset('images/empatia/Flag_of_Europe.png')}}" class="funding-img" alt=""/><p style="font-size: 15px;">{{trans('home.fundingFromTheEuropeanUnion')}}</p>
                </div>
            </div>
        </div>
        <div class="site-ethics-div">
            <div class="col-xs-12">
                <a href="{{ action("SiteEthicsController@showPublicSiteEthic",'use_terms') }}" target="_blank">{{trans('empatiaHome.use_terms')}}</a>
            </div>
            <div class="col-xs-12">
                <a href="{{ action("SiteEthicsController@showPublicSiteEthic",'privacy_policy') }}" target="_blank">{{trans('empatiaHome.privacy_policy')}}</a>
            </div>
        </div>
    </div>
    <div class="container-fluid footer">
        <div class="row footer-flex">
            <div class="flex-col">
                <p>2017 &#169; {{trans('home.allRightsReserved')}}</p>
            </div>
            <div class="flex-col col-centered ccLogo">
                <img src="{{asset('images/empatia/by-nc-sa.png')}}" alt=""/>
            </div>
            <div class="flex-col col-right">
                by <img src="{{ asset("images/empatia/logo.png") }}" style="width: auto;">
            </div>
        </div>
    </div>
</footer>

<script>
    function subscribeNL() {
        var email = document.getElementById("emailNewsletter").value;
        var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": false,
            "progressBar": false,
            "positionClass": "toast-bottom-right",
            "preventDuplicates": true,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "4000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };
        if (re.test(email)){
            $.ajax({
                type: 'POST',
                url: "{{action('NewsletterController@register')}}",
                data: {
                    "email":email,
                    _token: "{{ csrf_token()}}"
                },
                success: function(response) {
                    toastr.success('{{trans('home.newsletterSuccessMsg')}}');
                    document.getElementById('newsletterDiv').style.display  = 'none';
                    document.getElementById('newsletterSuccess').style.visibility = 'visible';
                },
                error: function(response) {
                    toastr.error('{{trans('home.newsletterErrorMsg')}}');
                }
            });
        }else{
            toastr.error('{{trans('home.newsletterNoEmailMsg')}}');
        }

    }
</script>