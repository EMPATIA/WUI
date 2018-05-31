@extends('private._pdf.questionnaire')

@section('content')
    <h1>{{ $titleQuestionnaire }}</h1>

    <br/>
    <hr/>
        
    <!-- User -->
    @if(isset($user))
        <table style="width:100%;margin-bottom:5px;">
            <tr>
                <td style="width:100px;text-align:right;padding:10px 20px"><b>{{ trans("user.name") }}</b></td>
                <td style="padding:10px 20px"  colspan="4">{{ $user }}</td>
            </tr>
        </table>
        <hr/>
        <br/>
    @endif
    
    @foreach($questionsAll as $questionGroup)
    <div class="questionGroup">    
        <p class="groupTitle">{{ $questionGroup->title }}</p>
        
        <table style="width:100%;">
            @foreach($questionGroup->questions as $question)
            <tr>
                <td>
                    <p class="question">{{ $question->question }}</p>

                    <div class="questionOptions">
                        @if($question->question_type->name == "Text" )
                            <div class="questionOptionText">
                            @if(!empty($answers["answers"][$question->id]))
                                {!! $answers["answers"][$question->id] !!}
                            @endif
                                &nbsp;
                            </div>
                        @elseif($question->question_type->name == "File" )
                            <div class="questionOptionText">
                                @if($answers["answers"][$question->id] != "null")
                                    @foreach(json_decode($answers["answers"][$question->id])  as $file )
                                        <a href="{{ action("FilesController@downloadFile",["id" => $file->id, "code" => $file->code]) }}" target="_blank">{{ $file->name }}</a><br>
                                    @endforeach
                                @endif
                            </div>
                        @elseif($question->question_type->name == "Radio Buttons" || $question->question_type->name == "Check Box" || $question->question_type->name == "Drop down")

                            <br>

                            @php $i = 1; @endphp
                            @foreach($question->question_options as $questionOption)

                                <div style="width:150px;display:inline-block">
                                    <table>
                                        <tr>
                                            <td style="text-align:left;vertical-align:top;padding-right:5px;">
                                                @if($question->question_type->name == "Check Box" || $question->question_type->name == "Drop down")
                                                    &nbsp;<div id="questionOptionCheckbox{{ $questionOption->id }}" class="questionOptionCheckbox"
                                                        style="@if(is_array($answers["answers"][$question->id]) && in_array($questionOption->id, $answers["answers"][$question->id])
                                                                || $answers["answers"][$question->id] == $questionOption->id) background-color:black; @endif">
                                                    </div>
                                                @elseif($question->question_type->name == "Radio Buttons")
                                                    &nbsp;<div id="questionOptionCheckbox{{ $questionOption->id }}" class="questionOptionRadio"
                                                        style="@if(is_array($answers["answers"][$question->id]) && in_array($questionOption->id, $answers["answers"][$question->id]) || $answers["answers"][$question->id] == $questionOption->id) background-color:black; @endif">
                                                    </div>
                                                @endif
                                            </td>
                                            <td style="text-align:left;vertical-align: top;">{!! $questionOption->label !!}</td>
                                        </tr>
                                    </table>
                                </div>

                                @if($i % 4 == 0)
                                    <br>
                                @endif
                                @php $i++; @endphp

                            @endforeach  
                        @elseif($question->question_type->name == "Text Area")
                            <div class="questionOptionTextarea">
                                @if(!empty($answers["answers"][$question->id]))
                                    {!! $answers["answers"][$question->id] !!}
                                @endif
                                &nbsp;
                            </div>
                        @endif
                    </div>
                
                </td>
            </tr>    
            @endforeach
          
        </table>    
            
        <br/>        
    @endforeach

    
    <style>
        .questionOptionText{
            border:1px solid black;
            min-height:26px;
            width:100%;
            padding: 10px 10px 2px 10px;
        }
        
        .questionOptionTextarea{
            border:1px solid black;
            min-height:120px;
            width:100%;
            padding: 10px;
        }
        
        .questionOptionCheckbox{
            height:16px;
            width:16px;
            border:1px black solid;
            border-radius:2px;
            display:inline-block;
        }
        
        .questionOptionRadio{
            height:16px;
            width:16px;
            border:1px black solid;
            border-radius:9px;
            display:inline-block;
        }        
        
        
        /* General */
        @page { 
            margin:  0px; 
        }
        body {
            font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif;
            padding: 40px 40px 40px 100px; 
        }    
        h1{
            font-size: 20px;
        }        
        .questionGroup{
            margin-top:4px;            
            margin-bottom:10px;  
        }
        .groupTitle{
            font-weight: bold;   
            font-size: 18px;
            margin-top:10px;            
            margin-bottom:20px;              
        }
        .question{
            font-size: 16px;
            margin-top: 10px;            
            margin-bottom:8px;              
        }   
        .questionOptions{
            font-size: 12px;            
            margin-top:10px;            
            margin-bottom:30px;             
        }
    </style>     
@endsection