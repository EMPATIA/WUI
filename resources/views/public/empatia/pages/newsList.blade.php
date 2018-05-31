@extends('public.empatia._layouts.index')

@section('content')

    @if(empty($contentKey))
        <div class="container-fluid padding-top-bottom-35">
            <div class="row menus-row margin-top-15 margin-bottom-35">
                <div class="menus-line col-sm-6 col-sm-offset-3">
                    <span class="fa fa-newspaper-o" style="color: #b3b3b3">&nbsp;</span>
                    News
                </div>
            </div>
        </div>
    @endif

    @if(empty($contentKey))
        <div id="infinite-scroll" class="newsList-container content-fluid"
             style="max-width: 1280px!important;margin:auto;margin-bottom:20px">
            @endif


            <div class="container-fluid news-container">
                <div class="row" style="">
                    <?php $i = 0 ?>
                    @if(empty($contentKey))
                        @if(count($informations) > 0)
                            @foreach($informations as $item)
                                <div class="paddingBlock">
                                    <div class="equalHMWrap eqWrap">
                                        <a href="{{ URL::action('PublicContentsController@show', $item->content_key) }}" class="col-md-12">
                                            <div class="col-md-12 my-news-list">
                                                <div class="row">
                                                    <div class="col-sm-3 news-inner-img-div my-news-inner-img-div-left"
                                                         style="background-image:url('{{  isset($newsImage[$item->content_key]) ? action('FilesController@download', ['id'=>$newsImage[$item->content_key]['id'],'code'=>$newsImage[$item->content_key]['code'],1] ) : url('images/empatia/default_img_contents.jpg')}}')"></div>
                                                    <div class="col-sm-9">
                                                        <p class="color-black">{!! $item->summary !!}</p>
                                                        @if(!empty($item->start_date))
                                                            <p class="color-ccc"><small>{!! $item->start_date !!}</small>
                                                            </p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            @endforeach

                        @endif
                    @endif
                </div>
            </div>


            @if(!empty($next))
                <div class="row">
                    <div class="col-xs-12">
                        <a class='jscroll-next'
                           href='{{ URL::action('PublicContentsController@showNewsList', $next->content_key) }} '>{{ trans("pages.next") }}</a>
                    </div>
                </div>
            @endif

            @if(empty($contentKey))
        </div>
    @endif


    @if(empty($contentKey))
        <script>
            $('#infinite-scroll').jscroll({
                loadingHtml: '<img src="{{ asset('images/ajax-loader.gif') }}" alt="Loading" /> ',
                padding: 20,
                nextSelector: 'a.jscroll-next:last',
            });
        </script>
    @endif

@endsection