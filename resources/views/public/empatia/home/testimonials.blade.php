<!-- Testimonials -->
<section>
    <div class="container-fluid testimonials-container hidden-xs">
        <div class="container-fluid aboutBanner">
            <div class="row menus-row">
                <div class="menus-line col-sm-6 col-sm-offset-3"><i class="fa fa-comment" style="color: #b3b3b3"></i> {{trans('home.testimonials')}}</div>
            </div>
        </div>

        <div id="myCarousel" class="carousel slide" data-ride="carousel">
            <div class="carousel-inner testimonial-div" role="listbox"  style="">
                <?php $i=0;?>
                @if(!empty($homePageConfigurations) && property_exists($homePageConfigurations,'testimonials'))
                    @foreach($homePageConfigurations->testimonials as $testimonial)
                        <?php $i++;?>
                    <!-- Wrapper for slides -->
                        <div class="item {{ ($i ==1) ? 'active': null }}">
                            <div class="row balao" style="">
                                <h3>{{ (isset($testimonial->testimonial_description) ?$testimonial->testimonial_description : null)}}</h3>
                            </div>
                            <div class="row testimonial-autor" style="margin:auto; max-width: 500px">
                                <div class="col-sm-12">
                                    <h1>{{ (isset($testimonial->testimonial_author) ?$testimonial->testimonial_author : null)}}</h1>
                                    <h2>{{ (isset($testimonial->testimonial_entity) ?$testimonial->testimonial_entity : null)}}</h2>
                                </div>
                            </div>
                        </div>

                    @endforeach
            </div>
            <!-- Left and right controls -->
            <a id="testimonial-controls-left" class="left carousel-control line-arrow square left" href="#myCarousel" role="button" data-slide="prev"></a>
            <a id="testimonial-controls-right" class="right carousel-control line-arrow square right" href="#myCarousel" role="button" data-slide="next"></a>
            @else
                <div class="testimonial-autor">
                    <h1>{{trans('home.noTestimonialsAvailable')}}</h1>
                </div>
            @endif
        </div>
    </div>
</section>