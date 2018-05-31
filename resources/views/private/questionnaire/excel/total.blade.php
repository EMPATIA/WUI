<table>
    @foreach ($questionnaire->question_groups as $group)
        <!--
        <tr>
            <td><h3>{!! $group->title !!}</h3></td>
        </tr>
        -->
        @foreach($group->questions as $question)
            @if( !empty($question->question_options) && count($question->question_options) > 0 )
                <tr>
                    <td><b>{!! $question->question !!}</b></td><td><b>Total</b></td>
                    <!-- <td>{!! $question->total_count !!}</td> -->
                </tr>
                @foreach($question->question_options as $option)
                    <tr>
                        <td>
                            {!! $option->label !!}
                        </td>
                        <td>
                            {!! $option->total !!}
                        </td>
                    </tr>
                @endforeach
                <tr><td></td></tr>
            @endif
        @endforeach
    @endforeach
</table>