@extends('private._private.index')


@section('content')
    <br><br>
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-body" title="Summary">
                    <h2>Performance</h2>
                </div>
            </div>

    Analyse average per day:
    <br><br>
    <div class="container">
        <div class='col-md-5'>
            <div class="form-group">
                <div class='input-group date' id='datetimepicker1'  >
                    <input type='text' class="form-control" />
                    <span class="input-group-addon">
                    <span class="glyphicon glyphicon-calendar"></span>
                </span>
                </div>
            </div>
        </div>
        <div class='col-md-5'>
            <div class="form-group">
                <div class='input-group date' id='datetimepicker2'  >
                    <input type='text' class="form-control" />
                    <span class="input-group-addon">
                    <span class="glyphicon glyphicon-calendar"></span>
                </span>
                </div>
            </div>
        </div>


    <button id="buttonOk" type="button" class="btn btn-secondary" onclick="showBarGraphs()">Ok</button>
    <div id="div_graphics_bars"></div>
    </div>
        </div>
    </div>
@endsection
@section('header_styles')
    <link href="{!! asset(elixir('css/bootstrap-datetimepicker/bootstrap-datetimepicker.css')) !!}" rel="stylesheet" type="text/css"/>
@endsection

@section('scripts')
    <script src="/bootstrap/plupload-fix/bootstrap.js"></script>
    <script type="text/javascript">
        $(function () {
            $('#datetimepicker1').datetimepicker();
            $('#datetimepicker2').datetimepicker({
                useCurrent: false //Important! See issue #1075
            });
            $("#datetimepicker1").on("dp.change", function (e) {
                $('#datetimepicker2').data("DateTimePicker").minDate(e.date);
            });
            $("#datetimepicker2").on("dp.change", function (e) {
                $('#datetimepicker1').data("DateTimePicker").maxDate(e.date);

            });
        });

    </script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

    <script>

        function showBarGraphs() {

            flag=0;
            var serverIp = $("#serverFilter").val();

            if(serverIp == "not") {alert("Chose a Server"); flag=1;}
            var startRangeAux= new Date($("#datetimepicker1 >input").val());
            startRange =  startRangeAux.getFullYear() + "-" +(startRangeAux.getMonth()+1) + "-" + startRangeAux.getDate() + " 00:00:00";
            var endRangeAux = new Date($("#datetimepicker2 >input").val());
            endRange =  endRangeAux.getFullYear() + "-" +(endRangeAux.getMonth()+1) + "-" + endRangeAux.getDate() + " 23:59:00";


            if(startRangeAux == 'Invalid Date'){ alert("Select a start time range to analyse"); flag=1;}
            if(endRangeAux == 'Invalid Date'){ alert("Select a end time range to analyse"); flag=1;}
            if(flag==0) {
                $.ajax({
                    method: 'POST', // Type of response and matches what we said in the route
                    url: "{{action('PerformanceController@loadDataPerformanceBars')}}", // This is the url we gave in the route
                    data: {
                        serverIp: serverIp,
                        startRange: startRange,
                        endRange: endRange,
                    }, // a JSON object to send back
                    success: function (response) { // What to do if we succeed
                        if (response != 'false') {
                            console.log("ajax success")
                            console.log(response)
                            $('#div_graphics_bars').empty();
                            $('#div_graphics_bars').append(response);

                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                        console.log("error sending ajax request");
                    }
                });
            }
        }
    </script>

@endsection