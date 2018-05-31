@extends('public.empaville._layouts.index')

@section('content')
<br/>
<div class="container-fluid" style="max-width: 1280px; margin: auto">
    <div class="row">
        <div class="col-md-12">
            <div class="body-side-content content-fluid">
                @foreach($informations as $item)
                    <div style="background-color: white;" class="box">
                        <div class="box-header">
                            <h4 class="attachment-heading">
                                <a href="{{ URL::action('ContentsController@showEvent', $item->content_key) }}">{!! $item->title !!}</a>
                            </h4>
                            <span class="time">
                                <i class="fa fa-clock-o"></i>
                                  {!! $item->start_date !!}
                            </span>
                        </div>
                        <div class="attachment-block clearfix">
                            <div style="background: white;" class="blockText">
                                {!! $item->summary !!}
                            </div>
                            <a href="{{ URL::action('ContentsController@showEvent', $item->content_key) }}">{{trans('PublicContent.continueReading')}}...</a>
                        </div>
                    </div>                
                @endforeach
                <br>
            </div>                   
        </div>                  
    </div>       
</div>                
@endsection


@section('header_styles')
<style>
.box {
    background: #ffffff none repeat scroll 0 0;
    border-radius: 3px;
    border-top: 3px solid #d2d6de;
    box-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
    margin-bottom: 20px;
    position: relative;
    width: 100%;
}

.box-header::before, .box-body::before, .box-footer::before, .box-header::after, .box-body::after, .box-footer::after {
    content: " ";
    display: table;
}
*::before, *::after {
    box-sizing: border-box;
}
.box-header::after, .box-body::after, .box-footer::after {
    clear: both;
}
.box-header::before, .box-body::before, .box-footer::before, .box-header::after, .box-body::after, .box-footer::after {
    content: " ";
    display: table;
}
*::before, *::after {
    box-sizing: border-box;
}
.box-header {
    color: #444;
    display: block;
    padding: 10px;
    position: relative;
}
.attachment-block {
    background: #f7f7f7 none repeat scroll 0 0;
    border: 1px solid #f4f4f4;
    margin-bottom: 10px;
    padding: 5px;
}
</style>
@endsection