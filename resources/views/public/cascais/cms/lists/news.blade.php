<?php
    $demoPageTitle = ONE::transSite("news_title");
?>
@extends('public.default._layouts.index')

@section('content')
    <div class="container-fluid ideas-grid home-news">
        <div class="row">
            <div class="col-12">
                <div class="container">
                    <div class="home-row-title color-text-primary">
                        <span>{{ ONE::transSite("news_subtitle") }}</span>
                    </div>
                    <div class="row no-gutters">
                        @include("public.default.cms.lists.news_list")
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection