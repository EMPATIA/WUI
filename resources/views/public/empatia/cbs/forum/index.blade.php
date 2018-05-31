@extends('public.empatia._layouts.index')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-body text-center">
                    <h3>{{trans('cbs.idea')}}</h3>
                </div>

            </div>
        </div>
    </div>
    <div class="row">
        @foreach ($cbsData as $cb)
            <div class="col-md-12">
                <div class="info-box">
                    <div class="box-header">
                        <div class="col-md-12 no-padding">
                            <a class="subject" href="{!! action('PublicCbsController@show', [$cb->cb_key, 'type'=> $type] ) !!}">{{ $cb->title }}</a>
                        </div>
                    </div>
                    <div class="box-body">
                        <p>{{ $cb->contents }}</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection