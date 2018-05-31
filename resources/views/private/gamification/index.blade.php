@extends('private._private.index')

@section('content')

    <!-- CB Configurations -->
    <div class="card flat">
        <div class="card-body">
            <div class="col-12" style="padding-left: 0px;">
                <div class="card flat">
                    <div class="card-header">
                        <a class="collapsed block accordion-header" role="button" data-toggle="collapse"
                           href="#gamification_cb" aria-expanded="false" aria-controls="gamification_cb">
                            {{ trans('gamification.cb')}}
                        </a>
                    </div>
                </div>
            </div>
            <div id="gamification_cb" class="panel-collapse collapse show" role="tabpanel">
                <div class="row">
                    <div class="col-xs-6 col-sm-4 col-md-3">
                        <div class="card-body">
                            {!! Form::label('topics', trans('gamification.topics')) !!}
                            {!! Form::text('topics', null, ['class' => 'form-control','required']) !!}
                        </div>
                    </div>
                    <div class="col-xs-6 col-sm-4 col-md-3">
                        <div class="card-body">
                            {!! Form::label('comments',trans('gamification.comments')) !!}
                            {!! Form::text('comments', null, ['class' => 'form-control', 'required']) !!}
                        </div>
                    </div>
                    <div class="col-xs-6 col-sm-4 col-md-3">
                        <div class="card-body">
                            {!! Form::label('vote', trans('gamification.vote')) !!}
                            {!! Form::text('vote', null, ['class' => 'form-control','required']) !!}
                        </div>
                    </div>
                    <div class="col-xs-6 col-sm-4 col-md-3">
                        <div class="card-body">
                            {!! Form::label('like', trans('gamification.like')) !!}
                            {!! Form::text('like', null, ['class' => 'form-control','required']) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-12" style="padding-left: 0px;">
                <div class="card flat">
                    <div class="card-header">
                        <a class="collapsed block accordion-header" role="button" data-toggle="collapse"
                           href="#gamification_register" aria-expanded="false" aria-controls="gamification_register">
                            {{ trans('gamification.profile')}}
                        </a>
                    </div>
                </div>
            </div>
            <div id="gamification_register" class="panel-collapse collapse show" role="tabpanel">
                <div class="row">
                    <div class="col-xs-6 col-sm-4 col-md-3">
                        <div class="card-body">
                            {!! Form::label('facebook', trans('gamification.facebook')) !!}
                            {!! Form::text('facebook', null, ['class' => 'form-control','required']) !!}
                        </div>
                    </div>
                    <div class="col-xs-6 col-sm-4 col-md-3">
                        <div class="card-body">
                            {!! Form::label('google',trans('gamification.google')) !!}
                            {!! Form::text('google', null, ['class' => 'form-control', 'required']) !!}
                        </div>
                    </div>
                    <div class="col-xs-6 col-sm-4 col-md-3">
                        <div class="card-body">
                            {!! Form::label('full_profile', trans('gamification.full_profile')) !!}
                            {!! Form::text('full_profile', null, ['class' => 'form-control','required']) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-12">
                <form class="box-body">
                    <div class="col-xs-12">
                        <input type="submit" class="btn-submit" form="checkList" value="{{ trans("gamification.submit") }}" style="float: right; margin-top:13px">
                    </div>
                </form>
            </div>
        </div>
    </div>






@endsection


@section('scripts')


@endsection




