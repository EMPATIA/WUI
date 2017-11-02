@extends('private._private.index')

@section('content')
    @if(!empty($voteEvents))
        <div class="margin-bottom-20">
            <div class="row">
                <div class="col-12">
                    <select id="voteEventSelect" name="voteEventSelect" class="voteEventSelect">
                        <option value="">{{ trans('privateCbsVoteAnalysis.select_vote_event') }}</option>
                        @foreach($voteEvents as $key => $voteEvent)
                            <option value="{!! $key !!}" @if(Session::get("voteEventKey") == $key) selected @endif>{!! $voteEvent !!}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    @endif
    <div class="row">
        <div class="col-12 tabs-left" id="nav-analysis" hidden="hidden">
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="nav-item">
                    <a class="nav-link active" href="#tab_vote_analysis_date" role="tab" id="vote_analysis_date" data-toggle="tab" aria-expanded="true">
                        {{ trans('privateCbsVoteAnalysis.vote_analysis_by_date') }}
                    </a>
                </li>
                <li role="presentation" class="nav-item">
                    <a class="nav-link" href="#tab_vote_analysis_last_day" role="tab" id="vote_analysis_last_day" data-toggle="tab" aria-expanded="true">
                        {{ trans('privateCbsVoteAnalysis.vote_analysis_last_day') }}
                    </a>
                </li>
            </ul>
            <!-- Tab panes -->
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active default-padding" id="tab_vote_analysis_date">
                    @if(!empty($voteEventKey))
                        <h3> {{ trans('privateCbsVoteAnalysis.total_vote_statistics_by_date') }}</h3>
                        <div id="statistics_by_top_chart-download-wrapper" class="chart-download-wrapper">
                            <a id="statistics_by_top_DownloadCSV" class="btn btn-flat btn-blue pull-right">
                                <i class="fa fa-file-excel-o" aria-hidden="true"></i> Download CSV
                            </a>
                        </div>

                        <div id="statistics_by_date" style="height: 300px">
                        </div>
                    @endif
                </div>
                <div role="tabpanel" class="tab-pane" id="tab_vote_analysis_last_day">

                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(function() {
            var array = ["{{ $type }}", "{{$cbKey}}"];
            getSidebar('{{ action("OneController@getSidebar") }}', 'votes_by_date', array, 'voteAnalysis' );
        });

        $("#voteEventSelect").select2();
        $("#userParameterSelect").select2();

        $('a[data-toggle="tab"]').on('shown.bs.tab', function () {
            var id = $(this).attr('id');
            var voteEventKey = '{{$voteEventKey ?? null}}';
            if(voteEventKey.length == 0){
                voteEventKey = $( "#voteEventSelect" ).val();
            }
            getVoteAnalysis(id,voteEventKey);
        });

        @if(empty($voteEvents))
             $('#nav-analysis').removeAttr('hidden');
        @endif


        $( "#voteEventSelect" ).change(function() {
            renderSelectedOptions();
        });

        $( "#userParameterSelect" ).change(function() {
            var keySelected = $('#voteEventSelect').val();
            var navActive = $("#nav-analysis").find(".active").attr('id');
            getVoteAnalysis(navActive,keySelected);
        });

        function getVoteAnalysis(id,voteEventKey){
            $.ajax({
                method: 'POST', // Type of response and matches what we said in the route
                url: "{{action('CbsController@getVoteAnalysis')}}", // This is the url we gave in the route
                data: {
                    statistics_type: id,
                    vote_event_key: voteEventKey,
                    parameter_key: ''
                },beforeSend: function () {
                    var ajaxLoader = '<div class="chartLoader"><div><i class="fa fa-spinner fa-pulse fa-3x fa-fw default-color"></i><span class="sr-only">Loading...</span></div></div>';
                    $('#tab_'+id).html(ajaxLoader);
                },
                success: function (response) { // What to do if we succeed
                    if(response == "false") {
                        var errorMessage = "{!! trim(preg_replace('/\s\s+/', ' ', trans('privateCbsVoteAnalysis.something_went_wrong'))) !!}";
                        $('#tab_' + id).html('<div class="chartMessage"><div><i class="fa fa-eye-slash" aria-hidden="true"></i> ' + errorMessage + '</div></div>');
                        toastr.error(errorMessage);
                    } else {
                        $('#tab_'+id).empty();
                        $('#tab_'+id).append(response);
                    }
                    // Remove loader
                    $(".chartLoader").remove();
                },
                error: function () { // What to do if we fail
                    var errorMessage = "{!! trim(preg_replace('/\s\s+/', ' ', trans('privateCbsVoteAnalysis.something_went_wrong'))) !!}";
                    $('#tab_'+id).html('<div class="chartMessage"><div><i class="fa fa-eye-slash" aria-hidden="true"></i> '+errorMessage+'</div></div>');
                    toastr.error(errorMessage);
                    $(".chartLoader").remove();
                }
            });
        }

        function renderSelectedOptions(){
            var keySelected = $("#voteEventSelect").val();
            if(keySelected.length == 0){
                $('#nav-analysis').attr('hidden','hidden');
            }else{
                var navActive = $("#nav-analysis").find(".active").attr('id');
                getVoteAnalysis(navActive,keySelected);
                $('#nav-analysis').removeAttr('hidden');
            }
        }
    </script>
    @if(!empty($voteEventKey))
        <script>
          // $( document ).ready(function() {
                // Data
                var statistics_by_date_data = [
                    @foreach($votesByDate->total->all_votes as $date => $voteValue)
                        {'{!! trim(preg_replace('/\s\s+/', ' ',trans('privateCbsVoteAnalysis.date'))) !!}': "{{ $date }}",
                        "name": '{!! trans('privateCbsVoteAnalysis.all_votes') !!}',
                        '{!! trim(preg_replace('/\s\s+/', ' ',trans('privateCbsVoteAnalysis.votes'))) !!}': {{ number_format($voteValue, 3, '.', ',') }} },
                    @endforeach
                ];

                // Setting Y range - bug fix when values are 0's
                var chartRange = [0, 10];
                for(var i = 0 ; i< statistics_by_date_data.length; i++ ){
                    if(statistics_by_date_data[i].{!! trim(preg_replace('/\s\s+/', ' ',trans('privateCbsVoteAnalysis.votes'))) !!} != 0){
                        chartRange = false;
                    }
                }

                // D3 plus chart rendering
                d3plus.viz()
                    .container("#statistics_by_date")
                    .data(statistics_by_date_data)
                    .type("line")
                    .id("name")
                    .y({
                        "value": "{!! trim(preg_replace('/\r\n|\r|\n/', ' ',trans('privateCbsVoteAnalysis.votes'))) !!}",
                        "range": chartRange
                    })
                    .x('{!! trans('privateCbsVoteAnalysis.date') !!}')
                    .font({"family": "Helvetica, Arial, sans-serif", "color": "#000"})
                    .resize(true)
                    .draw();

                // Error message if there are no results in Vote Analysis  :'(
                if(statistics_by_date_data.length === 0){
                    var warningMessage = "{!! trim(preg_replace('/\s\s+/', ' ', trans('privateCbsVoteAnalysis.no_data_Available'))) !!}";
                    $('#tab_'+id).html('<div class="chartMessage"><div><i class="fa fa-eye-slash" aria-hidden="true"></i> '+warningMessage+'</div></div>');
                    toastr.warning(warningMessage);
                    $(".chartLoader").remove();
                }

              // Error message if there are no results in Vote Analysis  :'(
              if(statistics_by_date_data.length == 0){
                  var warningMessage = "{!! trim(preg_replace('/\s\s+/', ' ', trans('privateCbsVoteAnalysis.no_data_Available'))) !!}";
                  $('#statistics_by_top').html('<div class="chartMessage"><div><i class="fa fa-eye-slash" aria-hidden="true"></i> '+warningMessage+'</div></div>');
                  toastr.warning(warningMessage);
                  $("#statistics_by_top_chart-download-wrapper").remove();
              } else {
                  // Export data for CSV (javascript)
                  $( "#statistics_by_top_DownloadCSV" ).click(function() {
                      var d = new Date();
                      var suffix_name = d.getFullYear()+"_"+(1+d.getMonth())+"_"+d.getDate()+"_"+d.getHours()+"_"+d.getMinutes()+"_"+d.getSeconds();
                      var filename = "statistics_by_date_data_"+suffix_name+".csv";
                      downloadCSV(statistics_by_date_data, filename);
                  });
              }

            // });
        </script>
    @endif


    @if(!empty(Session::get("voteEventKey")) && !empty($voteEvents) && array_key_exists(Session::get("voteEventKey"),$voteEvents))
        <script>
            $( document ).ready(function() {
                renderSelectedOptions();
            });
        </script>
    @endif

@endsection