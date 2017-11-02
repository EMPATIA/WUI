@extends('_private.index')

@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="fa"></i> {{ trans('forum.topic') }}</h3>
            <div style="float:right;">
                <a class="btn btn-success btn-sm" href="#">Approve</a>
                <a class="btn btn-danger btn-sm" href="#">Reject</a>

            </div>
        </div>

        <div class="box-body">
            <!-- Topic -->
            <!--div class="card">
                <div class="card-header" style="background-color:#ecf0f5 ">
                    <div style=" font-weight: bold;" id="forum_topic_title">
                        Topic 1
                    </div>
                </div>
                <div class="card-block" style="padding: 0px; position: relative; min-height: 150px;">
                    <div style="width: 15%;background-color: #f5f5f5;float: left;height: 100%; position: absolute; border-right: 1px solid #ccc;">
                        <div style="text-align: center; padding: 20px;  min-height: 200px; padding-top: 35%;">
                            <i class="fa fa-user fa-2x"></i>
                            <div>User</div>
                        </div>
                    </div>
                    <div style="width: 85%;margin-left: 15%;padding: 15px;" id="forum_topic_message">

                    </div>
                </div>

            </div-->



            <!-- END Topic -->

            <!-- Message Example -->
            <div class="card" id="message_template" style="display: none">
                <div class="card-header" style="background-color: #f5f5f5; height: 30px; padding: 5px 15px;">
                    <div style="float:right;">
                        <a style="margin-left: 20px" class="link" href="#">
                            <i class="fa fa-thumbs-up text-success"></i>
                        </a>
                        <a style="margin-left: 10px" class="link" href="#">
                            <i class="fa fa-thumbs-down text-danger"></i>
                        </a>
                    </div>
                </div>
                <div class="card-block" style="padding: 0px; position: relative; min-height: 150px;">
                    <div style="width: 15%;background-color: #f5f5f5;float: left;height: 100%; position: absolute; border-right: 1px solid #ccc;">
                        <div style="text-align: center; padding: 20px;  min-height: 200px; padding-top: 35%;">
                            <i class="fa fa-user fa-2x"></i>
                            <div>User</div>
                        </div>
                    </div>
                    <div style="width: 85%;margin-left: 15%;padding: 15px;" class="message-block">


                    </div>
                </div>
            </div>
            <!-- END Message Example -->

            @foreach ($messages as $value)
                <div class="card" id="message_template">
                    <div class="card-header" style="background-color: #f5f5f5; height: 30px; padding: 5px 15px;">
                        <div style="float:right;">
                            <a style="margin-left: 20px" class="link" href="#">
                                <i class="fa fa-thumbs-up text-success"></i>
                            </a>
                            <a style="margin-left: 10px" class="link" href="#">
                                <i class="fa fa-thumbs-down text-danger"></i>
                            </a>
                            <a style="margin-left: 10px" class="link" href="{{action("PostController@destroy", $value->id)}}">
                                <i class="fa fa-remove text-danger"></i>
                            </a>
                        </div>
                    </div>
                    <div class="card-block" style="padding: 0px; position: relative; min-height: 150px;">
                        <div style="width: 15%;background-color: #f5f5f5;float: left;height: 100%; position: absolute; border-right: 1px solid #ccc;">
                            <div style="text-align: center; padding: 20px;  min-height: 200px; padding-top: 35%;">
                                <i class="fa fa-user fa-2x"></i>
                                <div>User</div>
                            </div>
                        </div>
                        <div style="width: 85%;margin-left: 15%;padding: 15px;">
                            {{ $value->contents }}
                        </div>
                    </div>
                </div>
            @endforeach

            <div id="forum_messages">

            </div>
            <hr>

            <!-- Message Input -->

            <div class="card" style="height:250px;">
                <div style="padding-left: 20px;padding-top: 10px;background-color: #f5f5f5; border-bottom: 0px solid #ccc;text-align: center"><b>Your Message</b></div>

                <div class="card-block"  style="width: 85%; height: 88%; float: right; padding: 10px; background-color: #f5f5f5;">
                    <form id="topic" name="topic" accept-charset="UTF-8" action="{{ action('PostController@store') }}" method="POST">
                        <div style="width: 100%; height: 80%;">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                            <textarea id="contents" name="contents" style="width: 100%; resize: none; height: 100%; border: 1px solid #ccc;padding: 5px;" autofocus></textarea>
                        </div>
                        <div style="float: right; margin-top: 5px;">
                            <a class="btn btn-success" onclick="document.topic.submit()">
                                <i class="pull-left"></i> Reply Message </a>
                        </div>
                    </form>
                </div>

                <div style="float: right; width: 15%; height: 88%; /* border-right: 1px solid #ccc; */ padding: 15px; background-color: #f5f5f5;">
                    <div style="text-align: center; padding: 20px;padding-top: 45px;">
                        <i class="fa fa-user fa-2x"></i>
                        <div>Anonymous</div>
                    </div>
                </div>
            </div>

            <!-- END Message Input -->
        </div>
    </div>
@endsection

