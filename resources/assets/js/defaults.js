// Set select2 defaults
$( document ).ready(function() {
    $(".select2-default").select2({
        minimumResultsForSearch: Infinity
    });
    $(".select2-searchable").select2();
});