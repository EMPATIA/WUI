<section class="background-light-gray">
    <div class="row menus-row margin-top-15 margin-bottom-15">
        <div class="menus-line col-sm-6 col-sm-offset-3"><span class="fa fa-newspaper-o" style="color: #b3b3b3"></span> {{trans('home.news')}}</div>
    </div>
    <div class="row" style="padding: 0;margin: auto">
        <div class="container-fluid margin-top-35">
            @if(isset($lastNews))
                @forelse($lastNews as $lastNew)
                    @if($loop->iteration < 5)
                        <a href="{{ action('PublicContentsController@show', $lastNew->content_key) }}">
                            <div class="col-md-3 col-sm-6 col-xs-12">
                                <div class="news-box-div">
                                    <div class="news-inner-img-div news-inner-img-div-left height-200" style="background-image:url('{{ isset($newsImage[$lastNew->content_key]) ? action('FilesController@download', ['id'=>$newsImage[$lastNew->content_key]['id'],'code'=>$newsImage[$lastNew->content_key]['code'],1] ) : url('/images/empatia/default_img_contents.jpg')}}')">

                                    </div>
                                    @if(isset($lastNew->publish_date))
                                        <div class="new-date-box">
                                            {{$lastNew->publish_date}}
                                        </div>
                                    @endif
                                    <div class="news-title-box">
                                        {{$lastNew->title}}
                                    </div>
                                </div>
                            </div>
                        </a>
                    @endif
                @empty
                    <div class="otherNE-button text-center" style="">{{ trans("home.there_are_no_news") }}</div>
                @endforelse
            @else
                <div class="otherNE-button text-center" style="">{{ trans("home.there_are_no_news") }}</div>
            @endif
        </div>
    </div>
</section>
