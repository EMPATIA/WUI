@extends('public.empavilleSchools._layouts.index')

@section('content')
    <!-- Page content section -->
    <section>
        <div class="container">
            <!-- Title -->
            @include('public.default.pages.contents.header')

                <!-- Banner -->
            @if(!empty($banners))
                @include('public.default.pages.contents.banners')
            @endif
            <br />
            <!-- Content -->
            @if(!empty($html))
                @include('public.default.pages.contents.content')
            @endif
            <br />

                <!-- Slideshow -->
            @if(!empty($pageContent->slideshow) && !empty($slideshow))
                @include('public.default.pages.contents.slideshow')
            @endif
            <br />

                <!-- Files -->
            @if (!empty($files))
                @include('public.default.pages.contents.files')
            @endif
            <div class="bottom-buffer"></div>
        </div>
    </section>

@endsection

