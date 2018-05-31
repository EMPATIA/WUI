@extends('private._pdf.questionnaire')

@section('content')
    <center><h1>{{ $titleQuestionnaire }}</h1></center>
    
    <br/>
    <hr/>
        
    <!-- User -->
    @if(isset($user))
    <table style="width:100%;margin-bottom:5px;">
        <tr>
            <td style="width:100px;text-align:right;padding:10px 20px"><b>{{ trans("user.name") }}</b></td>  
            <td style="padding:10px 20px"  colspan="4">{{ $user->name }}</td>         
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
                            @if(isset($user)) 
                                {{ $question->reply }}
                            @endif
                            </div>
                        @elseif($question->question_type->name == "Radio Buttons" || $question->question_type->name == "Check Box" || $question->question_type->name == "Drop down")

                        <br>

                        <?php $i = 1; ?>
                        @foreach($question->question_options as $questionOption)
                            <div style="width: 150px;padding:5px;display:inline-block">
                            @if($question->question_type->name == "Check Box" || $question->question_type->name == "Drop down")
                                <div id="questionOptionCheckbox{{ $questionOption->id }}" class="questionOptionCheckbox"
                                    style="@if(isset($user) && is_array($question->reply) && in_array($questionOption->id, $question->reply) || isset($user) &&  $question->reply == $questionOption->id) background-color:black; @endif">
                                </div>
                            @elseif($question->question_type->name == "Radio Buttons")
                                <div id="questionOptionCheckbox{{ $questionOption->id }}" class="questionOptionRadio"
                                    style="@if(isset($user) && is_array($question->reply) && in_array($questionOption->id, $question->reply) || isset($user) && $question->reply == $questionOption->id) background-color:black; @endif">
                                </div>
                            @endif
                            {{ $questionOption->label }}
                            </div>

                            @if($i % 4 == 0)
                               <br>
                            @endif

                            <?php $i++; ?>
                        @endforeach


                        <div style="clear:both;"></div>

                        @elseif($question->question_type->name == "Text Area")
                            <div class="questionOptionTextarea">
                                @if(isset($user)) 
                                    {{ $question->reply }}
                                @endif
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
            height:26px;
            width:100%;
            padding: 10px 10px 2px 10px;
        }
        
        .questionOptionTextarea{
            border:1px solid black;
            height:120px;
            width:100%;
            padding: 10px 10px 2px 10px;
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
            font-size: 26px;
        }        
        .questionGroup{
            margin-top:4px;            
            margin-bottom:10px;  
        }
        .groupTitle{
            font-weight: bold;   
            font-size: 20px;  
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