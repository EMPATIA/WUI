@if(ONE::actionType('kiosk') == 'show')    
<dt>{{ trans('kiosk.download') }}</dt>
<dd> 
    <a href='{{ action('KiosksController@download', $kiosk->kiosk_key) }}' target="_blank">
        <i class="fa fa-file-pdf-o" style="color:#62a351;"></i>&nbsp;&nbsp;{{ trans('kiosk.pdf') }}
    </a> 
</dd>
@endif

<br/><br/><br/>
     
<!-- tables inside this DIV could have draggable content -->
<div id="redips-drag" class="row">
    <!-- left container -->
    <div class="col-md-6">
        <table id="table1" class="table">                      
            <tbody>
                <tr>
                    <td style="width:50%;;">
                        @if(!empty($proposalsTmp[8]))
                        <div id="{{ $proposalsTmp[8]->proposal_key }}" class="redips-drag green proposal" backToBase="[1, 1, 0]">
                            <div class='row'>
                                <div class='col-10 text-left ellipsis nowrap proposalItem'>
                                 {!!  $proposalsData[$proposalsTmp[8]->proposal_key] !!} 
                                </div>
                                <div class='col-2 proposalDelete'>
                                    <a id='remove{{ $proposalsTmp[8]->proposal_key }}' onclick="removeObject('{{ $proposalsTmp[8]->proposal_key }}')" style='cursor:pointer;' class='btn btn-flat btn-danger btn-sm btn-redisp-remove'><i class="fa fa-remove"></i></a>
                                </div>
                            </div>    
                        </div>                                                                        
                        @endif
                    </td>
                    <td style="width:50%;">
                        @if(!empty($proposalsTmp[1]))
                        <div id="{{ $proposalsTmp[1]->proposal_key }}" class="redips-drag green proposal" backToBase="[1, 1, 0]">
                            <div class='row'>
                                <div class='col-10 text-left ellipsis nowrap proposalItem'>
                                    {!!  $proposalsData[$proposalsTmp[1]->proposal_key] !!} 
                                </div>
                                <div class='col-2 proposalDelete'>
                                    <a  id='remove{{ $proposalsTmp[1]->proposal_key }}' onclick="removeObject('{{ $proposalsTmp[1]->proposal_key }}')" style='cursor:pointer;' class='btn btn-flat btn-danger btn-sm btn-redisp-remove'><i class="fa fa-remove"></i></a>
                                </div>
                            </div>    
                        </div>                                    
                        @endif                                    
                    </td>
                </tr>
                <tr>
                    <td>
                        @if(!empty($proposalsTmp[7]))
                        <div id="{{ $proposalsTmp[7]->proposal_key }}" class="redips-drag green proposal" backToBase="[1, 1, 0]">
                            <div class='row'>
                                <div class='col-10 text-left ellipsis nowrap proposalItem'>
                                    {!!  $proposalsData[$proposalsTmp[7]->proposal_key] !!}
                                </div>
                                <div class='col-2 proposalDelete'>
                                    <a  id='remove{{ $proposalsTmp[7]->proposal_key }}' onclick="removeObject('{{ $proposalsTmp[7]->proposal_key }}')" style='cursor:pointer;' class='btn btn-flat btn-danger btn-sm btn-redisp-remove'><i class="fa fa-remove"></i></a>
                                </div>
                            </div>                                               
                        </div>                                                                        
                        @endif                                      
                    </td>
                    <td>
                        @if(!empty($proposalsTmp[2]))
                        <div id="{{ $proposalsTmp[2]->proposal_key }}" class="redips-drag green proposal" backToBase="[1, 1, 0]">
                            <div class='row'>
                                <div class='col-10 text-left ellipsis nowrap proposalItem'>
                                    {!!  $proposalsData[$proposalsTmp[2]->proposal_key] !!}
                                </div>
                                <div class='col-2 proposalDelete'>
                                    <a  id='remove{{ $proposalsTmp[2]->proposal_key }}' onclick="removeObject('{{ $proposalsTmp[2]->proposal_key }}')" style='cursor:pointer;' class='btn btn-flat btn-danger btn-sm btn-redisp-remove'><i class="fa fa-remove"></i></a>
                                </div>
                            </div>                                              
                        </div>                                                                        
                        @endif           
                    </td>
                </tr>
                <tr>
                    <td>
                        @if(!empty($proposalsTmp[6]))
                        <div id="{{ $proposalsTmp[6]->proposal_key }}" class="redips-drag green proposal" backToBase="[1, 1, 0]" >
                            <div class='row'>
                                <div class='col-10 text-left ellipsis nowrap proposalItem'>
                                    {!!  $proposalsData[$proposalsTmp[6]->proposal_key] !!}
                                </div>
                                <div class='col-2 proposalDelete'>
                                    <a  id='remove{{ $proposalsTmp[6]->proposal_key }}' onclick="removeObject('{{ $proposalsTmp[6]->proposal_key }}')" style='cursor:pointer;' class='btn btn-flat btn-danger btn-sm btn-redisp-remove'><i class="fa fa-remove"></i></a>
                                </div>
                            </div>                                              
                        </div>                                                                        
                        @endif                                      
                    </td>
                    <td>
                        @if(!empty($proposalsTmp[3]))
                        <div id="{{ $proposalsTmp[3]->proposal_key }}" class="redips-drag green proposal" backToBase="[1, 1, 0]">
                            <div class='row'>
                                <div class='col-10 text-left ellipsis nowrap proposalItem'>
                                    {!!  $proposalsData[$proposalsTmp[3]->proposal_key] !!}
                                </div>
                                <div class='col-2 proposalDelete'>
                                    <a  id='remove{{ $proposalsTmp[3]->proposal_key }}' onclick="removeObject('{{ $proposalsTmp[3]->proposal_key }}')" style='cursor:pointer;' class='btn btn-flat btn-danger btn-sm btn-redisp-remove'><i class="fa fa-remove"></i></a>
                                </div>
                            </div>                                               
                        </div>                                                                        
                        @endif                                      
                    </td>
                </tr>
                <tr>
                    <td>
                        @if(!empty($proposalsTmp[5]))
                        <div id="{{ $proposalsTmp[5]->proposal_key }}" class="redips-drag green proposal"  backToBase="[1, 1, 0]" >
                            <div class='row'>
                                <div class='col-10 text-left ellipsis nowrap proposalItem'>
                                    {!!  $proposalsData[$proposalsTmp[5]->proposal_key] !!}
                                </div>
                                <div class='col-2 proposalDelete'>
                                    <a  id='remove{{ $proposalsTmp[5]->proposal_key }}' onclick="removeObject('{{ $proposalsTmp[5]->proposal_key }}')" style='cursor:pointer;' class='btn btn-flat btn-danger btn-sm btn-redisp-remove'><i class="fa fa-remove"></i></a>
                                </div>
                            </div>                                             
                        </div>                                                                        
                        @endif                                      
                    </td>
                    <td>
                        @if(!empty($proposalsTmp[4]))
                        <div id="{{ $proposalsTmp[4]->proposal_key }}" class="redips-drag green proposal"  backToBase="[1, 1, 0]" >
                            <div class='row'>
                                <div class='col-10 text-left ellipsis nowrap proposalItem'>
                                    {!!  $proposalsData[$proposalsTmp[4]->proposal_key] !!}
                                </div>
                                <div class='col-2 proposalDelete'>
                                    <a  id='remove{{ $proposalsTmp[4]->proposal_key }}' onclick="removeObject('{{ $proposalsTmp[4]->proposal_key }}')" style='cursor:pointer;' class='btn btn-flat btn-danger btn-sm btn-redisp-remove'><i class="fa fa-remove"></i></a>
                                </div>
                            </div>                                     
                        </div>                                                                        
                        @endif                                      
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- right container -->
    <div class="col-md-6">
        <table id="table2">
            <colgroup>
                <col width="500"/>
            </colgroup>
            <tbody>
                <tr>
                    <td class="redips-mark">
                        @foreach($topicList as $topic)
                        <div id="{{ $topic->id }}" class="redips-drag green proposal">
                            <div class='row'>
                                <div class='col-10 text-left ellipsis nowrap proposalItem'>
                                 {!! $topic->title !!}
                                </div>
                                <div class='col-2 proposalDelete'>
                                    <a id='remove{{ $topic->id }}' onclick="removeObject('{{  $topic->id }}')" style='cursor:pointer;display:none;' class='btn btn-flat btn-danger btn-sm btn-redisp-remove btn-redisp-remove'><i class="fa fa-remove"></i></a>
                                </div>
                            </div>    
                        </div>
                        @endforeach
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
        
        <!--
        <div>
            <input type="button" title="Send content to the server (it will only show accepted parameters)" onclick="save()" class="button" value="Save2">
            <span class="message_line">Save content of the first table (JSON format)</span>
        </div>
        -->
<script>
/* enable strict mode */
"use strict";

var redipsInit,			// define redipsInit variable
    toggleAnimation,	// enable / disable animation
    startPositions,		// remember initial positions of DIV elements
    pos = {},			// initial positions of DIV elements
    rd = REDIPS.drag;	// reference to the REDIPS.drag lib

// General configurations
REDIPS.drag.shift.animation = true;

// redips initialization
redipsInit = function () {
    // initialization
    rd.init();
    
    window.rd = rd; 
    
    // enable shift animation
    rd.shift.animation = false;
    // save initial DIV positions to "pos" object (it should go after initialization)
    startPositions();
    // in a moment when DIV element is moved, set drop_option property (shift or single)
    rd.event.moved = function () {
        // find parent table of moved element
        var tbl = rd.findParent('TABLE', rd.obj);
        // if table id is table1
        if (tbl.id === 'table1') {
            rd.dropMode = 'switch';
        }
        else {
            rd.dropMode = 'single';
        }
        
        // Show every remove button
        $("#table1 a").show();
    };
    // when DIV element is double clicked return it to the initial position
    rd.event.dblClicked = function () {
        // set dblclicked DIV id
        var id = rd.obj.id;
        // move DIV element to initial position
                
        var str = $("#"+id).attr("backToBase");
        if (typeof(str) != "undefined"){
            window.backToBase = $.parseJSON(str);
            // console.log(x);//cast as an array...  
            rd.moveObject({
                    id: id,			// DIV element id
                    target: window.backToBase,	// target position
                    callback: save
            });     
        } else {
            rd.moveObject({
                    id: id,			// DIV element id
                    target: pos[id],	// target position
                    callback: save
            });
        }
        $("#remove"+id).hide();
    };
};

function removeObject(id){
    window.rd.moveObject({
            id: id,			// DIV element id
            target: [1, 1, 0],	// target position
            callback: save
        });    
    $("#remove"+id).hide();
}

// function scans DIV elements and saves their positions to the "pos" object
startPositions = function () {
    // define local varialbles
    var divs, id, i, position;
    // collect DIV elements from dragging area
    divs = document.getElementById('redips-drag').getElementsByTagName('div');
    // open loop for each DIV element
    for (i = 0; i < divs.length; i++) {
        // set DIV element id
        id = divs[i].id;
        // if element id is defined, then save element position 
        if (id) {
            // set element position
            position = rd.getPosition(divs[i]);
            // if div has position (filter obj_new) 
            if (position.length > 0) {
                pos[id] = position;
            }
        }
    }
};

// add onload event listener
if (window.addEventListener) {
    window.addEventListener('load', redipsInit, false);
}
else if (window.attachEvent) {
    window.attachEvent('onload', redipsInit);
}

rd.event.dropped = function () {
   $("#table1 a").show();
   save();
}

// show prepared content for saving
function save() {
    // define table_content variable
    var table_content;
    // prepare table content of first table in JSON format or as plain query string (depends on value of "type" variable)
    table_content = REDIPS.drag.saveContent('table1', 'json');
    // if content doesn't exist
    if (!table_content) {
        console.log('Table is empty!');
    }
    // display query string
    else {
        // window.open('/my/multiple-parameters-json.php?p=' + table_content, 'Mypop', 'width=350,height=260,scrollbars=yes');
        // window.open('private/kiosk/{kioskKey}/proposals/store?p=' + table_content, 'Proposals', 'width=350,height=260,scrollbars=yes');
        $.post('{{ URL::action('KiosksController@storeProposals', $kiosk->kiosk_key) }}',
            {
                _token: "{{ csrf_token() }}",
                proposals: table_content
            },
            function (data) {
               console.log('data '+data);
            })
            .done(function ($result) {
                console.log($result);
            })
            .fail(function () {
                console.log('failed');
            });        
    }
}
</script>

<style>
#table1 td{
    width: 500px;
}    
    
/* container for the left table */
#main_container #left {
    float: left;
}

/* container for the right table */
#main_container #right {
    width: 100px;
    height: 200px;
    /* align container to the right */
    margin-left: auto;
}

/* drag objects (DIV elements inside table cells) */
.redips-drag {
    cursor: move;
    margin: auto;
    z-index: 10;
    background-color: white;
    text-align: center;
    opacity: 0.7;
    filter: alpha(opacity=70);
    /* without width, IE6/7 will not apply filter/opacity to the element ?! */
    /* IE needs element layout */
    height: 35px;
    line-height: 35px;
    /* round corners */
    border-radius: 4px; /* Opera, Chrome */
    -moz-border-radius: 4px; /* FF */
}

/* drag area */
#redips-drag {
    display: table;
}

/* table cells */
div#redips-drag td {
    height: 50px;
    text-align: center;
    font-size: 10pt;
    padding: 2px;
}

/* styles for left table */
#table1, #table2 {
    background-color: #eee;
    border-collapse: collapse;
}

/* border for table1 */
#table1 td {
    border: 1px solid #DDC5B5;
}

/* green objects */
.green {
    border: 2px solid #499B33;
}

/* orange objects */
.orange {
    border: 2px solid #BF6A30;
}

/* message line */
#message {
    color: white;
    background-color: #aaa;
    text-align: center;
    margin-top: 10px;
}

/* cells from the right table (forbidden cells) */
.redips-mark {
    color: #444;
    background-color: #e0e0e0;
}

.proposal{
    width:300px;
    margin: 10px;
}

.btn-redisp-remove{
    font-size: 9px;
    margin-top: -4px;
}

.ellipsis{
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.proposalItem{
    padding-left: 24px;
}
</style>