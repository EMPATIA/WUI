<table>
    <tr>
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
            @foreach($availableVoteEvents as $voteEvent)
                <td><b>{{$voteEvent}}</b></td>
            @endforeach
        @endif
    </tr>

    @foreach ($topics as $topic) {
    <tr>
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
                @if(!empty($parametersData[$topic->topic_key][$parameterCode]["value"]))
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
        @if(isset($topic->voteData))
            @foreach($topic->voteData as $voteData)
                <td>
                    {{$voteData->votes}}
                </td>
            @endforeach
        @endif
    </tr>
    @endforeach
</table>