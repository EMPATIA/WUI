<h3> {{ trans('privateCbsVote.vote_statistics_by_town') }}</h3>

<div id="statistics_by_town" style="height: 300px">



</div>
{{--vote statistics chart by town--}}
<script>

    var statistics_by_town_data = [
        @foreach($votesByTown as $key => $voteType)
            @foreach($voteType as $town => $value)
                {'{!! trans('privateCbsVote.town') !!}': "{!! $town !!}", "name":'{!! trans('privateCbsVotes.'.$key) !!}', '{!! trans('privateCbsVote.votes') !!}': {{$value}} },
            @endforeach
        @endforeach
    ];

    var visualization = d3plus.viz()
            .container("#statistics_by_town")
            .data(statistics_by_town_data)
            .type("bar")
            .id("name")
            .y('{!! trans('privateCbsVote.votes') !!}')
            .x('{!! trans('privateCbsVote.town') !!}')
            .font({"family": "Helvetica, Arial, sans-serif", "color": "#000"}).resize(true)
            .draw();
</script>

