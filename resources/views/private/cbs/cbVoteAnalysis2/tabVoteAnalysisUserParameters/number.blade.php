<!-- Number -->

<!-- 8) Tabela de propostas com:
          1) Total de votos ✔
          2) Total de votantes
          3) Lista de numeros
-->
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
                            {{--
                                @foreach($parametersOptions as $option)
                                    <th colspan="3">{{$option}}</th>
                                @endforeach
                                --}}
                            {{-- <th colspan="3">{{ trans('privateCbsVoteAnalysis.no_value') }}</th> --}}
                        </tr>
                        {{--
                        <tr>
                            @foreach($parametersOptions as $option)
                                <th style="width: 20px;">B</th>
                                <th style="width: 20px;">+</th>
                                <th style="width: 20px;">-</th>
                            @endforeach
                            <th style="width: 20px;">B</th>
                            <th style="width: 20px;">+</th>
                            <th style="width: 20px;">-</th>
                        </tr>
                        --}}
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
                                @php
                                    /*
                                    @foreach( $parametersOptions as $option)
                                    "balance_{{ $option }}": "{{ $vote->parameter_options->{$option}->balance ?? 0 }}",
                                    "positive_{{ $option }}": "{{ $vote->parameter_options->{$option}->positive ?? 0 }}",
                                    "negative_{{ $option }}": "{{ $vote->parameter_options->{$option}->negative ?? 0 }}",
                                    @endforeach
                                    */
                                @endphp
                                {{--
                                "balance_no_value": "{{ $vote->parameter_options->no_value->balance ?? 0 }}",
                                "positive_no_value": "{{ $vote->parameter_options->no_value->positive ?? 0 }}",
                                "negative_no_value": "{{ $vote->parameter_options->no_value->negative ?? 0 }}",
                                --}}
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
                                /*
                                @foreach( $parametersOptions as $option)
                                { "data" : "balance_{{ $option }}" } ,
                                { "data" : "positive_{{ $option }}" } ,
                                { "data" : "negative_{{ $option }}" } ,
                                @endforeach
                                { "data" : "balance_no_value" } ,
                                { "data" : "positive_no_value" } ,
                                { "data" : "negative_no_value" } ,
                                */
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
@endif