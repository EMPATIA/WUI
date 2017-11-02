<div class="box-body">
    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-6" style="padding-top: 2%">
            <input class="btn btn-success" id="btnStart" type="button" value="Iniciar Votação" onclick="openVotes();"/>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-6" style="padding-top: 2%">
            <input class="btn btn-danger" id="btnStop" type="button" value="Fechar Votação" onclick="closeVotes();"/>
        </div>
    </div>
</div>



<script>
    function openVotes(){
        $.ajax({
            method: 'POST', // Type of response and matches what we said in the route
            url: '{{action('EmpavillePresentationController@openVotes')}}', // This is the url we gave in the route
            data: { _token: "{{ csrf_token() }}", cbKey:"{{$cbKey}}"}, // a JSON object to send back
            success: function (object) { // What to do if we succees
                if(object == 'true') {
                    toastr.success('Votes open!', '', {timeOut: 1000,positionClass: "toast-bottom-right"});
                }
            },
            error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
            }
        });
    }

    function closeVotes() {
        $.ajax({
            method: 'POST', // Type of response and matches what we said in the route
            url: '{{action('EmpavillePresentationController@closeVotes')}}', // This is the url we gave in the route
            data: {_token: "{{ csrf_token() }}", cbKey: "{{$cbKey}}"}, // a JSON object to send back
            success: function (object) { // What to do if we succees
                if (object == 'true') {
                    toastr.success('Votes closed!', '', {timeOut: 1000, positionClass: "toast-bottom-right"});
                }
                else {
                    $('#btnCloseVotes').attr("disabled", false);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                $('#btnCloseVotes').attr("disabled", false);
                console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
            }
        });
    }
</script>
