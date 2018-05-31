@extends('private._private.index')

@section('content')

    <div class="row chart">
        <div class="col-md-12">
            <div class="info-box">
                <br>
                <div class="col-md-12">
                    <label for="date">{!! trans("privateLogs.filterDate") !!}</label>
                    {!! Form::open(array('action' => 'AccessesController@analytic','method'=>'get')) !!}
                    {!! Form::hidden('entityKey', isset($entityKey) ? $entityKey : false) !!}
                    <div class="input-group input-daterange">
                        <input type="text" class="form-control oneDatePicker col-sm-4 col-md-3 col-lg-2" id='dayStart' name='dayStart'  value="{{ isset($dayStart) ? $dayStart : null }}" required>
                        <div class="input-group-addon">to</div>
                        <input type="text" class="form-control col-sm-4 col-md-3 col-lg-2 " id='dayEnd' name='dayEnd'  value="{{ isset($dayEnd) ? $dayEnd : null }}" required>
                    </div><br>
                    {!! Form::submit(trans("privateLogs.submit"), ['class'=>'btn btn-flat btn-preview']) !!}
                    {!! Form::close() !!}
                </div>
                <br>
            </div>
        </div>


        <div class="col-md-12">
            <div class="info-box">
                <div>
                    {!! $chart->container() !!}
                </div>
                {!! $chart->script() !!}
            </div>
        </div>

        <div class="col-md-12">
            <div class="info-box">
                <div>
                    {!! $topicChart->container() !!}
                </div>
                {!! $topicChart->script() !!}
            </div>
        </div>
    </div>

@endsection


@section('scripts')

    <script src=//cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js charset=utf-8></script>
    <script src=//cdnjs.cloudflare.com/ajax/libs/highcharts/6.0.6/highcharts.js charset=utf-8></script>
    <script src=//cdn.jsdelivr.net/npm/fusioncharts@3.12.2/fusioncharts.js charset=utf-8></script>
    <script src=//cdnjs.cloudflare.com/ajax/libs/echarts/4.0.2/echarts-en.min.js charset=utf-8></script>

@endsection