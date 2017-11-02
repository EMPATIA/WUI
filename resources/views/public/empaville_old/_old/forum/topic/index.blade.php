@extends('public._layouts.index')

@section('content')
    <div class="row">
        <div class="col-md-12">

            <div class="box box-solid box-deflaut">

                <div class="box-header " style="color: #ffffff; background-color: #333333;">
                    <h3 style="padding-top: 5px;display: inline-block;margin: 0;line-height: 1;"><i class="fa fa-comments"></i> Topics</h3>

                    @if($isModerator)
                        <div style="float:right">
                            <a href="{!! action('PublicTopicController@create', $cbId) !!}"
                               class="btn btn-flat empatia">{{ trans('topic.create') }}</a>
                        </div>
                    @endif
                </div>



                <div class="box-body" style="margin-top: 10px;padding-bottom: 20px;">

                    @if(count($topics) > 0)
                        @foreach ($topics as $topic)
                            <div class="col-sm-8 col-md-12">
                                <div class="box" style="min-height: 90px;margin-bottom: 0px; border-top-color: #737373;">
                                    <table  class="table table-bordered">
                                        <tbody>
                                         <tr style="">
                                                <td style="text-align: center;width: 6%;padding: 10px;vertical-align: middle;">
                                                    @if($isModerator)

                                                        <a href="{!! action('PublicTopicController@show',  ['cbId'=> $cbId, 'topicId' => $topic->id] ) !!}">
                                                            @else
                                                                <a>
                                                            @endif
                                                            <i style="color:#62a351;" class="fa fa-comment-o fa-2x"></i>
                                                        </a></a>
                                                </td>
                                                <td style="padding: 10px;">
                                                    <a class="subject" href="{!! action('PublicPostController@index', $topic->id ) !!}" style="font-size: 16px;font-weight: bold;">{{ $topic->title }}</a>

                                                    <p style="padding-top: 5px;word-wrap: break-word; text-overflow: ellipsis; overflow: hidden;height: 40px;">Created by <b><a  href="{!! action('PublicUsersController@show', $topic->created_by ) !!}" >{{ $usersNames[$topic->created_by]['name']}} </a></b></p>
                                                </td>
                                                <td style="width: 15%;text-align: right;padding: 10px;vertical-align: middle;">
                                                    <p>
                                                        {{$topic->statistics->posts_counter}} Messages
                                                        <br>{{$topic->statistics->like_counter}} Likes
                                                        <br>{{$topic->statistics->dislike_counter}} Dislikes
                                                    </p>
                                                </td>
                                                <td style="width: 20%;padding: 10px;vertical-align: middle; text-align: center; position: relative">
                                                    @if($isModerator)
                                                        <div style="position: absolute; right: 10px; top: 5px">
                                                            <a  href="javascript:oneDelete('{!! action('PublicTopicController@delete',  ['cbId'=> $cbId, 'topicId' => $topic->id] ) !!}')">
                                                                <i style="color:red;" class="fa fa-remove"></i>
                                                            </a>
                                                        </div>
                                                    @endif
                                                    <p><strong><a href="{{ action('PublicUsersController@show', $topic->last_post->created_by) }}">{{$usersNames[$topic->last_post->created_by]['name']}} </a></strong><br>
                                                        {{ date('M d, Y H:i:s', strtotime($topic->last_post->updated_at))}}
                                                    </p>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="col-sm-8 col-md-12">
                            <div class="alert alert-warning">
                                <h4><i class="icon fa fa-warning"></i> Alert!</h4>
                                <p>No topics to display...</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection


