@extends('private._private.index')

@section('content')


    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-body" title="Summary">
                    <h2>Performance</h2>
                </div>
            </div>
        </div>
    </div>
   <p>Intervalo de tempo a analisar:</p>
    <select id="timeFilter" name="timeFilter" onchange="getTime()" >
        <option>Selecione</option>
        <option value="5mins">Últimos 5 minutos</option>
        <option value="15mins">Últimos 15 minutos</option>
        <option value="1h">Última hora</option>
        <option value="1d">Último dia</option>
        <option value="1w">Última semana</option>
        <option value="1m">Último mes</option>
        <option value="1y">Último ano</option>
        <option value="range">Intervalo de tempo</option>

    </select>

    <p></p>
    <div class="container">
        <div class='col-md-5'>
            <div class="form-group">
                <div class='input-group date' id='datetimepicker6'  style="visibility: hidden">
                    <input type='text' class="form-control" />
                    <span class="input-group-addon">
                    <span class="glyphicon glyphicon-calendar"></span>
                </span>
                </div>
            </div>
        </div>
        <div class='col-md-5'>
            <div class="form-group">
                <div class='input-group date' id='datetimepicker7'  style="visibility: hidden">
                    <input type='text' class="form-control" />
                    <span class="input-group-addon">
                    <span class="glyphicon glyphicon-calendar"></span>
                </span>
                </div>
            </div>
        </div>
    </div>
    Selecione um componente
    <select id="componentFilter" name="componentFilter" onchange="loadServersByComponent()" >
        <option value="not">Selecione</option>
        @foreach ($comp as $component)
            <option value="{{$component["name"]}}">{{$component["name"]}}</option>
        @endforeach
    </select>

    <div id="div-server-filter"></div>

    <div id="div_graphics" style="height: 1500px"></div>

@endsection

@section('header_styles')
    <link href="{!! asset(elixir('css/bootstrap-datetimepicker/bootstrap-datetimepicker.css')) !!}" rel="stylesheet" type="text/css"/>
@endsection

@section('scripts')
    <script src="/bootstrap/plupload-fix/bootstrap.js"></script>
    <script src="{!! asset(elixir('js/bootstrap-datetimepicker/moment-with-locales.js')) !!}"></script>
    <script src="{!! asset(elixir('js/bootstrap-datetimepicker/bootstrap-datetimepicker.js')) !!}"></script>

    <script type="text/javascript">
        $(function () {
            $('#datetimepicker6').datetimepicker();
            $('#datetimepicker7').datetimepicker({
                useCurrent: false //Important! See issue #1075
            });
            $("#datetimepicker6").on("dp.change", function (e) {
                $('#datetimepicker7').data("DateTimePicker").minDate(e.date);
            });
            $("#datetimepicker7").on("dp.change", function (e) {
                $('#datetimepicker6').data("DateTimePicker").maxDate(e.date);
            });
        });
    </script>

    <script type="text/javascript">

        function getTime(){


            if(  $("#timeFilter").val() != null || $("#timeFilter").val() !="not"){
                if(  $("#timeFilter").val() == "range"){
                    var e = document.getElementById("datetimepicker6");
                    e.style.visibility = 'visible';
                    var e = document.getElementById("datetimepicker7");
                    e.style.visibility = 'visible';

                } else{
                    var e = document.getElementById("datetimepicker6");
                    e.style.visibility = 'hidden';
                    var e = document.getElementById("datetimepicker7");
                    e.style.visibility = 'hidden';
                }
            }
        }

    </script>

    <script type="text/javascript">

        function loadServersByComponent(){
            var component=$("#componentFilter").val();
            var timeFilter=$("#timeFilter").val();
            var startRange = null;
            var endRange = null;
            if(timeFilter=="range"){
                var startRangeAux= new Date($("#datetimepicker6 >input").val());
                startRange =  startRangeAux.getFullYear() + "-" +(startRangeAux.getMonth()+1) + "-" + startRangeAux.getDate() + " 00:00:00";
                var endRangeAux = new Date($("#datetimepicker7 >input").val());
                endRange =  endRangeAux.getFullYear() + "-" +(endRangeAux.getMonth()+1) + "-" + endRangeAux.getDate() + " 23:59:00";
            }





        };


    </script>

    <script type="text/javascript">
        function loadData(){

            var timeFilter=$("#timeFilter").val();
            var startRange = null;
            var endRange = null;
            var showGraphics = 0;
            if(  $("#serverFilter").val() != "not" || $("#serverFilter").val() > -1){
                showGraphics = 1;
            }

            if(timeFilter=="range"){

                var startRangeAux= new Date($("#datetimepicker6 >input").val());
                startRange =  startRangeAux.getFullYear() + "-" +(startRangeAux.getMonth()+1) + "-" + startRangeAux.getDate() + " 00:00:00";
                var endRangeAux = new Date($("#datetimepicker7 >input").val());
                endRange =  endRangeAux.getFullYear() + "-" +(endRangeAux.getMonth()+1) + "-" + endRangeAux.getDate() + " 23:59:00";
            }

            var server = $("#serverFilter").val();
            var component = $("#componentFilter").val();


        };
    </script>



@endsection