
<h3> {{ trans('privateCbsVote.vote_statistics_by_age_in_person') }}</h3>
<div id="statistics_by_age_in_person" style="height: 300px;">
</div>
<h3> {{ trans('privateCbsVote.vote_statistics_by_age_web') }} </h3>
<div id="statistics_by_age_web" style="height: 300px;">
</div>




{{-- vote statistics chart by age --}}
<script>
            {{--vote statistics chart by age - in person --}}
    var data = [
                    @if(isset($votesByAge->in_person_votes))
                    @foreach($votesByAge->in_person_votes as $town => $votesAge)
                    @foreach($votesAge as $age => $value)
                {'{!! trans('privateCbsVote.town') !!}': "{!! $town !!}", "name":'{!! $age !!}', '{!! trans('privateCbsVote.votes') !!}': {{$value}} },
                @endforeach
                @endforeach
                @endif
            ];

    var visualization = d3plus.viz()
            .container("#statistics_by_age_in_person")
            .data(data)
            .type("bar")
            .id("name")
            .y('{!! trans('privateCbsVote.votes') !!}')
            .y({"stacked": true})
            .x('{!! trans('privateCbsVote.town') !!}')
            .font({"family": "Helvetica, Arial, sans-serif", "color": "#000"}).resize(true)
            .draw();

            {{--vote statistics chart by age - web --}}
    var data = [
                    @if(isset($votesByAge->web_votes))
                    @foreach($votesByAge->web_votes as $town => $votesAge)
                    @foreach($votesAge as $age => $value)
                {'{!! trans('privateCbsVote.town') !!}': "{!! $town !!}", "name":'{!! $age !!}', '{!! trans('privateCbsVote.votes') !!}': {{$value}} },
                @endforeach
                @endforeach
                @endif
            ];

    var visualization = d3plus.viz()
            .container("#statistics_by_age_web")
            .data(data)
            .type("bar")
            .id("name")
            .y('{!! trans('privateCbsVote.votes') !!}')
            .y({"stacked": true})
            .x('{!! trans('privateCbsVote.town') !!}')
            .font({"family": "Helvetica, Arial, sans-serif", "color": "#000"}).resize(true)
            .draw();
</script>