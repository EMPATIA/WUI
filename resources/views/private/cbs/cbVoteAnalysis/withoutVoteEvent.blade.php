@extends('private._private.index')

@section('content')
    @include('private.cbs.tabs')

    <div class="card-body background-white">
        <div class="alert alert-warning">
            <h4><span class="glyphicon glyphicon-warning-sign"></span> {{ trans('privateCbsVoteAnalysis.without_vote_events') }}</h4>
        </div>
    </div>
@endsection

@section('scripts')
@endsection