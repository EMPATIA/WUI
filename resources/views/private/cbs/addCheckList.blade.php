    <!-- Cb Check list -->
<div class="newCheckboxList" style="margin-top:13px">
    <div class="input-group" id="addCheckList">
        <div class="input-group-prepend">
            <div class="input-group-text">
                <input type="checkbox" name="checkList_checkbox[]" id="checkList_checkbox" value="none" onClick="checkChangedNewItem(this)" >
            </div>
        </div>
            <input type="text" class="form-control addText" value="" name="checkList_text[]" required>
        <div class="input-group-append">
            <button type="button" class="btn btn-outline-secondary dropdown-toggle dropdown-toggle-split append_state" data-toggle="dropdown" aria-haspopup="true" name="none" aria-expanded="false">
                {{trans("privateCbs.state")}}
            </button>
            <a class="btn btn-flat btn-danger btn-sm" onclick="removeNewCheckList(this);return false;" data-original-title="Delete"><i class="fa fa-remove"></i></a>
            <div class="dropdown-menu" >
                <a class=" dropdown-item" onclick="selectNewItem(this)" name="none">{{trans("privateCbs.none")}}</a>
                <a class=" dropdown-item" onclick="selectNewItem(this)" name="done">{{trans("privateCbs.done")}}</a>
                <a class=" dropdown-item" onclick="selectNewItem(this)" name="toDo">{{trans("privateCbs.toDo")}}</a>
            </div>
        </div>
    </div>
</div>
