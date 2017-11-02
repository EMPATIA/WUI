<!-- Modal Parameter -->
<div class="modal fade" id="modalAddParameter{{ $parameterCounter }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="card-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">{{ trans("privateCbs.addParameter") }}</h4>
            </div>
            <div class="modal-body">
                <div id="modalAddParameterButtons" class="row modalAddParameterButtons">      
                    <div class="col-12">
                        <span>{{ trans("privateCbs.addParameterChooseOption") }}</span>
                        <br/><br/>
                    </div>
                    <div class="col-12 col-md-6">
                        <a href="javascript:addNewParameterItem2({{ $parameterCounter }})" class="btn btn-flat btn-success btn-sm" title="Create parameter"  data-original-title="Create parameter">
                            <i class="fa fa-plus"></i> {{ trans("privateCbs.addParameter") }}
                        </a>               
                        <span class="form-text oneform-help-block" style="margin:6px 0px 0px;font-size:10px;">{{ trans("privateCbs.helpAddParameter") }}</span>
                    </div>
                    <div class="col-12 col-md-6">
                        <a href="javascript:addNewParameterTemplate2({{ $parameterCounter }})" class="btn btn-flat btn-warning btn-sm" title="Create template"  data-original-title="Create template" style="margin-right:20px;">
                            <i class="fa fa-plus"></i> {{ trans("privateCbs.addParameterTemplate") }}
                        </a>
                        <span class="form-text oneform-help-block" style="margin:6px 0px 0px;font-size:10px;">{{ trans("privateCbs.helpAddParameterTemplate") }}</span>
                    </div>                        
                </div>    
                <div id="modalAddParameterBody{{ $parameterCounter }}">

                </div>
            </div>
            <div class="modal-footer">
                <button  type="button" style='margin-left:10px;display:none;' class="btn btn-primary pull-right col-sm-2 "
                        id="modalAddParameterButton{{ $parameterCounter }}" onclick="addParameter2List({{ $parameterCounter }});" style="display:none;" >{{ trans("privateCbs.add") }}
                </button>
                <button type="button" class="btn btn-secondary col-sm-2 pull-right" data-dismiss="modal"
                        id="frm_cancel">{{ trans("privateCbs.close") }}
                </button>
            </div>
        </div>
    </div>
</div>