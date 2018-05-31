@extends('public._layouts.index')

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border bg-light-blue">
                    <h3 class="box-title"><i class="fa fa-comments"></i> Discussion</h3>

                    <div class="box-tools pull-right" style="top: 2px;">
                        <a href="{!! action('PublicDiscussionController@create') !!}" class="btn btn-success">{{ trans('discussion.create') }}</a>
                    </div>

                </div>

                <div class="box-body" style="margin-top: 10px;padding-bottom: 20px;">
                    @if(count($discussion) > 0)

                        <div class="col-sm-8 col-md-12">
                            <div class="box box-primary" style="min-height: 90px;margin-bottom: 0px;">
                                <table  class="table table-bordered">
                                    <tbody>
                                    @foreach ($discussion as $topic)
                                        <tr style="">
                                            <td style="text-align: center;width: 6%;padding: 10px;vertical-align: middle;">
                                                <a href="{!! action('PublicDiscussionController@show', $topic->id) !!}">
                                                    <i style="color:#3c8dbc;" class="fa fa-comment-o fa-2x"></i>
                                                </a>
                                            </td>
                                            <td style="padding: 10px;">
                                                <a class="subject" href="{!! action('PublicTopicController@index', $topic->id ) !!}" style="font-size: 16px;font-weight: bold;">{{ $topic->title }}</a>

                                                <p style="padding-top: 5px;word-wrap: break-word; text-overflow: ellipsis; overflow: hidden;height: 40px;">{{ $topic->contents }} </p>
                                            </td>
                                            <td style="width: 15%;text-align: right;padding: 10px;vertical-align: middle;">
                                                <p>
                                                    {{$topic->statistics->posts}} Messages
                                                    <br>{{$topic->statistics->topics}} Topics
                                                </p>
                                            </td>
                                            <td style="width: 20%;padding: 10px;vertical-align: middle; text-align: center">
                                                @if(isset($topic->lastpost->updated_at))
                                                    <p><strong><a href="{{ action('PublicUsersController@show', $topic->lasttopic->created_by) }}">{{$usersNames[$topic->lasttopic->created_by]['name']}} </a></strong> in <a href="{!! action('PublicPostController@index', $topic->lasttopic->id ) !!}">{{$topic->lasttopic->title}}</a><br>
                                                        <!--strong>Ontem</strong--> {{ date('M d, Y H:i:s', strtotime($topic->lastpost->updated_at))}}
                                                    </p>
                                                @else
                                                    <p>
                                                        <strong>Without Topics</strong>
                                                    </p>
                                                @endif
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
    </div>

@endsection