<table>
    <tr>
        @if(count($locations) > 0)
            <td><b>{!! trans("excel.locations") !!}</b></td>
        @endif

    @php $questionIndex=0; //counter @endphp

    <!-- First Column Cell - Name -->
    <td>
        <b>{!! trans('privateQuestionnaire.name')!!}</b>
    </td>

    @foreach ($questions as $i=>$question)
        <td>
            <b>{!! $question !!}</b>
        </td>
        @php $questionIndex++; @endphp

    @endforeach
    </tr>
    @foreach ($data as $id => $formReply)
    <tr>

        @if(count($locations) > 0)
        <td>
            @if(!empty($locations[$id]))
            {{ $locations[$id]["location"]  }}
            @endif
        </td>
        @endif

        @php $index=0;  //counter @endphp
        @foreach ($questions as $key => $question)
            <!-- First Column - Name (who answered the form) -->
            @if($index == 0)
                @if(isset($formReply[$key]["created_by_name"]) && !empty($formReply[$key]["created_by_name"]))
                    <td>{!! $formReply[$key]["created_by_name"] !!}</td>
                @else
                    <td>{!! trans('privateTopics.anonymous')!!}</td>
                @endif
            @endif
             <td>
                 @if(array_key_exists($key ,$formReply))
                     @if($formReply[$key]["question_type"] == "File")
                         @if( $formReply[$key]["question_option"] != "null" &&  $formReply[$key]["question_option"] != "" )
                             @foreach( is_array(json_decode($formReply[$key]["question_option"])) ? json_decode($formReply[$key]["question_option"]) : []    as $file )
                                 <a href="{{ action("FilesController@downloadFile",["id" => $file->id, "code" => $file->code]) }}"
                                    target="_blank">{{ $file->name }}</a>;&nbsp;
                             @endforeach
                         @endif
                     @else
                         {!! $formReply[$key]["question_option"] !!}
                     @endif
                 @endif &nbsp;
            </td>

            @php $index++; @endphp

        @endforeach
    </tr>
    @endforeach
</table>