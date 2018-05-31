@extends('private._private.pdf')

@section('content')
<table class='proposalsList' border="0" cellspacing="0" cellpadding="0" style="width:24cm;height:18cm;position:absolute;top:1.2cm;left:3cm;">
    @php
    $j = 8;
    @endphp
        
    @for($i = 1;$i <= 4; $i++)    
    <tr>
        <td class='proposal' style="border-bottom:1px solid black;width:11.5cm;height:4.5cm;">
            <div style="width:11.5cm;height:4.5cm;padding-top:2px;padding-bottom:2px;">
                @if(!empty($proposalsTmp[$j]))
                    <p class="title">{{ $proposalsData[$proposalsTmp[$j]->proposal_key]["title"] }}</p>
                    <p class="contents">{{ substr($proposalsData[$proposalsTmp[$j]->proposal_key]["contents"],0,100) }}&hellip;</p>                        
                    <table class='details' border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            @if(!empty($proposalsData[$proposalsTmp[$j]->proposal_key]["parameters"][0]))                            
                            <td>{{ $proposalsData[$proposalsTmp[$j]->proposal_key]["parameters"][0]["description"] }}: {{ $proposalsData[$proposalsTmp[$j]->proposal_key]["parameters"][0]["label"] }}</td>
                            @endif
                            @if(!empty($proposalsData[$proposalsTmp[$j]->proposal_key]["location"]) )                            
                            <td style="text-align:right;">Location: {{ $proposalsData[$proposalsTmp[$j]->proposal_key]["location"] }}</td>
                            @endif
                        </tr>
                        <tr>
                            @if( !empty($proposalsData[$proposalsTmp[$j]->proposal_key]["parameters"][1]) )                    
                            <td>{{ $proposalsData[$proposalsTmp[$j]->proposal_key]["parameters"][1]["description"] }}: {{ $proposalsData[$proposalsTmp[$j]->proposal_key]["parameters"][1]["label"] }}</td>
                            @endif
                            @if(!empty($userNames) && !empty($proposalsData[$proposalsTmp[$j]->proposal_key]["created_by"])  )
                            <td style="text-align:right;">Proposer: {{ $userNames[$proposalsData[$proposalsTmp[$j]->proposal_key]["created_by"]] }}</td>
                            @endif
                        </tr>            
                    </table>                
                @endif
            </div>          
        </td>  
        <td style="width:1cm;border-bottom:1px solid black;">
            <div id="line" style="border-left: 1px solid black;height:4.0cm;margin-bottom:0.5px"></div>
        </td>
        <td class='proposal' style="border-bottom:1px solid black;width:11.5cm;">
            <div style="width:11.5cm;height:4.5cm;padding-top:2px;padding-bottom:2px;">
                @if(!empty($proposalsTmp[$i]))
                    <p class="title">{{ $proposalsData[$proposalsTmp[$i]->proposal_key]["title"] }}</p>
                    <p class="contents">{{ substr($proposalsData[$proposalsTmp[$i]->proposal_key]["contents"],0,100) }}&hellip;</p>                                        
                    <table class='details' border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            @if(!empty($proposalsData[$proposalsTmp[$i]->proposal_key]["parameters"][0]))                            
                            <td>{{ $proposalsData[$proposalsTmp[$i]->proposal_key]["parameters"][0]["description"] }}: {{ $proposalsData[$proposalsTmp[$i]->proposal_key]["parameters"][0]["label"] }}</td>
                            @endif
                            @if(!empty($proposalsData[$proposalsTmp[$i]->proposal_key]["location"]) )                            
                            <td style="text-align:right;">Location: {{ $proposalsData[$proposalsTmp[$i]->proposal_key]["location"] }}</td>
                            @endif
                        </tr>
                        <tr>
                            @if( !empty($proposalsData[$proposalsTmp[$i]->proposal_key]["parameters"][1]) )                    
                            <td>{{ $proposalsData[$proposalsTmp[$i]->proposal_key]["parameters"][1]["description"] }}: {{ $proposalsData[$proposalsTmp[$i]->proposal_key]["parameters"][1]["label"] }}</td>
                            @endif
                            @if(!empty($userNames) && !empty($proposalsData[$proposalsTmp[$i]->proposal_key]["created_by"])  )
                            <td style="text-align:right;">Proposer: {{ $userNames[$proposalsData[$proposalsTmp[$i]->proposal_key]["created_by"]] }}</td>
                            @endif
                        </tr>            
                    </table>                  
                @endif
            </div>                       
        </td>     
        @php
        $j--;
        @endphp
        </tr>
    @endfor
    
</table>    

<style>
    /* General */
    @page { margin:  0px; }
    body {
    	font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif;
        padding: 0px 0px 0px 0px; 
    }    
 
    .proposalsList{
        width: 28cm;     
    }    
    
    .title{
        font-size: 14px;
        font-weight: bold;
        margin-bottom: 10px;
    }
    .contents{
        font-size:14px;
        font-weight: normal;
        margin-bottom: 8px;
    }    
    
    .details{
        width: 98%;
    }    
</style>    
@endsection