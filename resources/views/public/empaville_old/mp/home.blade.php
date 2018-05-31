@extends('public.empaville._layouts.index')

@section('content')



    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="glyphicon glyphicon-question-sign"></i> {{ trans('form.home') }}</h3>
                </div>
                <div class="box-body">
                    <div class="container">
                        <div class="row">
                            <h1 class="text-center">{{ trans('mp.intro') }} Empatia: Know, Participate and Create!</h1>
                            <p class="lead margin-big text-center">Lorem ipsum ...</p>
                            <hr>
                        </div>
                        <div class="row">
                            <div class="col-sm-12 col-md-5">
                                <a><img src="http://t-l.it/default/prova/image/default.jpg" alt="Create Idea" class="img-responsive"></a>
                            </div>
                            <div class="col-sm-12 col-md-7">
                                <ul class="tutorialList">
                                    <li><h4><span></span>{{ trans('mp.3m') }} Take 3 minutes to write an idea</h4></li>
                                    <li><h4><span></span>{{ trans('mp.compare') }} Compare it with other people ideas</h4></li>
                                    <li><h4><span></span>{{ trans('mp.publ_create') }} Publish and create a community</h4></li>
                                </ul>
                                <br/><br/>
                                {{--<div class="text-center">--}}
                                    {{--Have you created your idea?--}}
                                    {{--<a href="{{action('MPController@createIdea')}}">--}}
                                        {{--<span class="btn btn-info btn-lg stripBtn">Create an idea</span>--}}
                                    {{--</a>--}}
                                    {{--<br/><br/>--}}
                                    {{--Have you seen if there are ideas like yours?--}}
                                    {{--<a href="{{action('MPController@ideas')}}">--}}
                                        {{--<span class="btn btn-info btn-lg stripBtn">Explore ideas</span>--}}
                                    {{--</a>--}}
                                {{--</div>--}}
                                <div class="payoff hidden-xs">
                                    <a class="stripLink" href="{{action('MPController@createIdea')}}">
                                        <span class="stripSuggest1">{{ trans('mp.have_you') }}Have you created your idea?</span>

                                        <span class="btn btn-info btn-lg stripBtn">{{ trans('mp.create_idea') }}</span>
                                    </a>
                                    <br/><br/>
                                    <a class="stripLink" href="{{action('MPController@ideas')}}">
                                        <span class="stripSuggest2">{{ trans('mp.seen') }}Have you seen if there are ideas like yours?</span>

                                        <span class="btn btn-lg btn-default stripBtn">{{ trans('mp.explore_ideas') }}</span>
                                    </a>
                                </div>
                                <div class="payoff hidden-sm hidden-md hidden-lg">
                                    <a href="{{action('MPController@createIdea')}}" class="btn btn-info btn-lg btn-block">{{ trans('mp.create_idea') }}</a>
                                    <a href="{{action('MPController@ideas')}}" class="btn btn-default btn-lg btn-block">{{ trans('mp.explore_ideas') }}</a>
                                </div>
                                <br /><br /><br /><br />
                                <hr>
                            </div>
                            <div class="row">
                                <h5 class="text-center">{{ trans('mp.proposal_ideas') }}</h5>
                                <p class="lead margin-big text-center">Lorem ipsum ...</p>
                                <div class="form-group text-center">
                                    <a href="{{action('MPController@proposals')}}" class="btn btn-primary btn-lg">{{ trans('mp.proposal_search') }}</a>
                                </div>
                                <hr>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="glyphicon glyphicon-question-sign"></i> {{ trans('mp.home') }}</h3>
                </div>
                <div class="box-body">
                    <div class="BP-home">
                        <div class="container">
                            <div class="thumbnailHead">
                                <span><h3>Lastest ideas created</h3></span>
                                <span class="pull-right viewAll"><a href="{{action('MPController@ideas')}}">{{ trans('mp.see_all') }} »</a></span>
                            </div>
                            
                            <div class="col-md-12 BP-thumbContent">
                                @foreach ($lastestIdeas as $topic)
                                <div class="col-sm-6 col-md-3">
                                    <div class="box box-secundary">
                                        <div class="box-body">
                                            <div class="BP-thumbIcon"><img src="http://t-l.it/default/prova/image/ico-ambiente.png" alt="Ambiente" class="img-responsive"></div>
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
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection