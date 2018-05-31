@extends('public.empatia._layouts.index')

@section('content')
    <div class="container-fluid padding-top-bottom-35">
        <div class="row menus-row margin-top-15 margin-bottom-35">
            <div class="menus-line col-sm-6 col-sm-offset-3">
                <span class="fa fa-newspaper-o" style="color: #b3b3b3">&nbsp;</span>
                News
            </div>
        </div>
    </div>

    <div id="infinite-scroll" class="newsList-container content-fluid"
         style="max-width: 1280px!important;margin:auto;margin-bottom:20px">

        @include("public.empatia.cms.lists.news_list")
    </div>
    <script>
        $('#infinite-scroll').jscroll({
            loadingHtml: '<div class="text-center"><img src="{{ asset('images/ajax-loader.gif') }}" alt="Loading" /></div>',
            padding: 20,
            nextSelector: 'a.jscroll-next:last'
        });
    </script>
@endsection