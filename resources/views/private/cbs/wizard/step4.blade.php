<div class="card-header card-header-gray">
    <!-- Add new parameter template option  ('type' => $type ) -->
    <i>{!! trans("privateCbs.addVote") !!}</i>
    <a href="#" onclick="votesView()" class="btn btn-flat btn-info2 btn-sm margin-right-5 pull-right" title="Create vote"  data-original-title="Create vote">
        <i class="fa fa-plus"></i>
    </a>
</div>

<div id="voteGroup">
    <!-- Votes -->
    <div class="dd" id="nestable">
        <ol id="votesOrderedList" class="dd-list">
            @if(isset($cbVotes))
                @foreach($cbVotes as $vote)
                    <li id="voteItemId_{{$vote->vote_key}}" class="dd-item nested-list-item">
                        <div class="dd-handle nested-list-handle">
                            <span class="glyphicon glyphicon-move"></span>
                        </div>
                        <div class="nested-list-content">
                            <a id="voteItemName_{{$vote->vote_key}}" onclick="showVote('{{$vote->vote_key}}')" style="cursor:pointer;" >{!! $vote->vote_method !!}</a>
                        </div>
                    </li>
                @endforeach
            @endif
        </ol>
    </div>
</div>

<input id="voteItensIds" name="voteItensIds" value="" type="hidden" />

<script>

    toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": false,
        "progressBar": false,
        "positionClass": "toast-bottom-right",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "0",
        "hideDuration": "0",
        "timeOut": "0",
        "extendedTimeOut": "0",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    };

    var voteCounter = 0;

    $("#voteItensIds").val("");

    function votesView(){
        @if(isset($cbKey))
        $.ajax({
            url: '{{action("CbsController@moderateRouting", ['type' => $type, 'action' => 'create', 'step' => 'votes'])}}',
            method: 'get',
            data: {
                cbKey: '{{ $cbKey }}',
                _token: "{{ csrf_token()}}"
            },
            success: function(response){
                window.location.href = response;
            },
            error: function(msg){
                console.log(msg);
            }
        });
        @else
$.ajax({
            url: '{{action("CbsController@moderateRouting", ['type' => $type, 'action' => 'create', 'step' => 'votes'])}}',
            method: 'get',
            data: {
                cbKey: $("#cb_key").text(),
                _token: "{{ csrf_token()}}"
            },
            success: function(response){
                window.location.href = response;
            },
            error: function(msg){
                console.log(msg);
            }
        });
        @endif
    }

    function showVote(key){
        @if(isset($cbKey))
        $.ajax({
            url: '{{action("CbsController@moderateRouting", ['type' => $type, 'action' => 'show', 'step' => 'votes'])}}',
            method: 'get',
            data: {
                cbKey: '{{ $cbKey }}',
                voteKey: key,
                _token: "{{ csrf_token()}}"
            },
            success: function(response){
                window.location.href = response;
            },
            error: function(msg){
                console.log(msg);
            }
        });
        @else
$.ajax({
            url: '{{action("CbsController@moderateRouting", ['type' => $type, 'action' => 'show', 'step' => 'votes'])}}',
            method: 'get',
            data: {
                cbKey: $("#cb_key").text(),
                voteKey: key,
                _token: "{{ csrf_token()}}"
            },
            success: function(response){
                window.location.href = response;
            },
            error: function(msg){
                console.log(msg);
            }
        });
        @endif
    }

    {{--function addNewVoteModal(){--}}
        {{--voteCounter = voteCounter+1;--}}

        {{--$.ajax({--}}
            {{--url: '{{action("CbsController@addModalVote")}}',--}}
            {{--method: 'POST',--}}
            {{--data: {--}}
                {{--voteCounter: voteCounter,--}}
                {{--_token: "{{ csrf_token()}}"--}}
            {{--},--}}
            {{--success: function(response){--}}
                {{--// Check if response is what expected--}}
                {{--if(response.indexOf("modalAddVote") >= 0){--}}
                    {{--$("#modalGroup").append(response);--}}
                    {{--$('#modalAddVote'+voteCounter).modal('show');--}}
                    {{--$(".votesGroupDiv").slideDown();--}}

                    {{--//--}}
                    {{--$('#modalAddVote'+voteCounter).removeAttr("aria-hidden");--}}
                    {{--loadDatePickers();--}}
                    {{--loadTimePickers();--}}
                    {{--// addNewVoteItem2(voteCounter);--}}
                    {{--// $(".parametersGroupDiv").slideDown();--}}
                {{--} else {--}}
                    {{--// You aren't currently logged--}}
                    {{--location.reload();--}}
                {{--}--}}
            {{--},--}}
            {{--error: function(msg){--}}
                {{--console.log(msg);--}}
            {{--}--}}
        {{--});--}}

    {{--}--}}


    {{--function addNewVoteItem2(voteCounter){--}}
        {{--$.ajax({--}}
            {{--url: '{{action("CbsController@addVote")}}',--}}
            {{--method: 'POST',--}}
            {{--data: {--}}
                {{--voteCounter: voteCounter,--}}
                {{--_token: "{{ csrf_token()}}"--}}
            {{--},--}}
            {{--success: function(response){--}}
                {{--// Check if response is what expected--}}
                {{--if(response.indexOf("votesGroupDiv") >= 0){--}}
                    {{--// voteItensIds array--}}
                    {{--voteItensIds = $("#voteItensIds").val();--}}
                    {{--voteItensIds = (voteItensIds == '') ? voteCounter : (voteItensIds+","+voteCounter);--}}
                    {{--$("#voteItensIds").val( voteItensIds );--}}
                    {{--// Html--}}
                    {{--$("#voteGroup").append(response);--}}
                    {{--$(".votesGroupDiv").slideDown();--}}
                    {{--// Render DatePicker and Time Pickers--}}
                    {{--loadDatePickers();--}}
                    {{--loadTimePickers();--}}
                {{--} else {--}}
                    {{--// You aren't currently logged--}}
                    {{--location.reload();--}}
                {{--}--}}
            {{--},--}}
            {{--error: function(msg){--}}
                {{--console.log(msg);--}}
            {{--}--}}
        {{--});--}}
    {{--}--}}


    {{--function addNewVoteItem(){--}}
        {{--voteCounter = voteCounter+1;--}}

        {{--voteItensIds = $("#voteItensIds").val();--}}
        {{--voteItensIds = (voteItensIds == '') ? voteCounter : (voteItensIds+","+voteCounter);--}}
        {{--$("#voteItensIds").val( voteItensIds );--}}

        {{--$.ajax({--}}
            {{--url: '{{action("CbsController@addVote")}}',--}}
            {{--method: 'POST',--}}
            {{--data: {--}}
                {{--voteCounter: voteCounter,--}}
                {{--_token: "{{ csrf_token()}}"--}}
            {{--},--}}
            {{--success: function(response){--}}
                {{--// Check if response is what expected--}}
                {{--if(response.indexOf("votesGroupDiv") >= 0){--}}
                    {{--$("#voteGroup").append(response);--}}
                    {{--$(".votesGroupDiv").slideDown();--}}
                    {{--// Render DatePicker and Time Pickers--}}
                    {{--loadDatePickers();--}}
                    {{--loadTimePickers();--}}
                {{--} else {--}}
                    {{--// You aren't currently logged--}}
                    {{--location.reload();--}}
                {{--}--}}
            {{--},--}}
            {{--error: function(msg){--}}
                {{--console.log(msg);--}}
            {{--}--}}
        {{--});--}}
    {{--}--}}


    {{--function showMethods( voteCounter ) {--}}
        {{--var idMethodGroup = $('#methodGroupSelect_'+voteCounter).val();--}}

        {{--if (idMethodGroup == "") {--}}
            {{--$('#methodsDiv_'+voteCounter).html("");--}}
            {{--$('#configurationsDiv_'+voteCounter).html("");--}}
            {{--return;--}}
        {{--}--}}
        {{--$.ajax({--}}
            {{--method: 'POST', // Type of response and matches what we said in the route--}}
            {{--url: '{{action('CbsVoteController@getMethodsData')}}', // This is the url we gave in the route--}}
            {{--data: {postId: idMethodGroup, _token: "{{ csrf_token() }}"}, // a JSON object to send back--}}
            {{--success: function (response) { // What to do if we succeed--}}
                {{--// build select with array--}}
                {{--if (response instanceof Array) {--}}
                    {{--var selectObj = '<p></p><select class="form-control" id="methodSelect_'+voteCounter+'" name="methodSelect_'+voteCounter+'" required onchange="getMethodConfigurations('+voteCounter+')">';--}}
                    {{--selectObj += '<option value=""> -- {{ trans("privateCbs.selectMethodTypes") }} -- </option>';--}}
                    {{--for( i = 0; i < response.length ; i++  ){--}}
                        {{--selectObj += '<option  value="'+response[i].id+'">'+response[i].name+'</option>';--}}
                    {{--}--}}
                    {{--selectObj += '</select>';--}}

                    {{--$("#methodsDiv_"+voteCounter).html(selectObj);--}}
                    {{--$('#configurationsDiv_'+voteCounter).html("");--}}
                {{--} else {--}}
                    {{--location.reload();--}}
                {{--}--}}

            {{--},--}}
            {{--error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail--}}
                {{--console.log("AJAX error: " + textStatus + ' : ' + errorThrown);--}}
            {{--}--}}
        {{--});--}}
    {{--}--}}


    {{--function getMethodConfigurations(voteCounter) {--}}
        {{--var idMethod = $('#methodSelect_'+voteCounter).val();--}}
        {{--if (idMethod == "") {--}}
            {{--$('#configurationsDiv_'+voteCounter).html("");--}}
            {{--$('#advancedConfsSelect').attr('disabled');--}}
            {{--return;--}}
        {{--}--}}
        {{--$.ajax({--}}
            {{--method: 'POST', // Type of response and matches what we said in the route--}}
            {{--url: '{{action('CbsVoteController@getMethodConfigurations')}}', // This is the url we gave in the route--}}
            {{--data: {--}}
                {{--postId: idMethod,--}}
                {{--voteId: voteCounter,--}}
                {{--_token: "{{ csrf_token() }}"--}}
            {{--},--}}
            {{--success: function (response) { // What to do if we succeed--}}
                {{--$("#configurationsDiv_"+voteCounter).html(response);--}}
                {{--$('#advancedConfsSelect').removeAttr('disabled');--}}
                {{--if ($('#parameterTypeSelect').length && $('#parameterTypeSelect').val() != "") {--}}
                    {{--//  getAdvancedConfigurations();--}}
                {{--}--}}
            {{--},--}}
            {{--error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail--}}
                {{--console.log("AJAX error: " + textStatus + ' : ' + errorThrown);--}}
            {{--}--}}
        {{--});--}}
    {{--}--}}

    {{--function removeVote(voteCounter){--}}
        {{--var voteItensIds = $("#voteItensIds").val();--}}

        {{--if(voteItensIds!=""){--}}
            {{--var arrayIds = voteItensIds.split(",");--}}
            {{--var strVotes = "";--}}

            {{--for(i = 0; i < arrayIds.length; i++){--}}
                {{--if( arrayIds[i]!=voteCounter ) {--}}
                    {{--strVotes += arrayIds[i]+",";--}}
                {{--}--}}
            {{--}--}}

            {{--if(strVotes!=""){--}}
                {{--strVotes = strVotes.substring(0, strVotes.length-1);--}}
            {{--}--}}

            {{--$("#voteItensIds").val(strVotes);--}}
        {{--}--}}

        {{--$("#votesGroupDiv_"+voteCounter).remove();--}}
    {{--}--}}

    {{--function changeVoteTitle(parameterCounter){--}}
        {{--if($("#voteName_"+parameterCounter).val() != ""){--}}
            {{--$("#cbVoteTitle_"+parameterCounter).html( $("#voteName_"+parameterCounter).val() );--}}
        {{--} else {--}}
            {{--$("#cbVoteTitle_"+parameterCounter).html("{!! trans("privateCbs.vote") !!}");--}}
        {{--}--}}
    {{--}--}}

    {{--function addVote2List(counter){--}}

        {{--if($("#methodGroupSelect_"+counter).val() == ""){--}}
            {{--$("#methodGroupSelect_"+counter).focus();--}}
            {{--toastr.error("{!! preg_replace( "/\r|\n/", "", htmlentities(trans("privateCbs.voteGroupMethodIsRequiredOnTab!"),ENT_QUOTES)) !!} #2!");--}}
            {{--return false;--}}
        {{--}else if($("#methodSelect_"+counter).val() == ""){--}}
            {{--$("#methodSelect_"+counter).focus();--}}
            {{--toastr.error("{!! preg_replace( "/\r|\n/", "", htmlentities(trans("privateCbs.voteMethodIsRequiredOnTab!"),ENT_QUOTES)) !!} #2!");--}}
            {{--return false;--}}
        {{--}--}}

        {{--var voteItensIds = $("#voteItensIds").val();--}}
        {{--var arrayIds = voteItensIds.split(",")--}}
        {{--if(arrayIds.indexOf( counter.toString()  ) ==  -1){--}}
            {{--voteItensIds = (voteItensIds == '') ? voteCounter : (voteItensIds+","+voteCounter);--}}
            {{--$("#voteItensIds").val( voteItensIds );--}}
        {{--}--}}

        {{--name = $("#voteName_"+counter).val();--}}
        {{--if ($("#voteItemId"+counter).length){--}}
            {{--$("#voteItemName"+counter).html(name);--}}
        {{--} else {--}}
            {{--buttons = '<a style="margin-top:-6px;margin-right:2px;color:#fffbfe;" class="btn btn-flat btn-info btn-sm" onclick="javascript:showModalVote('+counter+')" ><i class="fa fa-eye"></i></a> ';--}}
            {{--buttons += "<a href='javascript:removeVoteItem("+counter+")' style='margin-top:-6px;'  class='btn btn-flat btn-danger btn-sm' data-toggle='tooltip' data-delay='{&quot;show&quot;:&quot;1000&quot;}' title='Delete' data-original-title='Delete'><i class='fa fa-remove'></i></a>";--}}
            {{--$("#votesOrderedList").append('<li id="voteItemId'+counter+'" class="dd-item nested-list-item"><div class="dd-handle nested-list-handle"><span class="glyphicon glyphicon-move"></span></div><div class="nested-list-content"><a id="voteItemName'+counter+'" onclick="javascript:showModalVote('+counter+')" style="cursor:pointer;" >'+name+'</a><div class="pull-right"> '+buttons+'</div></div></li>');--}}
        {{--}--}}
        {{--$('#modalAddVote'+counter).modal('hide');--}}
    {{--}--}}

    {{--function removeVoteItem(voteCounter){--}}
        {{--$("#voteItemId"+voteCounter).detach();--}}
        {{--$("#modalAddVote"+voteCounter).detach();--}}

        {{--var voteItensIds = $("#voteItensIds").val();--}}

        {{--if(voteItensIds!=""){--}}
            {{--var arrayIds = voteItensIds.split(",");--}}
            {{--var strVotes = "";--}}

            {{--for(i = 0; i < arrayIds.length; i++){--}}
                {{--if( arrayIds[i]!=voteCounter ) {--}}
                    {{--strVotes += arrayIds[i]+",";--}}
                {{--}--}}
            {{--}--}}

            {{--if(strVotes!=""){--}}
                {{--strVotes = strVotes.substring(0, strVotes.length-1);--}}
            {{--}--}}

            {{--$("#voteItensIds").val(strVotes);--}}
        {{--}--}}

    {{--}--}}

    {{--function showModalVote(voteCounter){--}}
        {{--$('#modalAddVote'+voteCounter).modal('show');--}}
    {{--}--}}
</script>