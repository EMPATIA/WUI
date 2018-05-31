@extends('public.empatia._layouts.index')

@section('content')
<div class="container-fluid" style="">
    <div class="row menus-row" style="">
        <div class="col-sm-6 col-sm-offset-3 menus-line" style=""><i class="fa fa-calendar-o" style="font-size: 4rem; color: #999999" aria-hidden="true"></i> {{trans('home.events')}}</div>
    </div>
</div>
<div class="container-fluid" style="">
    <div class="row">
        <div class="col-md-12">
            <div class="eventList-container content-fluid">
                @foreach($informations as $item)
                    <div class="row">
                        <div class="col-xs-12">
                            <div>
                                <h4 class="eventsList-time">
                                    {!! $item->start_date !!}
                                </h4>
                                <a class="eventsList-title" href="{{ URL::action('PublicContentsController@show', $item->content_key) }}"><h3 class="eventsList-title">
                                    {!! $item->title !!}
                                </h3></a>
                            </div>
                        </div>
                    </div>
                @endforeach
                <br>
            </div>                   
        </div>                  
    </div>       
</div>                
@endsection
