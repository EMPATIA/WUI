<div id="myCarousel" class="carousel slide" data-ride="carousel" data-interval="5000">
    <!-- Indicators -->
    <ol class="carousel-indicators">
        <li data-target="#myCarousel" data-slide-to="1" class="active"></li>
        <li data-target="#myCarousel" data-slide-to="2"></li>
        <li data-target="#myCarousel" data-slide-to="3"></li>
    </ol>

    <!-- Wrapper for slides -->
    <div class="carousel-inner" role="listbox">

        <div class="item active">


            <div class="parallax-container-small" data-parallax="scroll" data-position="top" data-bleed="5"
                 data-image-src="{{ asset('/images/background.jpg') }}" data-natural-width="1280"
                 data-natural-height="450">
                <div class="container">
                    <div class="row">
                        {{--<div class="caption container ">

                            <h2 class="banner-title">{{trans('empatiaHome.title_slider_1')}}</h2>

                            --}}{{--<h4>{{trans('empatiaHome.description_slider_1')}}</h4>--}}{{--

                        </div>--}}
                    </div>
                </div>

            </div>

        </div>
        <div class="item">
            <div class="parallax-container-small" data-parallax="scroll" data-position="top" data-bleed="5"
                 data-image-src="{{ asset('/images/background.jpg') }}" data-natural-width="1280"
                 data-natural-height="450">

                <div class="container">
                    <div class="row">
                        {{--<div class="caption container-fluid ">

                            <h2 class="banner-title">{{trans('empatiaHome.title_slider_2')}}</h2>

                            --}}{{--<h4>{{trans('empatiaHome.description_slider_2')}}</h4>--}}{{--
                        </div>--}}
                    </div>
                </div>
            </div>
        </div>

        <div class="item">
            <div class="parallax-container-small" data-parallax="scroll" data-position="top" data-bleed="5"
                 data-image-src="{{ asset('/images/background.jpg') }}" data-natural-width="1280"
                 data-natural-height="450">
                <div class="container">
                    <div class="row">
                        {{-- <div class="caption container-fluid ">

                             <h2 class="banner-title">{{trans('empatiaHome.title_slider_3')}}</h2>

                             --}}{{--<h4>{{trans('empatiaHome.description_slider_3')}}</h4>--}}{{--
                         </div>--}}
                    </div>
                </div>
            </div>
        </div>
        <!-- Left and right controls -->
        <a class="left carousel-control" href="#myCarousel" role="button" data-slide="prev">
            <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
        </a>
        <a class="right carousel-control" href="#myCarousel" role="button" data-slide="next">
            <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
        </a>
    </div>


</div>
