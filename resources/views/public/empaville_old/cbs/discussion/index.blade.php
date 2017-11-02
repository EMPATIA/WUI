@extends('public.empaville._layouts.index')

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="box box-solid box-deflaut">

                <div class="box-header " style="color: #ffffff; background-color: #333333;">
                    <h3 style="padding-top: 5px;display: inline-block;margin: 0;line-height: 1;"><i class="fa fa-comments"></i>{!! trans('PublicCbs.discussions') !!}</h3>

                </div>


                <div class="box-body" style="margin-top: 10px;padding-bottom: 20px;">
                    @if(count($cbsData) > 0)

                        <div class="col-sm-8 col-md-12">
                            <div class="box" style="min-height: 90px;margin-bottom: 0px; border-top-color: #737373;">
                                <table  class="table table-bordered">
                                    <tbody>
                                    @foreach ($cbsData as $topic)
                                        <tr style="">
                                            <td style="text-align: center;width: 6%;padding: 10px;vertical-align: middle;">
                                                <a href="{!! action('PublicCbsController@show', ['topicId' =>$topic->id, 'type'=> $type] ) !!}">
                                                    <i style="color:#3c8dbc;" class="fa fa-comment-o fa-2x"></i>
                                                </a>
                                            </td>
                                            <td style="padding: 10px;">
                                                <a class="subject" href="{!! action('PublicCbsController@show', ['topicId' =>$topic->id, 'type'=> $type] ) !!}" style="font-size: 16px;font-weight: bold;">{{ $topic->title }}</a>

                                                <p style="padding-top: 5px;word-wrap: break-word; text-overflow: ellipsis; overflow: hidden;height: 40px;">{{ $topic->contents }} </p>
                                            </td>
                                            <td style="width: 15%;text-align: right;padding: 10px;vertical-align: middle;">
                                                <p>
                                                    {{$topic->statistics->posts}} {!! trans('PublicCbs.messages') !!}
                                                    <br>{{$topic->statistics->topics}} {!! trans('PublicCbs.topics') !!}
                                                </p>
                                            </td>
                                            <td style="width: 20%;padding: 10px;vertical-align: middle; text-align: center">
                                                @if(isset($topic->lastpost->updated_at))
                                                    <p><strong><a href="{{ action('PublicUsersController@show', $topic->lasttopic->created_by) }}">{{$usersNames[$topic->lasttopic->created_by]['name']}} </a></strong> {!! trans('PublicCbs.in') !!} <a>{{$topic->lasttopic->title}}</a><br>
                                                        <!--strong>Ontem</strong--> {{ date('M d, Y H:i:s', strtotime($topic->lastpost->updated_at))}}
                                                    </p>
                                                @else
                                                    <p>
                                                        <strong>{!! trans('PublicCbs.withoutTopics') !!}</strong>
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
                                <h4><i class="icon fa fa-warning"></i> {!! trans('PublicCbs.alert') !!}</h4>
                                <p>{!! trans('PublicCbs.noTopicsToDisplay') !!}.</p>
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
    </div>

@endsection
