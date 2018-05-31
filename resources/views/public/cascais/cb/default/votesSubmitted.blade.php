
@extends('public.cascais._layouts.index')

@section('content')

    <div class="row margin-top-50" style="margin-top:50px;">
        <div class="col-12">
            <div class="container">
                <div class="row">
                    <div class="col-12 info-box no-padding margin-top-5">
                        <div class="text">
                            <span class="title">
                                @if(Session::has('user'))
                                    <p class="margin-top-15">{!! ONE::transCb("votes_submitted_dear", $cbKey) !!} {{Session::get('user')->name}},</p>
                                @endif

                                <div class="margin-top-15">
                                {{ ONE::transCb("votes_submitted_message", $cbKey) }}
                                </div>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection