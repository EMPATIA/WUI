{{--BANNERS--}}
<div id="top-carousel" class="carousel slide container-fluid"
     data-ride="carousel"
     style="height: 250px; width: 100%; margin-bottom: 20px; padding-left: 0; padding-right: 0;">
    <div class="carousel-inner" role="listbox"
         style="width: 100%; height: 100%;">

        @for ($i = 0; $i < sizeof($banners); $i++)
            <div class="item{{ $i == 0 ? ' active' : '' }}"
                 style="width:100%;height: 100%;">
                <div style="width:100%;height:100%;background:url('{{URL::action('FilesController@download',[ $banners[$i]->id,  $banners[$i]->code, 0])}}') no-repeat center center;background-size:cover;"></div>
            </div>
        @endfor
    </div>
    @if (sizeof($banners) > 1)
        <ol id="indicators-top-carousel" class="carousel-indicators"
            style="width: auto; left: auto; right: 20px; bottom: 0px;">
            @for ($i = 0; $i < sizeof($banners); $i++)
                <li data-target="#top-carousel" data-slide-to="{{ $i }}"
                    class="{{ $i == 0 ? 'active' : '' }}"></li>
            @endfor
        </ol>
    @endif
</div>
