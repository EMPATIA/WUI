@extends('public._layouts.index')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border bg-light-blue">
                    <h3 class="box-title"><i class="fa fa-comments"></i> Discussion Topics</h3>

                    @if($isModerator)
                        <div class="box-tools pull-right" style="top: 2px;">
                            <a href="{!! action('PublicTopicController@create', $cbId) !!}"
                               class="btn btn-success btn">{{ trans('topic.create') }}</a>
                        </div>
                    @endif
                </div>
                <div class="box-body" style="margin-top: 10px;padding-bottom: 20px;">

                    @if(count($topics) > 0)
                        <div class="col-sm-8 col-md-12">
                            <div class="box box-primary" style="min-height: 90px;margin-bottom: 0px;">
                                <table  class="table table-bordered">
                                    <tbody>
                                    @foreach ($topics as $topic)
                                        <tr style="">
                                            <td style="text-align: center;width: 6%;padding: 10px;vertical-align: middle;">

                                                @if($isModerator)
                                                    <a href="{!! action('PublicTopicController@show',  ['cbId'=> $cbId, 'topicId' => $topic->id] ) !!}">
                                                        @else
                                                            <a>
                                                        @endif
                                                        <i style="color:#3c8dbc;" class="fa fa-comment-o fa-2x"></i>
                                                    </a></a>

                                            </td>
                                            <td style="padding: 10px;">
                                                <a class="subject" href="{!! action('PublicDiscussionPostController@index', ['cbId'=> $cbId, 'topicId' => $topic->id] ) !!}" style="font-size: 16px;font-weight: bold;">{{ $topic->title }}</a>

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
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
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