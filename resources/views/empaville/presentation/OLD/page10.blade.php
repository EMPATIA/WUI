@extends('empaville.presentation.index')

@section('content')
    <div class="content-header">
        <div class="container" style="text-align: center;border-bottom-color: gray;border-bottom-style: solid;">
            <h2>Proposal presentation</h2>
        </div>
    </div>
    <div class="content">
        <div class="container" style="height: 100%;">
            <div class="col-lg-9 col-md-9 col-sm-9">
                <div class="row">
                    <div class="col-md-12 col-md-12 col-sm-12"  style="background-color: white">
                        <h3><span style="color:#62a351">{{$usersNames['name']}}</span></h3>
                        @if(count($ideas) > 0)
                            @foreach ($ideas as $idea)
                                <div class="" id="box1" id="proposal_{{$idea->id}}">
                                    <div class="box"
                                         style="margin-bottom: 10px;border-left: 1px solid #f5f5f5;border-right: 1px solid #f5f5f5; border-bottom: 1px solid #f5f5f5;border-top-color: #737373;">

                                        <div class="box-header" style="height: 35px; width: 100%; ">
                                            <h2 class="box-title" data-toggle="tooltip" data-placement="top"
                                                data-original-title="{{$idea->title}}"
                                                style="overflow: hidden; white-space: nowrap; text-overflow: ellipsis; width: 100%; color: #62a351;">
                                                <span style="font-weight: 600;font-size: 24px; color:#62a351"
                                                >{{$idea->title}}</span>
                                            </h2>
                                        </div>
                                        <div class="box-body no-padding"
                                             style="min-height: 100px; position: relative;">


                                            <div style="height: 100%; width: 100%; font-size: 14px;">
                                                <div id="mydiv" style="text-overflow: ellipsis; overflow: hidden;padding: 10px;  width: 100%;">
                                                    {!! $idea->contents !!}
                                                </div>
                                                <div style=" bottom: 0;padding-left: 10px; font-size: 12px;">
                                                    <i class="fa fa-clock-o margin-r-5"
                                                       style="color: #999;"></i>{{substr($idea->created_at, 0, 10)}}
                                                    ,Created by <i><span style="color:#62a351">{{$usersNames['name']}}</span></i>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="box-footer" style=" color:black; border-top: 0px; padding-bottom: 5px;">
                                            <div class="col-lg-4 col-md-4 col-sm-4">
                                                <div style="padding-top: 0px;text-align: center;border-bottom-color: gray;border-bottom-style: solid;">
                                                    <small>Category</small>
                                                </div>
                                                <div class="text-center">
                                                    <small>
                                                        @foreach($idea->parameters as $parameter)
                                                            @if($parameter->code == 'category')
                                                                <span class="" style="padding: 4px">{{$categoriesNameById[$parameter->pivot->value]}}</span>
                                                            @endif
                                                        @endforeach
                                                    </small>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-4 col-sm-4">
                                                <div style="padding-top: 0px;text-align: center;border-bottom-color: gray;border-bottom-style: solid;">
                                                    <small>Budget</small>
                                                </div>
                                                <div class="text-center">
                                                    <small>
                                                        @foreach($idea->parameters as $parameter)
                                                            @if($parameter->code == 'budget')
                                                                <span class="" style="padding: 4px">{{$categoriesNameById[$parameter->pivot->value]}}</span>
                                                            @endif
                                                        @endforeach
                                                    </small>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-4 col-sm-4">
                                                <div style="padding-top: 0px;text-align: center;border-bottom-color: gray;border-bottom-style: solid;">
                                                    <small>Area</small>
                                                </div>
                                                <div class="text-center">
                                                    <small>
                                                        @foreach($idea->parameters as $parameter)
                                                            @if($parameter->code == 'image_map')
                                                                <span class="" style="padding: 4px">{{isset($location[$idea->id])? $location[$idea->id]:""}}</span>
                                                            @endif
                                                        @endforeach
                                                    </small>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="col-sm-8 col-md-8 col-lg-8">
                                <div class="alert alert-warning">
                                    <h4><i class="icon fa fa-warning"></i> Alert!</h4>

                                    <p>No topics to display...</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-3" id ="box2" style="padding-top: 5%">
                <div id="timer" style="background-image:url('{{ asset('images/clock.jpg') }}');background-repeat: no-repeat;background-size: contain;">
                    <div class="timer"></div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6 text-center" style="padding-top: 2%">
                    <button class="btn btn-success" id="btnStart">start</button>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6 text-center" style="padding-top: 2%">
                    <button class="btn btn-danger" id="btnStop" disabled="true">stop</button>
                </div>
            </div>
        </div>

        <div style="float: right">
            <?php $next = $count+1; $previous = $count-1 ;?>
            <a class="right carousel-control" style="width: 5%;position: fixed" id="rightBtn" onclick="location.href='{{ action('EmpavillePresentationController@showProposal',['cbId'=> $cbId, 'id'=>'10','count'=>($next)]) }}'" role="button">
                <span class="glyphicon glyphicon-chevron-right" aria-hidden="true" style="color: black"></span>
                <span class="sr-only">Next</span>
            </a>
        </div>
        <div style="float: left">
            <a class="left carousel-control" style="width: 5%;position: fixed" id="leftBtn" onclick="location.href='{{ action('EmpavillePresentationController@showProposal',['cbId'=> $cbId ,'id'=>'10','count'=>($previous)]) }}'" role="button">
                <span class="glyphicon glyphicon-chevron-left" aria-hidden="true" style="color: black"></span>
                <span class="sr-only">Previous</span>
            </a>
        </div>
    </div>

@endsection
@section('scripts')
    <script>
        $(document).ready(function() {
            $('#btnStart').click(function() {

                var height = $('#timer').height();
                var width = $('#timer').width();
                var size;
                if(height > width)
                    size = width;
                else
                    size = height;
                // Run the countdown
                $('.timer').circularCountDown({
                    delayToFadeIn:1000,
                    size: size,
                    borderSize: 25,
                    fontColor: '#333333',
                    colorCircle: '#66A2D8',
                    background: '#a3c7e7',
                    reverseLoading: false,
                    fontSize: 48,
                    duration: {
                        hours: 0,
                        minutes: 02,
                        seconds: 00
                    },
                    beforeStart: function() {
                        $('#btnStart').attr("disabled", true);
                        $('#btnStop').removeAttr('disabled');
                    },
                    end: function(countdown) {
                    }
                });
            });

            $('#btnStop').click(function() {
                $('.timer').empty();
                $('#btnStart').removeAttr('disabled');
                $('#btnStop').attr("disabled", true);
            });

        });

    </script>
    <script>
        $(document).ready(function() {
            resizeBox();
        });
        $( window ).resize(function() {
            resizeBox();
        });
        function resizeBox(){
            var box1 = $('#box1');
            var box2 = $('#box2');
            var timer = $('#timer');

            timer.height(timer.width() + 'px');
            box2.height(timer.width()+45 + 'px');

        }
    </script>


@endsection