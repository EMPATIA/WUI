@extends('public.empaville._layouts.index')

@section('content')

    <div class="row">
        <div class="col-md-8">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="glyphicon glyphicon-wrench"></i> {{ trans('mp_users.how_it_works') }}</h3>
                </div>
                <div class="box-body">
                    <h3>This is how it works</h3>
                    <p>Lorem ipsum...</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="glyphicon glyphicon-eye-open"></i> {{ trans('mp_users.tips') }}</h3>
                </div>
                <div class="box-body">
                    <h3>Tips for you</h3>
                    <p>Lorem ipsum...</p>
                </div>
            </div>
        </div>
    </div>

@endsection