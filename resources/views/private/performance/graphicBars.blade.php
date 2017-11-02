<div class="container">


        <div class="row">

            <div class="col-sm-12">

                <div id="div_cpus" style="height: 400px" ></div>
            </div>
        </div>


</div>

<script>
    var cpu_data = [
        @foreach($avgDatas as $avgData)
                {"time":  '{{$avgData["day"]}}', "name": "Average", "value": {{$avgData["avg"]}}},
                {"time":  '{{$avgData["day"]}}', "name": "Standard Deviation", "value": {{$avgData["stDev"]}}},

        @endforeach
    ];
    var attributes = [
        {"name": "Average", "hex": "#B0C4DE"},
        {"name": "Standard Deviation", "hex": "#CD853F"}
    ]

    var visualization = d3plus.viz()
    .container("#div_cpus")
    .data(cpu_data)
    .type("bar")
    .id("name")
    .y('value')
    .x('time')
    .title('CPU')
    .y({"grid": false})
    .x({"grid": false})
    .font({"family": "Helvetica, Arial, sans-serif", "color": "#000"}).resize(true)
    .attrs(attributes)
    .color("hex")
    .font({"family": "Helvetica, Arial, sans-serif", "color": "#000"}).resize(true)
    .draw();


</script>