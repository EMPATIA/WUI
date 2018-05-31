<!-- Geolocation -->
@php
    $value1 = 0;
    $value2 = 0;
    $count = 0;

    foreach(!empty($geoLocations) ? $geoLocations : [] as $key => $value){
        $arrayLocations = explode(",",$value);
        $value1 = $value1 + $arrayLocations[0];
        $value2 = $value2 + $arrayLocations[1];
        $count++;
    }

    if($value1!=0)
        $average_of_value1 = $value1 / $count;
    if($value2!=0)
        $average_of_value2 = $value2 / $count;

@endphp

<!-- 6) Mapa com todas as localizações ✔ -->
@if(!empty($parameterKey) && !empty($geoLocations))
    <div class="padding-top-20 padding-bottom-20 margin-bottom-30">
        <div class="row">
            <div class="col-12">
                <div class="box-info">
                    <div class="box-header">
                        <h3 class="box-title"><i class="fa fa-map"></i> {{ trans('privateCbsVoteAnalysis.geolocation') }}</h3>
                    </div>
                    <div class="box-body" style="padding-top:0;">
                        <div id="map{{$parameterKey}}" style="height:500px;width:100%;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function initMap{{$parameterKey}}() {
            var map = new google.maps.Map(document.getElementById('map{{$parameterKey}}'), {
                zoom: 8,
                center: {lat: {{ $average_of_value1 }}, lng:  {{ $average_of_value2 }}}
            });
            // Create an array of alphabetical characters used to label the markers.
            var labels = '';

            var locations = [
                    @foreach(!empty($geoLocations) ? $geoLocations : [] as $key => $value)
                    @php
                        $arrayLocations = explode(",",$value);
                    @endphp
                {lat:  {{ $arrayLocations[0] }}, lng: {{ $arrayLocations[1] }} },
                @endforeach
            ];

            var markers = locations.map(function(location, i) {
                return new google.maps.Marker({
                    position: location,
                    label: labels[i % labels.length]
                });
            });

            // Add a marker clusterer to manage the markers.
            var markerCluster = new MarkerClusterer(map, markers,
                {imagePath: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m'});
        }
        initMap{{$parameterKey}}();
    </script>
@else
    <br><br><br><center><i><i class="fa fa-eye-slash" aria-hidden="true"></i> {{ trans('privateCbsVoteAnalysis.no_data_for_maps') }}</i></center><br><br><br>
@endif



<!-- 6) Mapa de calor com as localizações ✔ -->
@if(!empty($parameterKey) && !empty($geoLocations))
    <div class="padding-top-20 padding-bottom-20 margin-bottom-30">
        <div class="row">
            <div class="col-12">
                <div class="box-info">
                    <div class="box-header">
                        <h3 class="box-title"><i class="fa fa-map"></i> {{ trans('privateCbsVoteAnalysis.heatmap') }}</h3>
                    </div>
                    <div class="box-body" style="padding-top:0;">
                        <div id="heatMap{{$parameterKey}}" style="height:500px;width:100%;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        // don't forget to add gmaps-heatmap.js
        var myLatlng = new google.maps.LatLng({{ $average_of_value1 }},{{ $average_of_value2 }});
        // map options,
        var myOptions = {
            zoom: 9,
            center: myLatlng
        };
        // standard map
        map = new google.maps.Map(document.getElementById("heatMap{{$parameterKey}}"), myOptions);
        // heatmap layer
        heatmap = new HeatmapOverlay(map,
            {
                // radius should be small ONLY if scaleRadius is true (or small radius is intended)
                "radius": 0.01,
                "maxOpacity": 1,
                // scales the radius based on map zoom
                "scaleRadius": true,
                // if set to false the heatmap uses the global maximum for colorization
                // if activated: uses the data maximum within the current map boundaries
                //   (there will always be a red spot with useLocalExtremas true)
                "useLocalExtrema": true,
                // which field name in your data represents the latitude - default "lat"
                latField: 'lat',
                // which field name in your data represents the longitude - default "lng"
                lngField: 'lng',
                // which field name in your data represents the data value - default "value"
                valueField: 'count'
            }
        );

        var testData = {
            max: 1,
            data: [
                    @foreach(!empty($geoLocations) ? $geoLocations : [] as $key => $value)
                    @php
                        $arrayLocations = explode(",",$value);
                    @endphp
                {lat:  {{ $arrayLocations[0] }}, lng: {{ $arrayLocations[1] }}, count: 1 },
                @endforeach
            ]
        };

        heatmap.setData(testData);
    </script>
@endif






<!-- 8) Tabela de propostas com: ✘
          1) Total de votos ✘
          2) Total de votantes ✘
          3) Lista de Localizações ✘
-->
{{--
@if(!empty($parameterKey)  && !empty($votesByTopicParameter) && !empty($parametersOptions))
    <div class="row">
        <div class="col-12">
            <div class="box-info">
                <div class="box-header">
                    <h3 class="box-title"><i class="fa"></i> {{ trans('privateCbsVoteAnalysis.statistics_topics_by') }} {{trans('privateCbsVoteAnalysis.and') }} {{$parameterName ?? ''}}</h3>
                </div>
                <div class="box-body">
                    <div id="table-statistics_by_topic_downloads_wrapper" class="table-download-wrapper">
                        <a id="tableVotesByTopicParemeter_Download_CSV{{ $parameterKey }}"  class="btn btn-flat btn-blue pull-left margin-bottom-10">
                            <i class="fa fa-file-excel-o" aria-hidden="true"></i> {{ trans('privateCbsVoteAnalysis.download_csv') }}
                        </a>
                    </div>
                    <table id="proposals_list_{{$parameterKey}}" class="table table-responsive  table-striped">
                        <thead>
                        <tr>
                            <th rowspan="2">{{ trans('privateCbsVoteAnalysis.title') }}</th>
                            <th rowspan="2" style="width: 20px;">{{ trans('privateCbsVoteAnalysis.totals') }}</th>
                            <th rowspan="2" style="width: 20px;"><i class="fa fa-users" aria-hidden="true" title="{{ trans('privateCbsVoteAnalysis.voters') }}"></i></th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                    <script>
                        var votesByTopicParameter_{{$parameterKey}} = [
                                @if(!empty($votesByTopicParameter))
                                @foreach($votesByTopicParameter as $vote)
                            {
                                "vote_title": "{{$vote->title}}",
                                "total": "{{ $vote->total }}",
                                "votersCounter": "{{ $vote->votersCounter }}",
                            },
                            @endforeach
                            @endif
                        ];
                        //Load  datatable
                        var oTblReport = $("#proposals_list_{{$parameterKey}}")
                        oTblReport.DataTable ({
                            data : votesByTopicParameter_{{$parameterKey}},
                            columns : [
                                { "data" : "vote_title" },
                                { "data" : "total" },
                                { "data" : "votersCounter" },
                            ],
                            paging: false,
                            language: {
                                url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                                search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                            },
                            stateSave: false,
                            order: [['1', 'desc']]
                        });

                        // Export data for CSV (javascript)
                        $( "#tableVotesByTopicParemeter_Download_CSV{{ $parameterKey }}" ).click(function() {
                            var d = new Date();
                            var suffix_name = d.getFullYear()+"_"+(1+d.getMonth())+"_"+d.getDate()+"_"+d.getHours()+"_"+d.getMinutes()+"_"+d.getSeconds();
                            var filename = "votes_by_topic_parameter_"+suffix_name+"_table.csv";
                            downloadCSV( votesByTopicParameter_{{$parameterKey}}, filename);
                        });
                    </script>
                </div>
            </div>
        </div>
    </div>
@endif--}}