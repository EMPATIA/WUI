@foreach ($cbsDataPagination as $cb)
    <div class="col-lg-4 col-md-6 col-xs-12 cbs-boxes-padding">
        <div class="row pads-box">
            @if(empty($cb->end_date) || $cb->end_date >= \Carbon\Carbon::now())
                <div class="col-xs-6 green-text ">
                    <span class="glyphicon glyphicon-inbox"></span>{!! trans('defaultCbsProposal.forum_open') !!}
                </div>
                <div class="col-xs-6 green-text text-right">
                    <span class="glyphicon glyphicon-time"></span>{!! trans('defaultCbsProposal.close_at') !!}: {{$cb->end_date ?? trans('defaultCbsProposal.undefined')}}
                </div>
            @else
                <div class="col-xs-12 gray-text">
                    <span class="glyphicon glyphicon-inbox"></span>{!! trans('defaultCbsProposal.forum_closed') !!}
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
                <span class="glyphicon glyphicon-option-vertical"></span>{{$cb->statistics->topics}} {!! trans('defaultCbsProposal.topics') !!}
                <span class="glyphicon glyphicon-time"></span>{!! trans('defaultCbsProposal.created_at') !!}: {{\Carbon\Carbon::parse($cb->created_at)->toDateString()}}
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