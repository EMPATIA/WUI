<h3> {{ trans('privateCbsVote.vote_statistics_by_profession_in_person') }}</h3>
<div id="statistics_by_profession_in_person" style="height: 300px;">
</div>
<h3> {{ trans('privateCbsVote.vote_statistics_by_profession_web') }} </h3>
<div id="statistics_by_profession_web" style="height: 300px;">
</div>





{{-- vote statistics chart by profession --}}
<script>
            {{--vote statistics chart by profession - in person --}}
    var data = [
                    @if(isset($votesByProfession->in_person_votes))
                    @foreach($votesByProfession->in_person_votes as $town => $votesProfession)
                    @foreach($votesProfession as $profession => $value)
                {'{!! trans('privateCbsVote.town') !!}': "{!! $town !!}", "name":'{!! $profession !!}', '{!! trans('privateCbsVote.votes') !!}': {{$value}} },
                @endforeach
                @endforeach
                @endif
            ];

    var visualization = d3plus.viz()
            .container("#statistics_by_profession_in_person")
            .data(data)
            .type("bar")
            .id("name")
            .y('{!! trans('privateCbsVote.votes') !!}')
            .y({"stacked": true})
            .x('{!! trans('privateCbsVote.town') !!}')
            .font({"family": "Helvetica, Arial, sans-serif", "color": "#000"}).resize(true)
            .draw();

            {{--vote statistics chart by profession - web --}}
    var data = [
                    @if(isset($votesByProfession->web_votes))
                    @foreach($votesByProfession->web_votes as $town => $votesProfession)
                    @foreach($votesProfession as $profession => $value)
                {'{!! trans('privateCbsVote.town') !!}': "{!! $town !!}", "name":'{!! $profession !!}', '{!! trans('privateCbsVote.votes') !!}': {{$value}} },
                @endforeach
                @endforeach
                @endif
            ];

    var visualization = d3plus.viz()
            .container("#statistics_by_profession_web")
            .data(data)
            .type("bar")
            .id("name")
            .y('{!! trans('privateCbsVote.votes') !!}')
            .y({"stacked": true})
            .x('{!! trans('privateCbsVote.town') !!}')
            .font({"family": "Helvetica, Arial, sans-serif", "color": "#000"}).resize(true)
            .draw();
</script>