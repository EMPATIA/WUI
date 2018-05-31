@extends('public.empaville._layouts.index')
@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="box box-solid box-deflaut">

                <div class="box-header " style="color: #ffffff; background-color: #333333;">
                    <h3 style="padding-top: 5px;display: inline-block;margin: 0;line-height: 1;"><i class="fa fa-comments"></i>{!! trans('PublicCbs.ideas') !!}</h3>

                </div>

                <div class="box-body" style="margin-top: 10px;padding-bottom: 20px;">
                    @foreach ($cbsData as $cb)
                        <div class="col-sm-12 col-md-12">
                            <div class="box" style="min-height: 90px;margin-bottom: 0px; border-top-color: #737373;">

                                <table  class="table table-bordered">
                                    <tbody>
                                    <tr style="">
                                        <td style="text-align: center;width: 6%;padding: 10px;vertical-align: middle;">
                                            <a href="{!! action('PublicCbsController@show', ['cbKey' =>$cb->cb_key, 'type'=> $type] ) !!}">
                                                <i style="color:#62a351;" class="fa fa-comment-o fa-2x"></i>
                                            </a>
                                        </td>
                                        <td style="padding: 10px;">
                                            <a class="subject" href="{!! action('PublicCbsController@show', ['cbKey' =>$cb->cb_key, 'type'=> $type] )  !!}" style="font-size: 18px;font-weight: bold; color: #62a351">{{ $cb->title }}</a>

                                            <p style="padding-top: 10px;padding-left: 15px;word-wrap: break-word; text-overflow: ellipsis; overflow: hidden;height: 45px;">{{ $cb->contents }} </p>

                                            <div style="bottom: 0;padding-left: 0px; font-size: 11px;">
                                                <i class="fa fa-clock-o margin-r-5" style="color: #999;"></i>{{substr($cb->created_at, 0, 10)}}<br>
                                                {!! trans('PublicCbs.createdBy') !!} <i><a href="{{ action('PublicUsersController@show', $cb->created_by) }}" style="color:#62a351;">{{$usersNames[$cb->created_by]['name']}}</a></i>
                                            </div>
                                        </td>
                                        <td style="width: 15%;text-align: right;padding: 10px;vertical-align: middle;padding-top: 15px;">
                                            <p>
                                                {{$cb->statistics->posts}} {!! trans('PublicCbs.messages') !!}
                                                <br>{{$cb->statistics->topics}} {!! trans('PublicCbs.topics') !!}
                                            </p>
                                        </td>
                                        <td style="width: 20%;padding: 10px;vertical-align: middle; text-align: center">
                                            @if(isset($cb->lastpost->updated_at))
                                                <p><strong><a href="{{ action('PublicUsersController@show', $cb->lasttopic->created_by) }}" style="color: #62a351">{{$usersNames[$cb->lasttopic->created_by]['name']}} </a></strong> in <a href="{!! action('PublicPostController@index', $cb->lasttopic->id ) !!}" style="color: #62a351">{{$cb->lasttopic->title}}</a><br>
                                                    <!--strong>Ontem</strong--> {{ date('M d, Y H:i:s', strtotime($cb->lastpost->updated_at))}}
                                                </p>
                                            @else
                                                <p>
                                                    <strong>{!! trans('PublicCbs.withoutTopics') !!}</strong>
                                                </p>
                                            @endif
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

@endsection
