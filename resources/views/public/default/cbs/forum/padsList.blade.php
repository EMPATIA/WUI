@foreach ($cbsDataPagination as $cb)
    <div class="col-xs-12 cbs-boxes-padding">
        <div class="row pads-box">
            @if(empty($cb->end_date) || $cb->end_date >= \Carbon\Carbon::now())
                <div class="col-xs-6 green-text">
                    <span class="glyphicon glyphicon-inbox"></span>{!! trans('defaultCbsForum.forum_open') !!}
                </div>
                <div class="col-xs-6 green-text text-right">
                    <span class="glyphicon glyphicon-time"></span>{!! trans('defaultCbsForum.close_at') !!}: {{$cb->end_date ?? trans('defaultCbsForum.undefined')}}
                </div>
            @else
                <div class="col-xs-12 gray-text">
                    <span class="glyphicon glyphicon-inbox"></span>{!! trans('defaultCbsForum.forum_closed') !!}
                </div>
            @endif
            <div class="col-xs-12 cb-title">
                <h4>
                    <a href="{!! action('PublicCbsController@show', ['cbKey' =>$cb->cb_key, 'type'=> $type] )  !!}">{{ $cb->title }}</a>
                </h4>
            </div>
            <div class="col-xs-12 cb-content">
                <p>{{ $cb->contents }}</p>
            </div>
            <div class="col-xs-12">
                <span class="glyphicon glyphicon-time"></span>{!! trans('defaultCbsForum.created_at') !!}: {{\Carbon\Carbon::parse($cb->created_at)->toDateString()}}
                <span class="glyphicon glyphicon-option-vertical"></span>{{$cb->statistics->topics}} {!! trans('defaultCbsForum.topics') !!}
            </div>
        </div>
    </div>
@endforeach
@if(!empty($cbsDataPagination->nextPageUrl()))
    <div class="row">
        <div class="col-xs-12">
            <a class='jscroll-next' href='{{ URL::action('PublicCbsController@index', ["type"=> $type, 'page' => $cbsDataPagination->currentPage()+1])}}'>{{ trans("pages.next") }}</a>
        </div>
    </div>
@endif