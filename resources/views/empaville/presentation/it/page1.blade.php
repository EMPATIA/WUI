@extends('empaville.presentation.index')

@section('content')
    <div class="content-header" >
        <div class="container" style="padding-top: 0px;text-align: center;border-bottom-color: gray;border-bottom-style: solid;">
            <h2>Tavola rotonda</h2>
        </div>
    </div>
    <div class="content">
        <div class="container" style="height: 100%;padding-top: 5%;">
            <div class="col-lg-9 col-md-9 col-sm-9" id="box1" style="box-shadow: 0px 0px 23px -3px rgba(0,0,0,0.75);">
                <ul style="font-size: 1.5em">
                <li>Partecipi alla tavola rotonda del tuo quartiere gestita da un moderatore</li>
                <li>Hai 15 minuti per fare una proposta per il tuo quartiere</li>
                <li>Ogni tavolo decide e seleziona 2 proposte secondo modalità definite dal tavolo stesso</li>
                <li>Il budget totale è di 1.5M coins</li>
                    <li>Devi indicare il costo di della tua proposta secondo le seguenti classi di budget:</li>
                    <ul>
                    <li>1000000 coins</li>
                    <li>500000 coins</li>
                    <li>250000 coins</li>
                    </ul>
                </ul>

            </div>
            <div class="col-lg-3 col-md-3 col-sm-3"  id="box2">
                <div id="timer" style="background-image:url('{{ asset('images/clock.jpg') }}');background-repeat: no-repeat;background-size: contain;">
                    <div class="timer"></div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6 text-center" style="padding-top: 2%">
                    <button class="btn btn-success" id="btnStart">inizio</button>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6 text-center" style="padding-top: 2%">
                    <button class="btn btn-danger" id="btnStop" disabled="true">stop</button>
                </div>
            </div>
        </div>
        @include('empaville.presentation.carouselRight')

    </div>
@endsection
@section('scripts')
    <script src="{{asset('js/sweetalert.min.js')}}"></script>
    <script>
        $(document).ready(function() {
            $('#btnStart').click(function() {
                $('#btnStop').removeAttr('disabled');
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
                        minutes: 20,
                        seconds: 00
                    },
                    beforeStart: function() {
                        $('#btnStart').attr("disabled", true);
                    },
                    end: function(countdown) {
                        $('#divCloseProp').removeAttr("hidden");
//                        countdown.destroy();
//                        $('#btnStart').attr("disabled", false);
                    }
                });
            });
        });

        $('#btnStop').click(function() {
            $('.timer').empty();
            $('#btnStart').removeAttr('disabled');
            $('#btnStop').attr("disabled", true);
        });

        {{--$('#btnCloseProp').click(function() {--}}
        {{--$('#btnCloseProp').attr("disabled", true);--}}
        {{--$.ajax({--}}
        {{--method: 'POST', // Type of response and matches what we said in the route--}}
        {{--url: '{{action('EmpavillePresentationController@closeProposals')}}', // This is the url we gave in the route--}}
        {{--data: { _token: "{{ csrf_token() }}"}, // a JSON object to send back--}}
        {{--success: function (object) { // What to do if we succees--}}
        {{--if(object == 'true') {--}}
        {{--swal({--}}
        {{--title: "Proposals closed!",--}}
        {{--text: "",--}}
        {{--type:"success",--}}
        {{--timer: 3000,--}}
        {{--showConfirmButton: false--}}
        {{--});--}}

        {{--}--}}
        {{--else {--}}
        {{--$('#btnCloseProp').attr("disabled", false);--}}
        {{--}--}}

        {{--},--}}
        {{--error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail--}}
        {{--$('#btnCloseProp').attr("disabled", false);--}}
        {{--console.log("AJAX error: " + textStatus + ' : ' + errorThrown);--}}
        {{--}--}}
        {{--});--}}

        {{--});--}}

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
            box2.height(timer.width()+85 + 'px');
        }


    </script>

@endsection