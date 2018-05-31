@extends('private._pdf.questionnaire')

@section('content')

    @foreach ($questionnaire->question_groups as $group)
    <table>
        <tr>
            <td><b>{!! $group->title !!}</b></td>
        </tr>
        <tr><td></td></tr>
        @foreach($group->questions as $question)
            <tr>
                <td>{!! $question->question !!}</td>
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


                {{--    @if( $question->question_type == "File" )
                        @if($question->answer!= "null")
                            @foreach(json_decode($question->answer)  as $file )
                                    <td> <a href="{{ action("FilesController@downloadFile",["id" => $file->id, "code" => $file->code]) }}" target="_blank">{{ $file->name }}</a></td>
                            @endforeach
                        @endif
                    @elseif
                        @foreach($question->form_replies as $formReply)
                            <td>
                                {!! $formReply->answer !!}
                            </td>
                        @endforeach
                    @endif--}}
            <tr><td></td></tr>
        @endforeach
    </table>
    @endforeach
@endsection