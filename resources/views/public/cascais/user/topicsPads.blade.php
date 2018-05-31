



@forelse ($topics as $i => $topic)
    <?php
    if($i==0){
        $cbKey = $topic->cbKey;
        $cbType = $topic->type;
    }

    ?>
    <tr data-href='{!! action('PublicTopicController@show', [$topic->cbKey , $topic->topic_key, 'type' => $topic->type] ) !!}'>

        <td class="title">{{$topic->title}}</td>
        <td>
            @if(isset($topic->parameters))
                @foreach($topic->parameters as $parameter)
                    @if(isset($parameter->parameter_code) && $parameter->parameter_code == 'category')

                        {!! collect($parameter->options)->where('id', '=', $parameter->pivot->value)->first()->label !!}

                    @endif
                @endforeach
            @endif
        </td>
        <td>
            {!! \Carbon\Carbon::parse($topic->created_at)->format('d/m/Y') !!}
        </td>
    </tr>


@empty
    @if (is_null($originalPageToken))
        <div class="col-12" style="margin-top: 10px">
            {!!  Html::oneMessageInfo(ONE::transSite("user_topic_no_ideas_to_display")) !!}
        </div>
    @endif
@endforelse
@if(!empty($pageToken))

    <tr>
        <td>
            <a class='jscroll-next'
               href='{{ URL::action('PublicUsersController@userTopics',['page' => $pageToken, "ajax_call" => true, 'topics_to_show' => $numberTopicToShow])}}'>{{ ONE::transSite("user_topic_next") }}</a>
        </td>
    </tr>
@endif