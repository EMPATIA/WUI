<table>
    <tr>
        <td><b>{!! trans('topic.topicID') !!}</b></td>
        <td><b>{!! trans('topic.topicNumber') !!}</b></td>
        <td><b>{!! trans('topic.title') !!}</b></td>
        <td><b>{!! trans('topic.summary') !!}</b></td>
        <td><b>{!! trans('topic.contents') !!}</b></td>
        @foreach($parametersTitle as $title)
            <td><b>{!! $title !!}</b></td>
        @endforeach
        <td><b>{!! trans('topic.createdAt') !!}</b></td>
        {{--<td><b>{!! trans('topic.status') !!}</b></td>--}}
        <td><b>{!! trans('topic.createdBy') !!}</b></td>
        <td><b>{!! trans('topic.link') !!}</b></td>
        @if($availableVoteEvents)
            @foreach($availableVoteEvents as $voteEventMethod => $voteEvent)
                <?php 
                    $colspan = 3;
                    if (!empty($voteAnalysisData[$voteEventMethod]["totalOptionsCount"]))
                        $colspan += $voteAnalysisData[$voteEventMethod]["totalOptionsCount"]*3;
                ?>
                <td colspan="{{ $colspan }}"><b>{{$voteEvent}}</b></td>
            @endforeach
        @endif
    </tr>
    @if($availableVoteEvents)
        <tr>
            <td colspan="{{ 8 + count($parametersTitle??[]) }}">&nbsp;</td>
            @foreach($availableVoteEvents as $voteEventMethod => $voteEvent)
                <td colspan="3">&nbsp;</td>
                @foreach($voteAnalysisData[$voteEventMethod]["parameters"]??[] as $parameter) 
                    <?php $colspan = (3*count($parameter["parametersOptions"])); ?>
                    <td colspan="{{ $colspan }}">
                        {{ $parameter["parameterName"] }}
                    </td>
                @endforeach
            @endforeach
        </tr>
        <tr>
            <td colspan="{{ 8 + count($parametersTitle??[]) }}">&nbsp;</td>
            @foreach($availableVoteEvents as $voteEventMethod => $voteEvent)
                <td colspan="3">&nbsp;</td>
                @foreach($voteAnalysisData[$voteEventMethod]["parameters"]??[] as $parameter) 
                    @foreach($parameter["parametersOptions"] as $parameterOption)
                        <td colspan="3">
                            {{ $parameterOption }}
                        </td>
                    @endforeach
                @endforeach
            @endforeach
        </tr>
    @endif

    @foreach ($topics as $topic)
    <tr>
        <td>
            {{ $topic->topic_key }}
        </td>
        <td>
            {!! !empty($topic->topic_number) ? $topic->topic_number : "" !!}
        </td>
        <td>
            {!! !empty($topic->title) ? $topic->title : "" !!}
        </td>
        <td>{!! !empty($topic->summary) ? $topic->summary : "" !!}</td>
        <td>
            {!! !empty($topic->contents) ? $topic->contents  : "" !!}
        </td>
        @foreach($parametersTitle as $parameterCode => $title)
            <td>
                @if(isset($parameterCode) && !empty($parametersData[$topic->topic_key][$parameterCode]["value"]) && is_string($parametersData[$topic->topic_key][$parameterCode]["value"]))
                    {!!  $parametersData[$topic->topic_key][$parameterCode]["value"] !!}
                @endif
            </td>
        @endforeach
        <td>{!! !empty($topic->created_at) ? $topic->created_at : "" !!}</td>
        {{--<td>{!! !empty($topic->status->status_type->name) && isset($topic->status->status_type->name) ? $topic->status->status_type->name : trans('topic.noStatusAvailable') !!}</td>--}}
        <td>
            @if(!empty($topic->created_by))
                {!! $topic->created_by != 'anonymous' && !empty($userNames[$topic->created_by]) ? $userNames[$topic->created_by] : 'anonymous' !!}
            @endif
        </td>
        <td>
            @if(isset($cbKey) && isset($type))
                <a href="{{ action('PublicTopicController@show', ['cbKey' => $cbKey, 'topicKey' => $topic->topic_key, 'type' => $type]) }}">{!! trans('topic.link_to_topic') !!}</a>
            @endif
        </td>
            @if($availableVoteEvents)
                @foreach($availableVoteEvents as $voteEventMethod => $voteEvent)
                    <td> {{ $topic->voteData->{ $voteEventMethod }->votes??0 }}</td>
                    <td> {{ $topic->voteData->{ $voteEventMethod }->positive??0 }}</td>
                    <td> {{ $topic->voteData->{ $voteEventMethod }->negative??0 }}</td>
                    
                    @foreach($voteAnalysisData[$voteEventMethod]["parameters"]??[] as $parameter)
                    @foreach($parameter["parametersOptions"] as $parameterOption)
                        <td>{{ $parameter["votesByTopicParameter"][$topic->topic_key]->parameter_options->{ $parameterOption }->balance??0 }}</td>
                        <td>+{{ $parameter["votesByTopicParameter"][$topic->topic_key]->parameter_options->{ $parameterOption }->positive??0 }}</td>
                        <td>-{{ $parameter["votesByTopicParameter"][$topic->topic_key]->parameter_options->{ $parameterOption }->negative??0 }}</td>
                    @endforeach
                @endforeach
            @endforeach
        @endif
    </tr>
    @endforeach
</table>