<div class="container-fluid ideas-grid home-news">
    <div class="row" style="padding-bottom:70px ">
        <div class="col-12">
            <div class="container">
                <div class="home-row-title color-text-primary">
                    <span>{{ ONE::transSite("news_last_news") }}</span>
                    <a href="{{ action('PublicContentManagerController@index', ['type' => 'news']) }}">
                        {{ ONE::transSite("news_see_all") }}
                    </a>
                </div>
                <div class="row no-gutters">
                    <?php
                        try{
                            $news = \App\ComModules\CM::getLastOfType("news",3);
                        } catch(Exception $e) {
                            $news = [];
                        }
                    ?>
                    @forelse($news as $new)
                        @include("public.default.cms.lists.news_content_box",["newsContent"=> $new])
                    @empty
                        <!-- If doesn't exist news-->
                        <div class="col-12 idea-card flex primary-color color-text-primary">
                            <div class="no-info-txt">
                                {{ ONE::transSite("news_no_news_to_show") }}
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>