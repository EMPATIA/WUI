@extends('public.empaville._layouts.index')

@section('content')



    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="glyphicon glyphicon-question-sign"></i> {{ trans('mp.ideas') }}</h3>
                </div>
                <div class="box-body">
                    <div class="row">
                        <h1 class="text-center">{{ trans('mp.ideas_explore') }}Explore ideas</h1>
                        <p class="lead margin-big text-center">Lorem ipsum ...</p>
                        <div class="form-group text-center">
                            <a href="{{action('MPController@createIdea')}}" class="btn btn-primary btn-lg">{{ trans('mp.create_idea') }}</a>
                        </div>
                        <hr>
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <form role="search">
                            <input type="text" class="form-control " placeholder="{{ trans('form.search') }}">
                        </form>
                    </div>
                    <div class="col-sm-12 col-md-3">
                        <ul class="nav nav-pills">
                            <li class="up" role="presentation"><a href="fase0-exploreIdeaMap.php"><i class="fa fa-map-marker ico-big"></i> Map</a></li>
                        </ul>
                    </div>
                    <div class="col-sm-12 col-md-3">
                        <div class="BP-select">
                            <select class="form-control">
                                <option>Select Area</option>
                                <option>Environment</option>
                                <option>Health</option>
                                <option>Sports</option>
                                <option>Food</option>
                                <option>Pets</option>
                            </select>
                        </div>
                        <br /><br />
                    </div>

                        
                    <div class="BP-content">
                        <div class="row BP-tabBox">
                            <ul class="nav nav-tabs">
                                <li class="btn-tab active"><a href="#recent" data-toggle="tab">+ recent</a></li>
                                <li class="btn-tab"><a href="#followed" data-toggle="tab">+ followed</a></li>
                                <li class="btn-tab"><a href="#commented" data-toggle="tab">+ commented</a></li>
                            </ul>
                            <!-- TAB visualization -->
                            <div class="tab-content">
                                <div class="tab-pane active" id="recent">
                                    <!-- TAB content -->
                                    <div class="row BP-thumbContent">
                                        <div class="col-md-12 BP-thumbContent">
                                            @foreach ($lastestIdeas as $topic)
                                            <div class="col-sm-6 col-md-3">
                                                <div class="box box-secundary">
                                                    <div class="box-body">
                                                        <div class="BP-breadCrumbs">
                                                            <div class="BP-property">
                                                                {{ $topic['title'] }}<span>»</span>
                                                            </div>
                                                            <div class="BP-category">
                                                                {{ $topic['area'] }}
                                                            </div>
                                                        </div>
                                                        <div class="BP-postHeading">
                                                            <a href="#">{{ $topic['created_by'] }} - {{ $topic['created_at'] }}</a>
                                                        </div>
                                                        <div class="BP-postDescription">
                                                            {{ $topic['contents'] }}
                                                        </div>
                                                        <div class="BP-postAction">
                                                            <a href="fase0-viewIdea.php" class="link activelink"><i class="fa fa-users"></i> {{ $topic['supporters'] }}</a>
                                                            <a href="fase0-viewIdea.php" class="link activelink"><i class="fa fa-check"></i> {{ trans('form.following') }} </a>
                                                            <a href="fase0-viewIdea.php" class="link"><i class="fa fa-search"></i> {{ trans('form.see') }}</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <hr>
                                </div>
                            </div>
                            <div class="tab-content">
                                <div class="tab-pane active" id="followed">
                                    <!-- TAB content -->
                                    <div class="row BP-thumbContent">
                                        <div class="col-md-12 BP-thumbContent">
                                            @foreach ($mostFollowedIdeas as $topic)
                                            <div class="col-sm-6 col-md-3">
                                                <div class="box box-secundary">
                                                    <div class="box-body">
                                                        <div class="BP-breadCrumbs">
                                                            <div class="BP-property">
                                                                {{ $topic['title'] }}<span>»</span>
                                                            </div>
                                                            <div class="BP-category">
                                                                {{ $topic['area'] }}
                                                            </div>
                                                        </div>
                                                        <div class="BP-postHeading">
                                                            <a href="#">{{ $topic['created_by'] }} - {{ $topic['created_at'] }}</a>
                                                        </div>
                                                        <div class="BP-postDescription">
                                                            {{ $topic['contents'] }}
                                                        </div>
                                                        <div class="BP-postAction">
                                                            <a href="fase0-viewIdea.php" class="link activelink"><i class="fa fa-users"></i> {{ $topic['supporters'] }}</a>
                                                            <a href="fase0-viewIdea.php" class="link activelink"><i class="fa fa-check"></i> {{ trans('form.following') }} </a>
                                                            <a href="fase0-viewIdea.php" class="link"><i class="fa fa-search"></i> {{ trans('form.see') }}</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <hr>
                                </div>
                            </div>
                            <div class="tab-content">
                                <div class="tab-pane active" id="commented">
                                    <!-- TAB content -->
                                    <div class="row BP-thumbContent">
                                        <div class="col-md-12 BP-thumbContent">
                                            @foreach ($mostCommentedIdeas as $topic)
                                            <div class="col-sm-6 col-md-3">
                                                <div class="box box-secundary">
                                                    <div class="box-body">
                                                        <div class="BP-breadCrumbs">
                                                            <div class="BP-property">
                                                                {{ $topic['title'] }}<span>»</span>
                                                            </div>
                                                            <div class="BP-category">
                                                                {{ $topic['area'] }}
                                                            </div>
                                                        </div>
                                                        <div class="BP-postHeading">
                                                            <a href="#">{{ $topic['created_by'] }} - {{ $topic['created_at'] }}</a>
                                                        </div>
                                                        <div class="BP-postDescription">
                                                            {{ $topic['contents'] }}
                                                        </div>
                                                        <div class="BP-postAction">
                                                            <a href="fase0-viewIdea.php" class="link activelink"><i class="fa fa-users"></i> {{ $topic['supporters'] }}</a>
                                                            <a href="fase0-viewIdea.php" class="link activelink"><i class="fa fa-check"></i> {{ trans('form.following') }} </a>
                                                            <a href="fase0-viewIdea.php" class="link"><i class="fa fa-search"></i> {{ trans('form.see') }}</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <hr>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
             
    <div class="row">
        <div class="col-md-6">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="glyphicon glyphicon-question-sign"></i> {{ trans('mp.search_ideas') }}</h3>
                </div>
                <div class="box-body">
                    <h1 class="text-center">{{ trans('mp.search_ideas_areas_text') }}</h1>
                    <p class="lead text-center">Lorem ipsum ...</p>
                    <div class="text-center">
                        <a href="#">Area</a><a href="#"> Area</a><a href="#"> Area</a>
                        <a href="#">Area</a><a href="#"> Area</a><a href="#"> Area</a>
                        <a href="#">Area</a><a href="#"> Area</a><a href="#"> Area</a>
                        <a href="#">Area</a><a href="#"> Area</a><a href="#"> Area</a>
                        <hr>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="glyphicon glyphicon-question-sign"></i> {{ trans('mp.search_ideas') }}</h3>
                </div>
                <div class="box-body">
                    <h1 class="text-center">{{ trans('mp.search_ideas_text') }}</h1>
                    <p class="lead text-center">Lorem ipsum ...</p>
                    <div class="text-center">
                        <a href="#">Keyword</a><a href="#"> Keyword</a><a href="#"> Keyword</a>
                        <a href="#">Keyword</a><a href="#"> Keyword</a><a href="#"> Keyword</a>
                        <a href="#">Keyword</a><a href="#"> Keyword</a><a href="#"> Keyword</a>
                        <a href="#">Keyword</a><a href="#"> Keyword</a><a href="#"> Keyword</a>
                        <hr>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection