@extends('public.empaville_new._layouts.index')

@section('content')
    <!-- Page content section -->
    <section>
        <div class="container">
            @if(isset($events))
                <!-- Title -->
                @include('public.empaville_new.pages.contents.header')
                <div class="row eventPage">

                    <div class="col-md-2 col-xs-12 page-content-date">
                        <span class="day-month">{{\Carbon\Carbon::parse($pageContent->start_date)->format('d M')}}</span>
                        <span class="year">{{\Carbon\Carbon::parse($pageContent->start_date)->format('Y')}}</span>
                    </div>
                    <div class="col-md-10">
                        <div class="row">
                            <div class="col-xs-12 content-title">
                                <h3>{!! $pageContent->title or "Page Title" !!}</h3>
                            </div>
                        </div>

                        <!-- Event Description -->
                        <div class="row">
                            <div class="col-xs-12 page-content-summary">{!! $pageContent->summary !!}</div>
                        </div>

                        <!-- Banner -->
                        @if(!empty($banners))
                            @include('public.empaville_new.pages.contents.banners')
                        @endif
                        <br/>
                        <!-- Content -->
                        @if(!empty($html))
                            @include('public.empaville_new.pages.contents.content')
                        @endif
                        <br/>

                        <!-- Slideshow -->
                        @if(!empty($pageContent->slideshow) && !empty($slideshow))
                            @include('public.empaville_new.pages.contents.slideshow')
                        @endif
                        <br/>

                        <!-- Files -->
                        @if (!empty($files))
                            @include('public.empaville_new.pages.contents.files')
                        @endif

                    </div>
                </div>
            @else
                <!-- Title -->
                @include('public.empaville_new.pages.contents.header')

                <div class="row">
                    <div class="col-xs-12 news-contents-header-title content-title">
                        @if (!empty($pageContent->start_date))
                        <span class="contents-header-date">{{\Carbon\Carbon::parse($pageContent->start_date)->format('d-m-Y')}}</span>
                        @endif
                        {{--<h3>{!! $pageContent->title or "Page Title" !!}</h3>--}}
                    </div>
                </div>


            <!-- Banner -->
                @if(!empty($banners))

                        @include('public.empaville_new.pages.contents.banners')
                @endif
                <br/>
                <!-- Content -->
                @if(!empty($html))
                    <div class="newsPage-content">
                    @include('public.empaville_new.pages.contents.content')
                    </div>
                @endif
                <br/>

                <!-- Slideshow -->
                @if(!empty($pageContent->slideshow) && !empty($slideshow))
                    @include('public.empaville_new.pages.contents.slideshow')
                @endif
                <br/>

                <!-- Files -->
                @if (!empty($files))
                    @include('public.empaville_new.pages.contents.files')
                @endif

                {{--</div>--}}
            @endif
            <div class="bottom-buffer"></div>
        </div>
    </section>

@endsection

