<h3> {{ trans('privateCbsVote.vote_statistics_by_gender_in_person') }}</h3>
<div id="statistics_by_gender_in_person" style="height: 300px;">
</div>
<h3> {{ trans('privateCbsVote.vote_statistics_by_gender_web') }} </h3>
<div id="statistics_by_gender_web" style="height: 300px;">
</div>





{{-- vote statistics chart by gender --}}
<script>
            {{--vote statistics chart by gender - in person --}}
    var data = [
                    @if(isset($votesByGender->in_person_votes))
                    @foreach($votesByGender->in_person_votes as $town => $votesGender)
                    @foreach($votesGender as $gender => $value)
                {'{!! trans('privateCbsVote.town') !!}': "{!! $town !!}", "name":'{!! $gender !!}', '{!! trans('privateCbsVote.votes') !!}': {{$value}} },
                @endforeach
                @endforeach
                @endif
            ];

    var visualization = d3plus.viz()
            .container("#statistics_by_gender_in_person")
            .data(data)
            .type("bar")
            .id("name")
            .y('{!! trans('privateCbsVote.votes') !!}')
            .y({"stacked": true})
            .x('{!! trans('privateCbsVote.town') !!}')
            .font({"family": "Helvetica, Arial, sans-serif", "color": "#000"}).resize(true)
            .draw();

            {{--vote statistics chart by gender - web --}}
    var data = [
                    @if(isset($votesByGender->web_votes))
                    @foreach($votesByGender->web_votes as $town => $votesGender)
                    @foreach($votesGender as $gender => $value)
                {'{!! trans('privateCbsVote.town') !!}': "{!! $town !!}", "name":'{!! $gender !!}', '{!! trans('privateCbsVote.votes') !!}': {{$value}} },
                @endforeach
                @endforeach
                @endif
            ];

    var visualization = d3plus.viz()
            .container("#statistics_by_gender_web")
            .data(data)
            .type("bar")
            .id("name")
            .y('{!! trans('privateCbsVote.votes') !!}')
            .y({"stacked": true})
            .x('{!! trans('privateCbsVote.town') !!}')
            .font({"family": "Helvetica, Arial, sans-serif", "color": "#000"}).resize(true)
            .draw();
</script>