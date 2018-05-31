@extends('public.empaville._layouts.index')

@section('content')
    <!-- Title -->
    @include('public.empatia.pages.contents.header')
    
    <div class="container" id="content-container">
        <div class="body-side-content content-fluid">
            <!-- Banner -->   
            @if(!empty($banners))            
                @include('public.empatia.pages.contents.banners')    
            @endif
            <!-- Content -->
            @if(!empty($html))
                @include('public.empatia.pages.contents.content')
            @endif                
            <!-- Slideshow -->    
            @if(isset($pageContent->slideshow) && $pageContent->slideshow && isset($slideshow) && sizeof($slideshow) > 0)
                @include('public.empatia.pages.contents.slideshow')
            @endif                
            <!-- Files -->    
            @if (!empty($files))
                @include('public.empatia.pages.contents.files')
            @endif
            
            <!-- docs_side -->           
            {{-- @include('public.empatia.pages.contents.docs_side') --}}
            <!-- news -->                
            {{-- @include('public.empatia.pages.contents.news') --}}            
            <!-- events -->                
            {{-- @include('public.empatia.pages.contents.events') --}} 
        </div>   
    </div>
@endsection