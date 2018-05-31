<script>
    $("body").keydown(function(e) {
        if(e.keyCode == 37) { // left
            $('#leftBtn').trigger('click');
        }
        else if(e.keyCode == 39) { // right
            $('#rightBtn').trigger('click');
        }
    });
</script>