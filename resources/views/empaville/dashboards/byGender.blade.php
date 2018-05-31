@extends('empaville._layout.index')

@section('content')
    <div class="box box-success text-center">
        <div class="box-header with-border">
            {{trans('empaville.proposalByGender')}}
        </div>
        <div class="box-body">
            <div class="col-md-12" style="height: 600px">
                <div id="proposals_votes"></div>
            </div>

        </div>
    </div>
@endsection
@section('scripts')
    <script>

        var data = [
            {"year": 1991, "name":"alpha", "value": 15},
            {"year": 1991, "name":"beta", "value": 10},
            {"year": 1991, "name":"gamma", "value": 5},
            {"year": 1991, "name":"delta", "value": 50},
            {"year": 1992, "name":"alpha", "value": 20},
            {"year": 1992, "name":"beta", "value": 10},
            {"year": 1992, "name":"gamma", "value": 10},
            {"year": 1992, "name":"delta", "value": 43},
            {"year": 1993, "name":"alpha", "value": 30},
            {"year": 1993, "name":"beta", "value": 40},
            {"year": 1993, "name":"gamma", "value": 20},
            {"year": 1993, "name":"delta", "value": 17},
            {"year": 1994, "name":"alpha", "value": 60},
            {"year": 1994, "name":"beta", "value": 60},
            {"year": 1994, "name":"gamma", "value": 25},
            {"year": 1994, "name":"delta", "value": 32}
        ];
        var visualization = d3plus.viz()
                .container("#proposals_votes")
                .data(data)
                .type("bar")
                .id("name")
                .x("year")
                .y("value")
                .resize(true)
                .draw();


    </script>
@endsection
