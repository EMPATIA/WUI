@extends('public.empatia._layouts.index')

@section('content')
    <div id="noContentPage-container">
        <div> <h1><i class="fa fa-wrench" aria-hidden="true">&nbsp;</i></h1></div>
        <div class="title"> <h1>{{trans('content.noContent')}}</h1></div>
    </div>
@endsection