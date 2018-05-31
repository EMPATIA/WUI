$(document).ready(function() {
    $("#iWantMore").click(function(event) {
        event.preventDefault();
        scrollToAnchor('#content-home-anchor');
    });
});
function scrollToAnchor(aSelector){
    $('html,body').animate({scrollTop:  $(aSelector).offset().top},'slow');
}