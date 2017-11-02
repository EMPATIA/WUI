@extends('public.empaville._layouts.index')
@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-body" title="Summary">
                    <h5>{{trans("PublicContent.summary")}}</h5><p> {{ $summary }}</p>
                </div>
                <div class="box-body" title="Content">
                    {{ $content }}
                </div>
            </div>
        </div>
    </div>


@endsection
