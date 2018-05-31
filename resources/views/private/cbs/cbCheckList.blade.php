
    <!-- Cb Check list -->
    <hr style="margin: 10px 0 10px 0">
    <p> <b>  {{trans("privateCbs.CheckList")}} </b> </p>
    <!-- button add view to report -->
    <div class="row">
        <div class="col-xl-12 ">
            <div class="col-xs-12">
                <button onclick="addChecklist()" type="button" class="btn btn-flat one-btn-bordered" id="add_Checklist" style="float: right; margin-top:13px;margin-bottom:13px; " data-toggle="tooltip"><i class="fa fa-plus" aria-hidden="true"></i> &nbsp{{trans("form.add") }}</button>
            </div>
        </div>
    </div>
    <br>

    @if(isset($checklists) && !empty($checklists) )
        <div class="row">
            <div class="col-xl-12">
                <div class="col-xl-12" id="add_Checklist">
                    @foreach($checklists as $checklist)
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <input type="checkbox" name="checkboxList_{{ $checklist->checklist_key }}" id="{{$checklist->checklist_key}}"  onClick="checkChanged(this,'{{$checklist->checklist_key}}')" @if($checklist->state == 'done') checked @endif  >
                                </div>
                            </div>
                            <div class="col-xl-10">
                                <span id="check_{{ $checklist->checklist_key }}" @if($checklist->state == 'done')style="text-decoration: line-through"  @endif> {{$checklist->title}} </span>
                            </div>
                            <div class="input-group-append">
                                <button type="button" @if($checklist->state == 'done')style="text-decoration: line-through"  @endif class="btn btn-outline-secondary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    @if($checklist->state == 'none') {{trans("privateCbs.none")}}
                                    @elseif($checklist->state == 'done') {{trans("privateCbs.done")}}
                                    @else {{trans("privateCbs.toDo")}}
                                    @endif
                                </button>
                                <a class="btn btn-flat btn-danger btn-sm" onclick="removeCheckList('{{$checklist->checklist_key}}')" data-original-title="Delete"><i class="fa fa-remove"></i></a>
                                <div class="dropdown-menu" id="state">
                                    <a class=" dropdown-item" onclick="updateChecklistItem('', '{{$checklist->checklist_key}}','none')" name="none">{{trans("privateCbs.none")}}</a>
                                    <a class=" dropdown-item" onclick="updateChecklistItem('', '{{$checklist->checklist_key}}','done')" name="done">{{trans("privateCbs.done")}}</a>
                                    <a class=" dropdown-item" onclick="updateChecklistItem('', '{{$checklist->checklist_key}}','toDo')" name="toDo">{{trans("privateCbs.toDo")}}</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <div class="row">
        <div class="col-xl-12">
            <form id="checkList" class="box-body">
                <div id="addCheckListRow">
                    <hr style="margin: 10px 0 10px 0" id="line">
                </div>
                <div class="col-xs-12">
                    <input type="submit" class="btn-submit" id="submitCheckList" form="checkList" value="{{ trans("privateCbs.submit") }}" style="float: right; margin-top:13px">
                </div>
            </form>
        </div>
    </div>