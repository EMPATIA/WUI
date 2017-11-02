@extends('private._private.index')

@section('content')
    <div class="card-body" style="background-color: white;">

        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="nav-item"><a class="active nav-link" href="#tab-statistics-by-date" role="tab" data-toggle="tab" aria-expanded="true">{{ trans('privateCbsVote.vote_statistics_by_date') }}</a></li>
            <li role="presentation" class="nav-item"><a class="nav-link" href="#tab-statistics-by-town" id="town" role="tab" data-toggle="tab" aria-expanded="false">{{ trans('privateCbsVote.vote_statistics_by_town') }}</a></li>
            <li role="presentation" class="nav-item"><a class="nav-link" href="#tab-statistics-by-age" id="age" role="tab" data-toggle="tab" aria-expanded="false">{{ trans('privateCbsVote.vote_statistics_by_age') }}</a></li>
            <li role="presentation" class="nav-item"><a class="nav-link" href="#tab-statistics-by-gender" id="gender" role="tab" data-toggle="tab" aria-expanded="false">{{ trans('privateCbsVote.vote_statistics_by_gender') }}</a></li>
            <li role="presentation" class="nav-item"><a class="nav-link" href="#tab-statistics-by-profession" id="profession" role="tab" data-toggle="tab" aria-expanded="false">{{ trans('privateCbsVote.vote_statistics_by_profession') }}</a></li>
            <li role="presentation" class="nav-item"><a class="nav-link" href="#tab-statistics-by-education" id="education" role="tab" data-toggle="tab" aria-expanded="false">{{ trans('privateCbsVote.vote_statistics_by_education') }}</a></li>
        </ul>
        <!-- Tab panes -->
        <div class="tab-content" style="background-color: white">
            <div role="tabpanel" class="tab-pane active" id="tab-statistics-by-date">
                <h3> {{ trans('privateCbsVote.vote_statistics_by_date') }}</h3>
                <div id="statistics_by_date" style="height: 300px">
                </div>
            </div>
            <div role="tabpanel" class="tab-pane" id="tab-statistics-by-town">

            </div>
            <div role="tabpanel" class="tab-pane" id="tab-statistics-by-age">

            </div>
            <div role="tabpanel" class="tab-pane" id="tab-statistics-by-gender">

            </div>
            <div role="tabpanel" class="tab-pane" id="tab-statistics-by-profession">

            </div>
            <div role="tabpanel" class="tab-pane" id="tab-statistics-by-education">
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {

            var id = $(this).attr('id');
            $.ajax({
                method: 'POST', // Type of response and matches what we said in the route
                url: "{{action('CbsVoteController@getStatistics',isset($voteEventKey) ? $voteEventKey : null)}}", // This is the url we gave in the route
                data: {
                    statistics_type: id,
                }, // a JSON object to send back
                success: function (response) { // What to do if we succeed
                    if (response != 'false') {
                        $('#tab-statistics-by-'+id).empty();
                        $('#tab-statistics-by-'+id).append(response);
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail

                }
            });

        });
    </script>

    {{--vote statistics chart by date--}}
    <script>
        var statistics_by_date_data = [
            @foreach($votesByDate->total as $key => $voteType)
                @foreach($voteType as $date => $value)
                    {'{!! trans('privateCbsVote.date') !!}': "{{ $date }}", "name":'{!! trans('privateCbsVote.'.$key) !!}', '{!! trans('privateCbsVote.votes') !!}': {{$value}} },
                @endforeach
            @endforeach
        ];
        var visualization = d3plus.viz()
                .container("#statistics_by_date")
                .data(statistics_by_date_data)
                .type("line")
                .id("name")
                .y('{!! trans('privateCbsVote.votes') !!}')
                .x('{!! trans('privateCbsVote.date') !!}')
                .font({"family": "Helvetica, Arial, sans-serif", "color": "#000"})
                .resize(true)
                .draw();
    </script>

@endsection