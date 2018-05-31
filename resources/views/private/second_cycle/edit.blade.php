@extends('private.second_cycle.form')
@section('page_title')
{{trans('secondCycle.modify')}}
@endsection
@section('form_init')
{!! Form::open(['action' => ['SecondCycleController@update','cbKey' => $cbKey, 'topicKey' => $topicKey]]) !!}
@endsection
