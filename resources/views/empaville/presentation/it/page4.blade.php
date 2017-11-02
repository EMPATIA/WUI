@extends('empaville.presentation.index')

@section('content')
    <div class="content-header">
        <div class="container" style="padding-top: 0px;text-align: center;border-bottom-color: gray;border-bottom-style: solid;">
            <h2>Votazione</h2>
        </div>
    </div>
    <div class="content">
        <div class="container" style="height: 100%;;padding-bottom: 5%">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <div class="col-lg-9 col-md-8 col-sm-8" id="box1">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <h2>Hai 20 min per leggere le proposte finali e decidere quale votare</h2>
                        <h2>Voti secondo le modalità del canale assegnato</h2>
                    </div>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-3" id="box3">
                    <div id="timer" style="background-image:url('{{ asset('images/clock.jpg') }}');background-repeat: no-repeat;background-size: contain;">
                        <div class="timer" ></div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6 text-center" style="padding-top: 2%">
                        <button class="btn btn-success" id="btnStart">inizio</button>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6 text-center" style="padding-top: 2%">
                        <button class="btn btn-danger" id="btnStop" disabled="true">stop</button>
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12 text-center" style="padding-top: 2%" id="divMoreTime" hidden>
                        <button class="btn btn-success" id="btnMoreTime">+1Minuto</button>
                    </div>
                </div>

            </div>

            <div class="col-lg-12 col-md-12 col-sm-12" id="box2" style="margin-top: 2%">
                <img src="{{ asset('images/EmpavilleGame_id_card_table.jpg') }}"  />
            </div>
        </div>
        @include('empaville.presentation.carouselRight')
        <div style="float: left">
            <a class="left carousel-control" style="width: 5%" id="leftBtn" onclick="location.href='{{ action('EmpavillePresentationController@showProposal',['cbKey'=> $cbKey, 'id'=>($id-1),'count'=> '4', 'lang' => $lang]) }}'" role="button">
                <span class="glyphicon glyphicon-chevron-left" aria-hidden="true" style="color: black"></span>
                <span class="sr-only">Previous</span>
            </a>
        </div>
    </div>

@endsection
@section('scripts')
    <script src="{{asset('js/sweetalert.min.js')}}"></script>
    <script>
        minutes = 00;
        count = null;
        $(document).ready(function() {
            $('#btnStart').click(function() {
                if(minutes == 0){
                    minutes = 20;
                }
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
                        minutes: minutes,
                        seconds: 00
                    },
                    beforeStart: function(countdown) {
                        count = countdown;
                        openVotes();
                        $('#btnStart').attr("disabled", true);
                        $('#divMoreTime').attr("hidden",'hidden');
                        $('#btnStop').removeAttr('disabled');
                    },
                    end: function(countdown) {
                        //                        $('#btnStart').attr("disabled", false);
                        count = countdown;
                        closeVotes();
                        $('#divMoreTime').removeAttr("hidden");

                    }
                });

            });


            $('#btnStop').click(function() {
                if(count) {
                    count.stopDecrementTimeEvent();
                    count.destroy();
                    count = null;
                }
                closeVotes();
                $('#btnStart').removeAttr('disabled');
                $('#btnStop').attr("disabled", true);
                $('#divMoreTime').removeAttr("hidden");
                minutes = 0;
            });

        });

    </script>
    <script>
        function openVotes(){
            $.ajax({
                method: 'POST', // Type of response and matches what we said in the route
                url: '{{action('EmpavillePresentationController@openVotes')}}', // This is the url we gave in the route
                data: { _token: "{{ csrf_token() }}", cbKey:"{{$cbKey}}"}, // a JSON object to send back
                success: function (object) { // What to do if we succees
                    if(object == 'true') {
                        toastr.success('Votes open!', '', {timeOut: 1000,positionClass: "toast-bottom-right"});
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                }
            });
        }

        function closeVotes() {
            $.ajax({
                method: 'POST', // Type of response and matches what we said in the route
                url: '{{action('EmpavillePresentationController@closeVotes')}}', // This is the url we gave in the route
                data: {_token: "{{ csrf_token() }}", cbKey: "{{$cbKey}}"}, // a JSON object to send back
                success: function (object) { // What to do if we succees
                    if (object == 'true') {
                        toastr.success('Votes closed!', '', {timeOut: 1000, positionClass: "toast-bottom-right"});
                    }
                    else {
                        $('#btnCloseVotes').attr("disabled", false);
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                    $('#btnCloseVotes').attr("disabled", false);
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                }
            });
        }
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
            var timer = $('#timer');
            timer.height(timer.width() + 'px');
        }
    </script>
    <script>
        $('#btnMoreTime').click(function() {
            if(count) {
                count.destroy();
                count = null;
            }
            minutes += 1;
            toastr.success( minutes+' minutes','Additional Time:', {timeOut: 1000,positionClass: "toast-bottom-right"});
            $('#btnStart').attr("disabled", false);
        });
    </script>

@endsection