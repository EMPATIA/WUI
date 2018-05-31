@if(ONE::actionType('node') != 'show')
    <div class="card-header">
        <i>{!! trans("privateCbs.add_parameter") !!}</i>
        <a href="javascript:addNewParameterModal()" class="btn btn-flat btn-success btn-sm pull-right" title="Create parameter"  data-original-title="Create parameter">
            <i class="fa fa-plus"></i>
        </a>
    </div>

@endif

<div id="parametersGroup">
    <!-- Parameters -->
    <div class="dd" id="nestable">
        <ol id="parametersOrderedList" class="dd-list">
            @if(isset($parameters))
                @foreach($parameters as $parameter)
                    <li id="parameterItemId_{{$parameter->id}}" class="dd-item nested-list-item">
                        <div class="dd-handle nested-list-handle">
                            <span class="glyphicon glyphicon-move"></span>
                        </div>
                        <div class="nested-list-content">
                            <a id="parameterItemName_{{$parameter->id}}" onclick="showParameter({{$parameter->id}})" style="cursor:pointer;" >{!! $parameter->parameter !!}</a>
                            <div class="pull-right">
                                <a style="margin-top:-6px;margin-right:2px;color: #fffbfe;" class="btn btn-flat btn-info btn-sm" onclick="showParameter({{$parameter->id}})" ><i class="fa fa-eye"></i></a>
                                @if(ONE::actionType('node') != 'show')
                                    <a style='margin-top:-6px;' onclick="removeParameterCache({{$parameter->id}})"  class='btn btn-flat btn-danger btn-sm' data-toggle='tooltip' data-delay='{&quot;show&quot;:&quot;1000&quot;}' title='Delete' data-original-title='Delete'><i class='fa fa-remove'></i></a>
                                @endif
                            </div>
                        </div>
                    </li>
                @endforeach
            @endif
        </ol>
    </div>
</div>


<!-- Parameters Itens Id -->
<input id="parameterItensIds" name="parameterItensIds" value="" type="hidden"  />

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

    var parameterCounter = 0;

    $("#parameterItensIds").val("");


    function addNewParameterModal(){
        parameterCounter = parameterCounter+1;
        $.ajax({
            url: '{{action("MPCbsController@addModalParameter")}}',
            method: 'POST',
            data: {
                parameterCounter: parameterCounter,
                _token: "{{ csrf_token()}}"
            },
            success: function(response){
                // Check if response is what expected
                if(response.indexOf("modalAddParameter") >= 0){
                    $("#modalGroup").append(response);
                    $('#modalAddParameter'+parameterCounter).modal('show');
                    // $(".parametersGroupDiv").slideDown();
                } else {
                    // You aren't currently logged
                    location.reload();
                }
            },
            error: function(msg){
                console.log(msg);
            }
        });

    }



    function addNewParameterTemplate2(parameterCounter){
        $.ajax({
            url: '{{action("MPCbsController@addParameterTemplateSelection")}}',
            method: 'POST',
            data: {
                parameterCounter: parameterCounter,
                _token: "{{ csrf_token()}}"
            },
            success: function(response){
                // Check if response is what expected
                if(response.indexOf("parametersGroupDiv") >= 0){
                    $("#modalAddParameterBody"+parameterCounter).append(response);
                    $(".parametersGroupDiv").slideDown();
                    $(".modalAddParameterButtons").hide();
                } else {
                    // You aren't currently logged
                    location.reload();
                }
            },
            error: function(msg){
                console.log(msg);
            }
        });
    }

    function addNewParameterItem2(parameterCounter){
        $.ajax({
            url: '{{action("MPCbsController@addParameter")}}',
            method: 'POST',
            data: {
                parameterCounter: parameterCounter,
                _token: "{{ csrf_token()}}"
            },
            success: function(response){
                // Check if response is what expected
                if(response.indexOf("parametersGroupDiv") >= 0){
                    $("#modalAddParameterBody"+parameterCounter).append(response);
                    $(".parametersGroupDiv").slideDown();
                    $(".modalAddParameterButtons").hide();
                } else {
                    // You aren't currently logged
                    location.reload();
                }
            },
            error: function(msg){
                console.log(msg);
            }
        });
    }

    function removeOption(parameterCounter,id){

        var numInputs = 0;
        var optionsDiv = $('.newOptionsDiv_'+parameterCounter);
        if(optionsDiv.length > 0){
            numInputs = $('#'+optionsDiv[1].id +' :input').size();
        }
        if(numInputs <2){
            toastr.error("{!! preg_replace( "/\r|\n/", "", htmlentities(trans("privateCbs.one_option_required!"),ENT_QUOTES)) !!}");
            return false;
        }
        $('.opt_'+parameterCounter+'_'+id).remove();
        return true;
    }

    function addNewOption(parameterCounter,val) {
//Default Values
        val = typeof(val) != 'undefined' ? val : '';

        var newOptionsDiv = $('.newOptionsDiv_'+parameterCounter);
        var optionIndex = 1;
        if(newOptionsDiv.length > 0){
            optionIndex = $('#'+newOptionsDiv[1].id +' > div').size() + 1;
        }

        for(var i=0 ;i< newOptionsDiv.length; i++){
            var optDivId = newOptionsDiv[i].id;
            var required = "notRequired";
            var optRequired = $('#'+newOptionsDiv[i].id).hasClass( "required" );
            if(optRequired){
                required = "required";
            }
            var lang = optDivId.replace('newOptionsDiv_'+parameterCounter+'_','');
            var html = '';
            html += '<div class="col-md-3 opt_'+parameterCounter+'_'+optionIndex+'" id="">';
            html += '<div class="btn-group" style="margin-top:5px;margin-bottom:10px;">';
            html += '<input class="form-control" id="optionsNew_'+parameterCounter+'" placeholder="{!! trans("privateCbs.option_value") !!}" value="'+val+'" '+required+' name="optionsNew_'+parameterCounter+'['+optionIndex+']['+lang+']" type="text">';
            html += '</div>';
            html += '<div class="btn-group" style="margin-top:5px;margin-bottom:10px;">';
            html += '<a style="margin-left:6px;" class="btn btn-flat btn-danger btn-sm" onclick="removeOption('+parameterCounter+','+optionIndex+')" data-original-title="Delete"><i class="fa fa-remove"></i></a>';
            html += '</div>';
            html += '</div>';
            $(html).appendTo(newOptionsDiv[i]);
        }
        optionIndex++;
    }

    function selectNewParameterType(parameterCounter,id,isTemplate) {
// TODO:add and remove required attribute from options
        var numInputs = $('#newOptionsDiv_'+parameterCounter+' :input').size();

        isTemplate = typeof(isTemplate) != 'undefined' ? isTemplate : false;

        $("#parameterStep4_"+parameterCounter).addClass( "disabled" );

        if( isParameterWithOptions(parameterCounter) ){
            $("#parameterNextStep_"+parameterCounter+"_3").show();
        } else {
            $("#parameterNextStep_"+parameterCounter+"_3").hide();
        }


        if (id != '') {

            switch (id) {
                case 'text':
                case 'text_area':
                    $("#parameter_minMaxChars_div_"+parameterCounter).show();
                    $(".parameterOptionsDiv_"+parameterCounter).hide();
                    $("#uploadImageCb_"+parameterCounter).hide();
                    $("#imageMapGroup_"+parameterCounter).hide();
                    // $("#newOptionsDiv").empty();
                    $("#parameterStep4_"+parameterCounter).addClass( "disabled" );
                    break;
                case 'category':
                case 'budget':
                case 'radio_buttons':
                case 'check_box':
                case 'dropdown':
                    $(".parameterOptionsDiv_"+parameterCounter).show();
                    $("#parameter_minMaxChars_div_"+parameterCounter).hide();
                    $("#uploadImageCb_"+parameterCounter).hide();
                    $("#imageMapGroup_"+parameterCounter).hide();
                    //$("#newOptionsDiv").empty();
                    if(isTemplate == false && numInputs == 0){
                        addNewOption(parameterCounter);
                    }
                    $("#parameterStep4_"+parameterCounter).removeClass( "disabled" );
                    break;
                case 'image_map':
                    $("#uploadImageCb_"+parameterCounter).hide();
                    $("#parameter_minMaxChars_div_"+parameterCounter).hide();
                    $(".parameterOptionsDiv_"+parameterCounter).hide();
                    // $("#newOptionsDiv").empty();
                    $("#imageMapGroup_"+parameterCounter).show();
                    break;
                default:
                    $("#parameter_minMaxChars_div_"+parameterCounter).hide();
                    $(".parameterOptionsDiv_"+parameterCounter).hide();
                    $("#uploadImageCb_"+parameterCounter).hide();
                    $("#imageMapGroup_"+parameterCounter).hide();
                    //$("#newOptionsDiv").empty();
                    break;
            }
            $("#parameter_name_div_"+parameterCounter).slideDown();
        }
        else{
            $("#parameter_name_div_"+parameterCounter).hide();
            $("#parameter_minMaxChars_div_"+parameterCounter).hide();
            $(".parameterOptionsDiv_"+parameterCounter).hide();
            $("#uploadImageCb_"+parameterCounter).hide();
        }
    }


    function removeParameter(parameterCounter){
        var parameterItensIds = $("#parameterItensIds").val();

        if(parameterItensIds!=""){
            var arrayIds = parameterItensIds.split(",");
            var strParameters = "";

            for(i = 0; i < arrayIds.length; i++){
                if( arrayIds[i]!=parameterCounter ) {
                    strParameters += arrayIds[i]+",";
                }
            }

            if(strParameters!=""){
                strParameters = strParameters.substring(0, strParameters.length-1);
            }

            $("#parameterItensIds").val(strParameters);
        }

        $("#parametersGroupDiv_"+parameterCounter).remove();
    }

    function changeParameterTitle(parameterCounter){
        if($("#parameterName_"+parameterCounter).val()!="") {
            $("#cbParameterTitle_"+parameterCounter).html( $("#parameterName_"+parameterCounter).val() );
        }else {
            $("#cbParameterTitle_"+parameterCounter).html("{!! trans("privateCbs.parameter") !!}");
        }
    }

    function removeOptionSelect(parameterCounter,id){
        /*
         var numInputs = $('#newOptionsDiv_'+parameterCounter+' :input').size();
         if(numInputs <4){
         toastr.error('One option required!', '', {timeOut: 1000,positionClass: "toast-bottom-right"});
         return false;
         }
         $('#optSelect_'+id).remove();
         return true;
         */
    }


    function chooseTemplate(template,parameterCounter){
        $.ajax({
            url: '{{action("MPCbsController@addParameterTemplate")}}',
            method: 'POST',
            data: {
                parameterCounter: parameterCounter,
                template: template,
                _token: "{{ csrf_token()}}"
            },
            success: function(response){
                if(response.indexOf("parameterItem") >= 0){
                    $("#template_selected_"+parameterCounter).html(response);
                    $(".parametersGroupDiv").slideDown();
                } else {
                    // You aren't currently logged
                    location.reload();
                }
            },
            error: function(msg){
                console.log(msg);
            }
        });

    }

    function isParameterWithOptions(counter){
        var parameterType = $("#paramTypeSelect_"+counter).val();
        return ( parameterType == "radio_buttons" ||
        parameterType  == "check_box" ||
        parameterType == "dropdown" ||
        parameterType == "category" ||
        parameterType == "budget" );
    }


    function addParameter2List(counter){
        name = $("#parameterName_"+counter).val();

        var submitOk = true;
        var booleanVar = isParameterWithOptions(counter);
        if(booleanVar) {
            $(".newOptionsDiv_"+counter+" input").each(function(){
                var optRequired = $(this).closest(".newOptionsDiv_"+counter).hasClass( "required" );
                if(optRequired && $(this).val()== ""){
                    toastr.error("{!! preg_replace( "/\r|\n/", "", htmlentities(trans("privateCbs.please_fill_all_options_on_tab!"),ENT_QUOTES)) !!} #4!");
                    submitOk = false;
                    return false;
                }
            });
        }

        if(submitOk == true){
// parameterItensIds array
            var parameterItensIds = $("#parameterItensIds").val();
            var arrayIds = parameterItensIds.split(",")
            if(arrayIds.indexOf( counter.toString()  ) ==  -1){
                parameterItensIds = (parameterItensIds == '') ? parameterCounter : (parameterItensIds+","+parameterCounter);
                $("#parameterItensIds").val( parameterItensIds );
            }
            if ($("#parameterItemId"+counter).length){
                $("#parameterItemName"+counter).html(name);
            } else {
                buttons = '<a style="margin-top:-6px;margin-right:2px;color: #fffbfe;" class="btn btn-flat btn-info btn-sm" onclick="javascript:showModalParameter('+counter+')" ><i class="fa fa-eye"></i></a> ';
                buttons += "<a href='javascript:removeParameterItem("+counter+")' style='margin-top:-6px;'  class='btn btn-flat btn-danger btn-sm' data-toggle='tooltip' data-delay='{&quot;show&quot;:&quot;1000&quot;}' title='Delete' data-original-title='Delete'><i class='fa fa-remove'></i></a>";
                $("#parametersOrderedList").append('<li id="parameterItemId'+counter+'" class="dd-item nested-list-item"><div class="dd-handle nested-list-handle"><span class="glyphicon glyphicon-move"></span></div><div class="nested-list-content"><a id="parameterItemName'+counter+'" onclick="javascript:showModalParameter('+counter+')" style="cursor:pointer;" >'+name+'</a><div class="pull-right">'+buttons+'</div></div></li>');
            }
            $('#modalAddParameter'+counter).modal('hide');
        }
    }


    function removeParameterItem(parameterCounter){
        $("#parameterItemId"+parameterCounter).detach();
        $("#modalAddParameter"+parameterCounter).detach();

        var parameterItensIds = $("#parameterItensIds").val();

        if(parameterItensIds!=""){
            var arrayIds = parameterItensIds.split(",");
            var strParameters = "";

            for(i = 0; i < arrayIds.length; i++){
                if( arrayIds[i]!=parameterCounter ) {
                    strParameters += arrayIds[i]+",";
                }
            }

            if(strParameters!=""){
                strParameters = strParameters.substring(0, strParameters.length-1);
            }

            $("#parameterItensIds").val(strParameters);
        }
    }

    function showParameter(parameterId){
        $.ajax({
            url: '{{action("MPCbsController@getParameter")}}',
            method: 'POST',
            data: {
                parameter_id: parameterId,
                operator_key: '{{$operator->operator_key ?? null}}',
                _token: "{{ csrf_token()}}",
                action_type: "{{ONE::actionType('node')}}"
            },
            success: function(response){
                if(response != 'false'){
                    $("#modalAddParameterBody").empty();
                    $("#modalAddParameterBody").append(response);
                    $(".parametersGroupDiv").slideDown();
                    $('#modalParameterUpdate').modal('show');

                } else {
                    // You aren't currently logged
//                    location.reload();
                }
            },
            error: function(msg){
                console.log(msg);
            }
        });
    }

    function removeParameterCache(parameterId){
        $.ajax({
            url: '{{action("MPCbsController@removeParameterCache")}}',
            method: 'POST',
            data: {
                parameter_id: parameterId,
                operator_key: "{{$operator->operator_key ?? null}}",
                _token: "{{ csrf_token()}}"
            },
            success: function(response){
                if(response != 'false'){
                    $("#parameterItemId_"+parameterId).detach();
                }
            },
            error: function(msg){
                console.log(msg);
            }
        });
    }
    function showModalParameter(parameterCounter){
        $('#modalAddParameter'+parameterCounter).modal('show');
    }
</script>