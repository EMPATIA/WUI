@extends('private._private.index')

@section('content')
    <div class="card-body" style="background-color: white;">
        <div class="alert alert-warning">
            <h4><span class="glyphicon glyphicon-warning-sign"></span> {{ trans('privateCbsVoteAnalysis.without_vote_events') }}</h4>
        </div>
    </div>
@endsection

@section('scripts')
@endsection