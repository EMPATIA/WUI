<!-- Modal Dialog -->
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">{!! $title or trans("form.modal_delete_title") !!}</h4>
        </div>
        <div class="modal-body">
            <p>{!! $msg or "form.modal_delete_msg" !!}</p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default"
                    data-dismiss="modal">{!! $btn_ko or "form.modal_delete_cancel" !!}</button>
            <button type="button" class="btn btn-danger"
                    id="confirm">{!! $btn_ok or "form.modal_delete_aprove" !!}</button>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {


        $(document.getElementById('delete-modal')).find('.modal-footer #confirm').on('click', function () {
            $.ajax({
                url: '{{ $action }}',
                type: 'GET',
                data: {_token: '{{csrf_token()}}'},
                success: function (action) {
                    window.location = action;
                },
                error: function (data) {
                    //TODO Deal with the error!
                }
            });
            $(document.getElementById('delete-modal')).modal("hide");
        });
    });
</script>