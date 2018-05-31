<div class="container-fluid whiteBgnd" style="margin-top:100px">
    <div class="container-fluid whiteBgnd pilotPage-contentContainer" id="content-container" style="background-color: #ffffff">
        <div class="row">
            <div class="col-md-12">
                <div class="body-side-content content-fluid">
                    <div class="row" style="margin-top: 30px; margin-bottom: 30px">
                        <div
                            @if(!empty($sideMenu['menu']) && ((isset($pageContent->docs_side) && $pageContent->docs_side)
                                || (isset($contentType['news']) && $contentType['news']) || (isset($contentType['events']) && $contentType['events'])
                                || (isset($pageContent->highlights) && $pageContent->highlights) || (isset($pageContent->slideshow) && $pageContent->slideshow)))
                            id="main-col" class="col-md-12 pilotPage-content"
                            @elseif(!empty($sideMenu['menu']) || ((isset($pageContent->docs_side) && $pageContent->docs_side)
                                || (isset($contentType['news']) && $contentType['news']) || (isset($contentType['events']) && $contentType['events'])
                                || (isset($pageContent->highlights) && $pageContent->highlights) || (isset($pageContent->slideshow) && $pageContent->slideshow)))
                            id="main-col" class="col-md-12 pilotPage-content"
                            @else
                            id="main-col" class="col-md-12 pilotPage-content"
                            @endif
                        >

                            {{--BANNERS--}}
                            @if (!empty($banners[0]))
                                <div id="top-carousel" class="carousel slide container-fluid"
                                     data-ride="carousel"
                                     style="height: 100px; width: 100%; margin-bottom: 20px; padding-left: 0; padding-right: 0;">
                                    <div class="carousel-inner" role="listbox"
                                         style="width: 100%; height: 100%;">

                                        @for ($i = 0; $i < sizeof($banners); $i++)
                                            <div class="item{{ $i == 0 ? ' active' : '' }}"
                                                 style="width: 100%; height: 100%;">
                                                <div style="width: 100%; height: 100%; background: url(' <?= env('DOWNLOAD_API', 'https://empatia-test.onesource.pt:5005/file/download/') . $banners[$i]->id . '/' . $banners[$i]->code . '/1' ?>' ) no-repeat center center;  "></div>
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
                            @endif

                            {{--CONTENT--}}
                            {!! $html !!}

                            {{--FILES BOTTOM--}}
                            @if (isset($pageContent->docs_main) && $pageContent->docs_main && !empty($files))

                                <div style="background-color: #f4f4f4; padding: 10px; margin-top: 50px">
                                    <div id="column-title"><i
                                                class="fa fa-file-o"></i> {{ trans('PublicContent.files') }}
                                    </div>
                                    <hr style="margin: 10px 0px; color: #cccccc">
                                    @foreach($files as $file)
                                        <p><span><a
                                                        href="{{action('FilesController@download', ['id'=>$file->id,'code'=>$file->code])}}"
                                                        title="{{ $file->name }}">
                                                        {!! ONE::getFileIcon($file) !!} {{ $file->name }}</a></span>
                                            <span class="pull-right">{!! round($file->size/1024) !!}
                                                KB</span>
                                        </p>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        @if((isset($pageContent->docs_side) && $pageContent->docs_side)
                            || (isset($contentType['news']) && $contentType['news']) || (isset($contentType['events']) && $contentType['events'])
                            || (isset($pageContent->highlights) && $pageContent->highlights) || (isset($pageContent->slideshow) && $pageContent->slideshow))
                            <div id="side-col" class="col-md-12" style="margin-top: 50px">

                                <div style="background-color: #f4f4f4; padding: 10px;">
                                    @endif

                                    {{--SLIDESHOW--}}
                                    @if (isset($pageContent->slideshow) && $pageContent->slideshow && isset($slideshow) && sizeof($slideshow) > 0)
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
                                                            <div style="width: 100%; height: 100%; background: url(' <?= env('DOWNLOAD_API', 'https://empatia-test.onesource.pt:5005/file/download/') . $slideshow[$i]->id . '/' . $slideshow[$i]->code . '/1' ?>') no-repeat center center; background-size: cover; "></div>
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

                                    @endif

                                    {{--DOCS--}}

                                    @if (isset($pageContent->docs_side) && $pageContent->docs_side && !empty($files))

                                        <div style="background-color: #f4f4f4; padding: 10px;">
                                            <div id="column-title"><i
                                                        class="fa fa-file-o"></i> {{ trans('PublicContent.files') }}
                                            </div>
                                            <hr style="margin: 10px 0px; color: #cccccc">
                                            @foreach($files as $file)
                                                <p><span><a
                                                                href="{{action('FilesController@download', ['id'=>$file->id,'code'=>$file->code])}}"
                                                                title="{{ $file->name }}">
                                                                {!! ONE::getFileIcon($file) !!} {{ $file->name }}</a></span>
                                                    <span class="pull-right">{!! round($file->size/1024) !!}
                                                        KB</span>
                                                </p>
                                            @endforeach
                                        </div>
                                    @endif


                                    {{--NEWS--}}
                                    @if (isset($contentType['news']) && count(count($contentType['news'])) > 0 && !empty($news))
                                        <div class="row">
                                            <div id="column-title" class="col-md-4">
                                                <i class="fa fa-newspaper-o"></i> {{ trans('PublicContent.news') }}
                                                <a id="column-more" class="col-md-2" href='{{ URL::action('ContentsController@showNewsList', $content->key) }}'>
                                                    {{ trans('public.read_all') }}</a></div>
                                        </div>
                                        <hr style="margin: 10px 0px; color: #cccccc">
                                        <ul style="list-style: none; padding-left: 0px">
                                            @foreach($news as $item)
                                                @if (!empty($item->translations[0]->title))
                                                    <li class="news-item" onclick="location.href='{{ URL::action('ContentsController@showNews', $item->id) }}'">
                                                        <div id="news-date">{{  $item->start_date }}</div>
                                                        <div id="news-topic"><a style="cursor: pointer">{{ $item->translations[0]->title }}</a></div>
                                                    </li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    @endif

                                    {{--EVENTS--}}
                                    @if (isset($contentType['events']) && count(count($contentType['events'])) > 0 && !empty($events))
                                        <div class="row">
                                            <div id="column-title" class="col-md-4">
                                                <i class="fa fa-calendar"></i> {{ trans('PublicContent.events') }}
                                                <a id="column-more" class="col-md-2" href="{{ URL::action('ContentsController@showEventsList', $content->key) }}">
                                                    {{ trans('public.see_all') }}</a></div>
                                        </div>
                                        <hr style="margin: 10px 0px; color: #cccccc">
                                        <ul style="list-style: none; padding-left: 0px">
                                            @foreach($events as $item)
                                                @if (!empty($item->translations[0]->title))
                                                    <li class="event-item" onclick="location.href='{{ URL::action('ContentsController@showEvent', $item->id) }}'">
                                                        {{--<div id="event-square">--}}
                                                        {{--<div id="event-month">{{ strtoupper(substr($item->start_date->formatLocalized('%B'), 0, 3)) }}</div>--}}
                                                        {{--<div id="event-day">{{ $item->start_date->day }}</div>--}}
                                                        {{--</div>--}}
                                                        <div id="event-date">{{  $item->start_date }}</div>
                                                        <div style="margin-bottom: 20px;">
                                                            {{--<div id="event-location">{{ $item->translations[0]->location }}</div>--}}
                                                            <div id="event-title"><a style="cursor: pointer">{{ $item->translations[0]->title }}</a></div>
                                                        </div>
                                                        <div style="clear: both"></div>
                                                    </li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    @endif

                                    @if((isset($pageContent->docs_side) && $pageContent->docs_side)
                                        || (isset($contentType['news']) && $contentType['news']) || (isset($contentType['events']) && $contentType['events'])
                                        || (isset($pageContent->highlights) && $pageContent->highlights) || (isset($pageContent->slideshow) && $pageContent->slideshow))
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>