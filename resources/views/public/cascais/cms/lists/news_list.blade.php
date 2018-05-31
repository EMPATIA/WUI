@forelse($contents as $content)
    @include("public.default.cms.lists.news_content_box",["newsContent"=> $content])
    
    @if($loop->last && !empty($nextPage))
        <div class="col-12 loader text-center">
            <a href="{{ action('PublicContentManagerController@index', ["contentType" => $contentType, "page" => $nextPage]) }}">
                {{--  <i class="fa fa-circle-o-notch fa-spin fa-fw"></i>  --}}
                {{ ONE::transSite("news_list_see_more") }}
            </a>
        </div>
    @endif
@empty
    <div class="col-12">
        {{ ONE::transSite("news_list_no_news_to_show") }}
    </div>
@endforelse