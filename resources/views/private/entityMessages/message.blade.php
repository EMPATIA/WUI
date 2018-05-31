@extends('private._private.index')

@section('content')
    <div class="box box-primary">

        <div class="box-header">
            <h3 class="box-title"><i class="fa"></i> {{ trans('privateEntityMessages.messages') }}</h3>
        </div>

        <div class="box-body">
            {!! $message !!}
        </div>

        <div class="box-footer">
            <a href="{{action('EntityMessagesController@index')}}" class="btn btn-flat empatia"><i class="fa fa-arrow-left"></i> Voltar</a>
        </div>
    </div>

@endsection