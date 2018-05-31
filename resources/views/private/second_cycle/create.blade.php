@extends('private.second_cycle.form')

@section('page_title')
    {{trans('secondCycle.create')}}
@endsection

@section('form_init')
    {!! Form::open(['action' => ['SecondCycleController@store','cbKey' => $cbKey,'level' => $level,'parentTopicKey' => $parentTopicKey]]) !!}
@endsection
