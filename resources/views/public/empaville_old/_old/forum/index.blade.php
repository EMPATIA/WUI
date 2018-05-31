@extends('public._layouts.index')
@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="box box-solid box-deflaut">

                <div class="box-header " style="color: #ffffff; background-color: #333333;">
                    <h3 style="padding-top: 5px;display: inline-block;margin: 0;line-height: 1;"><i class="fa fa-comments"></i> Forum</h3>

                    <div  style="float:right">
                        <a href="{!! action('PublicForumController@create') !!}" class="btn btn-flat empatia" >{{ trans('forum.create') }}</a>
                    </div>
                </div>

                <div class="box-body" style="margin-top: 10px;padding-bottom: 20px;">
                    @foreach ($forum as $topic)
                        <div class="col-sm-12 col-md-12">
                            <div class="box" style="min-height: 90px;margin-bottom: 0px; border-top-color: #737373;">

                                <table  class="table table-bordered">
                                    <tbody>
                                        <tr style="">
                                            <td style="text-align: center;width: 6%;padding: 10px;vertical-align: middle;">
                                                <a href="{!! action('PublicForumController@show', $topic->id) !!}">
                                                    <i style="color:#62a351;" class="fa fa-comment-o fa-2x"></i>
                                                </a>
                                            </td>
                                            <td style="padding: 10px;">
                                                <a class="subject" href="{!! action('PublicTopicController@index', $topic->id ) !!}" style="font-size: 18px;font-weight: bold; color: #62a351">{{ $topic->title }}</a>

                                                <p style="padding-top: 10px;padding-left: 15px;word-wrap: break-word; text-overflow: ellipsis; overflow: hidden;height: 45px;">{{ $topic->contents }} </p>

                                                <div style="bottom: 0;padding-left: 0px; font-size: 11px;">
                                                    <i class="fa fa-clock-o margin-r-5" style="color: #999;"></i>{{substr($topic->created_at, 0, 10)}}<br>
                                                    Created by <i><a href="{{ action('PublicUsersController@show', $topic->created_by) }}" style="color:#62a351;">{{$usersNames[$topic->created_by]['name']}}</a></i>
                                                </div>
                                            </td>
                                            <td style="width: 15%;text-align: right;padding: 10px;vertical-align: middle;padding-top: 15px;">
                                                <p>
                                                    {{$topic->statistics->posts}} Messages
                                                    <br>{{$topic->statistics->topics}} Topics
                                                </p>
                                            </td>
                                            <td style="width: 20%;padding: 10px;vertical-align: middle; text-align: center">
                                                @if(isset($topic->lastpost->updated_at))
                                                    <p><strong><a href="{{ action('PublicUsersController@show', $topic->lasttopic->created_by) }}" style="color: #62a351">{{$usersNames[$topic->lasttopic->created_by]['name']}} </a></strong> in <a href="{!! action('PublicPostController@index', $topic->lasttopic->id ) !!}" style="color: #62a351">{{$topic->lasttopic->title}}</a><br>
                                                        <!--strong>Ontem</strong--> {{ date('M d, Y H:i:s', strtotime($topic->lastpost->updated_at))}}
                                                    </p>
                                                @else
                                                    <p>
                                                        <strong>Without Topics</strong>
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
    </div>


@endsection
