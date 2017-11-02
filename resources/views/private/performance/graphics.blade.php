<div class="container">
@foreach($dataPerformances as $key=>$performance)
        <h4> {{$performance->componentName}}</h4>
    <div class="row">
        <div class="col-sm-4">

            <div id="div_memory_{{$key}}" style="height: 400px" ></div>
        </div>

        <div class="col-sm-4">

            <div id="div_io_{{$key}}" style="height: 400px" ></div>
        </div>

        <div class="col-sm-4">

            <div id="div_cpu_{{$key}}" style="height: 400px" ></div>
        </div>
    </div>
@endforeach

</div>


<script>

            {{--memory chart --}}
            @foreach($dataPerformances as $key=>$performance)
            var attributes = [
                {"name": "Memory Used", "hex": "#C6E2FF"},
                {"name": "Read Sector", "hex": "#C6E2FF"},
                {"name": "Read Byte", "hex": "#4682B4"},
                {"name": "Write Sector", "hex": "#FFD39B"},
                {"name": "Write Byte", "hex": "#CD853F"},

            ]

    var memory_data_{{$key}} = [
            @foreach($performance->dataCollections as $performanceDatas)
                    @foreach($performanceDatas as $performanceData)
                        {"time": '{{$performanceData->created_at }}',"name": "Memory Used", "value": {{floatval($performanceData->memory_used)}} },
                    @endforeach
            @endforeach
        ];


    var visualization_{{$key}} = d3plus.viz()
        .container("#div_memory_{{$key}}")
        .data(memory_data_{{$key}})
        .type("line")
        .id("name")
        .y('value')
        .x('time')
        .y({"grid": false})
        .x({"grid": false})
        .title('Memory')
        .shape({
            "interpolate": "monotone"  // takes accepted values to change interpolation type
        })
        //.color(["#FFC0CB", "#CD853F"])
        .attrs(attributes)
        .color("hex")
        .font({"family": "Helvetica, Arial, sans-serif", "color": "#000"}).resize(true)
        .draw();

    var io_data_{{$key}} = [
            @foreach($performance->dataCollections as $performanceDatas)
                @foreach($performanceDatas as $performanceData)
                    {"time": '{{$performanceData->created_at }}',"name": "Read Sector", "value": {{floatval($performanceData->read_sector)}} },
                    {"time": '{{$performanceData->created_at }}',"name": "Write Sector", "value": {{floatval($performanceData->write_sector)}} },
                    {"time": '{{$performanceData->created_at }}',"name": "Read Byte", "value": {{floatval($performanceData->read_byte)}} },
                    {"time": '{{$performanceData->created_at }}',"name": "Write Byte", "value": {{floatval($performanceData->write_byte)}} },
                @endforeach
            @endforeach
    ];
    var visualization_{{$key}} = d3plus.viz()
        .container("#div_io_{{$key}}")
        .data(io_data_{{$key}})
        .type("line")
        .id("name")
        .y('value')
        .x('time')
        .title('IO')
        .y({"grid": false})
        .x({"grid": false})
        .attrs(attributes)
        .color("hex")
        .shape({
            "interpolate": "monotone"  // takes accepted values to change interpolation type
        })
        .font({"family": "Helvetica, Arial, sans-serif", "color": "#000"}).resize(true)
        .draw();

    var cpu_data_{{$key}} = [
            @foreach($performance->cpus as $cpus)
                @foreach($cpus as $key2=>$cpu)
                    @if(!is_array($cpu))
                        {"time":  '{{$cpu->created_at}}', "name": "{{$key2}}", "value": {{floatval($cpu->value)}} },
                            @else
                        {"time":  '{{$cpu["created_at"]}}', "name": "{{$key2}}", "value": {{floatval($cpu["value"])}} },
                    @endif
                @endforeach
            @endforeach
    ];

    var visualization_{{$key}} = d3plus.viz()
        .container("#div_cpu_{{$key}}")
        .data(cpu_data_{{$key}})
        .type("line")
        .id("name")
        .y('value')
        .x('time')
        .title('CPU')
        .y({"grid": false})
        .x({"grid": false})
        .shape({
            "interpolate": "monotone" , // takes accepted values to change interpolation type
            "rendering":"geometricPrecision"
        })
        .font({"family": "Helvetica, Arial, sans-serif", "color": "#000"}).resize(true)
        .draw();
    @endforeach

</script>
