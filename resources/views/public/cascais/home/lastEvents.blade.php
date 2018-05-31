<div class="container-fluid events-grid">
    <div class="row secondary-color">
        <div class="col-12">
            <div class="container">
                <div class="home-row-title">
                    <span>{{ ONE::transSite("events_next_events")}}</span>
                    {{--  <a href="{{ action('PublicContentsController@showContentsList', ['type' => 'events']) }}">  --}}
                    <a href="#">
                        {{ ONE::transSite("events_see_all") }}
                    </a>
                </div>
                <div class="row">
                    <?php
                        try{
                            $events = \App\ComModules\CM::getLastOfType("events",6);
                        } catch(Exception $e) {
                            $events = [];
                        }
                        $events = [];
                    ?>
                    @forelse($events as $event)
                        <div class="col-12 col-sm-12 col-md-6 col-lg-4 event-card">
                            <div class="card-content">
                                <a href="#" class="date-title primary-color">
                                    <div class="date color-text-primary">
                                        <p class="number">26</p>
                                        <p class="month">Set</p>
                                    </div>
                                    <div class="title color-text-primary">
                                        <p class="title-txt">Cras a facilisis sapien</p>
                                        <p class="sub-title-txt">Integer ultrices aliquam bibendumn</p>
                                    </div>
                                </a>
                                <div class="time-text">
                                    <div class="time">
                                        09:30
                                    </div>
                                    <div class="text">
                                        Curabitur et logobortis
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty

                    @endforelse
                    <div class="col-12 col-sm-12 col-md-6 col-lg-4 event-card">
                        <div class="card-content">
                            <a href="#" class="date-title primary-color">
                                <div class="date color-text-primary">
                                    <p class="number">26</p>
                                    <p class="month">Set</p>
                                </div>
                                <div class="title color-text-primary">
                                    <p class="title-txt">Nulla non arcu eu tortor</p>
                                    <p class="sub-title-txt"> Sed hendrerit dui at</p>
                                </div>
                            </a>
                            <div class="description light-grey-bg">
                                Phasellus imperdiet fringilla ligula sed placerat. Morbi felis magna, congue ut justo non, faucibus eleifend eros. Vivamus vel posuere nulla. Sed sodales ex diam, ac semper eros accumsan a.
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-12 col-md-6 col-lg-4 event-card">
                        <div class="card-content">
                            <a href="#" class="date-title primary-color">
                                <div class="date color-text-primary">
                                    <p class="number">26</p>
                                    <p class="month">Set</p>
                                </div>
                                <div class="title color-text-primary">
                                    <p class="title-txt">Quisque laoreet, ante dignissim</p>
                                    <p class="sub-title-txt">Pellentesque mollis, eros</p>
                                </div>
                            </a>
                            <div class="time-text">
                                <div class="time">
                                    09:30
                                </div>
                                <div class="text">
                                    Curabitur et logobortis
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if(false)
    <div class="container-fluid ideas-grid home-news">
        <div class="row">
            <div class="col-12">
                <div class="container">
                    <div class="home-row-title color-text-primary">
                        <span>{{ ONE::transSite("events_last_news") }}</span>
                        {{--  <a href="{{ action('PublicContentsController@showContentsList', ['type' => 'news']) }}">  --}}
                        <a href="#">
                            {{ ONE::transSite("events_see_all") }}
                        </a>
                    </div>
                    <div class="row no-gutters">
                        <?php
                            try{
                                $news = \App\ComModules\CM::getLastOfType("news",6);
                            } catch(Exception $e) {
                                $news = [];
                            }
                            $news = [];
                        ?>
                        @forelse($news as $news)
                            <?php
                                $news->sections = collect($news->sections);
                                $newHeader = $news->sections->where("code","=","newHeading")->first() ?? "";
                                if (!empty($newHeader))
                                    $newHeader = collect($newHeader->section_parameters)->where("section_type_parameter.code","=","textParameter")->first()->value??"";

                                $newContent = $news->sections->where('code', '=', 'newContent')->first();
                                if (!empty($newContent)) {
                                    $newContent = strip_tags(collect($newContent->section_parameters)->where("section_type_parameter.code","=","htmlTextArea")->first()->value??"");
                                    if (strlen($newContent)>163)
                                        $newContent = substr($newContent,0,160) . "...";
                                }

                                $newImage = $news->sections->where("code","=","newImage")->first();
                                if (!empty($newImage)) {
                                    $newImage = collect($newImage->section_parameters)->first()->value;
                                    if (!empty($newImage)) {
                                        $newImage = json_decode($newImage)[0];
                                        $newImage = action('FilesController@download', ['id' => $newImage->id,'code' => $newImage->code, 'inline' => 1]);
                                    }
                                }
                                if (empty($newImage))
                                    $newImage = "/images/demo/image-1.jpg";
                            ?>
                            <div class="col-12 col-sm-6 col-md-4 idea-card primary-color color-text-primary">
                                <a href="#" class="a-wrapper">
                                    <div class="card-img" style="background-image:url('{{ $newImage }}')"></div>
                                    <div class="title">
                                        {{ $newHeader }}
                                    </div>
                                    <div class="description">
                                        {!! $newContent !!}
                                    </div>
                                    <div class="see-more-btn">
                                        <hr>
                                        <div class="">
                                            See more <i class="fa fa-long-arrow-right" aria-hidden="true"></i>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @empty
                            <!-- If doesn't exist news-->
                            <div class="col-12 idea-card flex primary-color color-text-primary">
                                <div class="no-info-txt">
                                    There is no news to show
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif