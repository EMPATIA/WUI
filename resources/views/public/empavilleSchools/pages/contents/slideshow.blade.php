@section('header_scripts')

    <!-- Fancybox.js -->

    <!-- Mousewheel plugin (optional) -->
    <script type="text/javascript" src="{{ asset('js/fancybox/lib/jquery.mousewheel-3.0.6.pack.js')}}"></script>
    <!-- Fancybox.js -->
    <script type="text/javascript" src="{{ asset('js/fancybox/source/jquery.fancybox.pack.js')}}"></script>
    <!-- helpers - button, thumbnail and/or media (optional) -->
    <script type="text/javascript" src="{{ asset('js/fancybox/source/helpers/jquery.fancybox-buttons.js')}}"></script>
    <script type="text/javascript" src="{{ asset('js/fancybox/source/helpers/jquery.fancybox-media.js')}}"></script>
    <script type="text/javascript" src="{{ asset('js/fancybox/source/helpers/jquery.fancybox-thumbs.js')}}"></script>
    <!-- // Fancybox.js -->

@endsection

@section('header_styles')

    <!-- Fancybox.js -->
    <link rel="stylesheet" href="{{ asset('css/fancybox/source/jquery.fancybox.css')}}" type="text/css" media="screen" />
    <!-- helpers - button, thumbnail and/or media (optional) -->
    <link rel="stylesheet" href="{{ asset('css/fancybox/source/helpers/jquery.fancybox-buttons.css')}}" type="text/css" media="screen" />

    <link rel="stylesheet" href="{{ asset('css/fancybox/source/helpers/jquery.fancybox-thumbs.css')}}" type="text/css" media="screen" />
    <!-- // Fancybox.js -->

@endsection

<div class="row">
    <div class="col-xs-12 contents-images-slideshow">{{--SLIDESHOW--}}
        <div id="side-carousel" class="carousel slide container-fluid contents-carousel-container" data-ride="carousel">
            <div class="carousel-inner" role="listbox">
                @for($i = 0; $i < sizeof($slideshow); $i++)
                    <div class="item {{ $i == 0 ? ' active' : '' }}">
                        <a title="{{ $slideshow[$i]->name }}"
                           class="fancybox" rel="group"
                           href="{{action('FilesController@download', ['id'=>$slideshow[$i]->id ,'code'=>$slideshow[$i]->code])}}">
                            <div class="carousel-image-div" style="width: 100%; height: 100%; background: url('{{URL::action('FilesController@download',[ $slideshow[$i]->id,  $slideshow[$i]->code, 1])}}') no-repeat center center; background-size: cover; "></div>
                        </a>
                    </div>
                @endfor
            </div>
            @if (!empty($slideshow) && !empty($slideshow[1]))
                <a class="left carousel-control" href="#side-carousel"
                   role="button"
                   data-slide="prev">
                            <span class="glyphicon glyphicon-chevron-left"
                                  aria-hidden="true"></span>
                    <span class="sr-only">Previous</span>
                </a>
                <a class="right carousel-control" href="#side-carousel"
                   role="button"
                   data-slide="next">
                            <span class="glyphicon glyphicon-chevron-right"
                                  aria-hidden="true"></span>
                    <span class="sr-only">Next</span>
                </a>
            @endif
        </div>
    </div>
</div>

<br />
<div class="row">
    <div class="col-xs-12 files">
        <div id="column-title"><i class="fa fa-picture-o"></i> {{ trans('defaultPagesContents.images') }}</div>
        <hr class="contents-box-line">

        @foreach($slideshow as $image)
            <div class="col-lg-3 col-md-6 col-xs-12 thumb">

                <a class="fancybox thumbnail" rel="group"
                   href="{{action('FilesController@download', [$image->id, $image->code, 1] )}}"><img class=""
                                                                                                      src="{{action('FilesController@download', [$image->id, $image->code] )}}"
                                                                                                      style="height:150px;"/></a>&nbsp;&nbsp;
            </div>
        @endforeach
    </div>
</div>


@section('scripts')
    <script>

        // -- Fancybox plugin start --

        $(document).ready(function () {
            $(".fancybox").fancybox({
                "type": "image"
            });
        });
    </script>
@endsection