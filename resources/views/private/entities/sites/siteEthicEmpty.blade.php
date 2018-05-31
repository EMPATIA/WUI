@extends('private._private.index')

@section('content')

    <div class="box box-primary">
        <div class="box-body">
            <div class="row">
                @if(Session::get('user_role') == 'admin')
                    <div class="col-12 text-right">
                        {!! ONE::actionButtons(['site_key'=>$siteKey ?? null,'type'=>$type], ['create' => 'SiteEthicsController@create']) !!}
                    </div>
                @endif
                <div class="col-12">
                    <h3>{{ $message ?? '' }}</h3>
                </div>
            </div>
        </div>
    </div>
@endsection