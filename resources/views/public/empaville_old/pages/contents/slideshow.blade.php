{{--SLIDESHOW--}}
<div id="side-carousel" class="carousel slide container-fluid"
     data-ride="carousel"
     style="height: 400px; background-color: #f4f4f4; padding: 10px;">
    <div class="carousel-inner" role="listbox"
         style="width: 100%; height: 100%;">
        @for($i = 0; $i < sizeof($slideshow); $i++)
            <div class="item {{ $i == 0 ? ' active' : '' }}"
                 style="width: 100%; height: 100%;">
                <a title="{{ $slideshow[$i]->name }}"
                   class="fancybox" rel="group"
                   href="{{action('FilesController@download', ['id'=>$slideshow[$i]->id ,'code'=>$slideshow[$i]->code])}}">
                    <div style="width: 100%; height: 100%; background: url('{{URL::action('FilesController@download',[ $slideshow[$i]->id,  $slideshow[$i]->code, 1])}}') no-repeat center center; background-size: cover; "></div>
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