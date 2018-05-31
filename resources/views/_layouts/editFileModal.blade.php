<!-- Modal Dialog -->
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">{!! trans("cb.editFile") !!}</h4>
        </div>
        <div class="modal-body">
            <div class="form-group "><label for="title">{!! trans("cb.filename") !!}</label><input class="form-control" id="name" name="name" type="text" value="{{isset($file) ? $file->name : ""}}"></div>
            <div class="form-group "><label for="title">{!! trans("cb.description") !!}</label><input class="form-control" id="description" name="description" type="text" value="{{isset($file) ? $file->description : ""}}"></div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default"
                    data-dismiss="modal">{!! trans("cb.cancel") !!}</button>
            <button type="button" class="btn btn-success"
                    id="confirm" onclick="saveFileDetails()">{!! trans("cb.save") !!}</button>
        </div>
    </div>
</div>

<script type="text/javascript">
    function saveFileDetails(){
        $(document.getElementById('fileDetails-modal')).modal("hide");  
        $.ajax({
            url: '{{action("PublicPostController@editFile")}}',
            type: 'POST',
            data: {post_key: '{{isset($post_key) ? $post_key : 0}}', file_id: '{{isset($file->file_id) ? $file->file_id : 0}}', name: $("#name").val(),description: $("#description").val(), _token: '{{csrf_token()}}'},
            success: function (action) {
                updateFilesPostList('#files');
                $(document.getElementById('fileDetails-modal')).remove(); 
            },
            error: function (data) {
                //TODO Deal with the error!
                $(document.getElementById('fileDetails-modal')).remove(); 
                toastr["error"]("Error on file upload!");
            }
        });
    }
</script>