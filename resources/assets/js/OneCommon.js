// Form Actions 
function oneDelete(action) {
    var ele = document.getElementById("delete-modal");
    if (ele != null)
        ele.outerHTML="";
    $.get(action, function (data) {
        $('<div id="delete-modal" class="modal fade">' + data + '</div>').modal();
        $(window).on('shown.bs.modal', function() {
            $("#delete-modal").find("script").each(function(i) {
                eval($(this).text());
            });
            console.log("oneDelete @ shown.bs.modal");
        });
    });
}

function oneActivate(action) {
    var ele = document.getElementById("activate-modal");
    if (ele != null)
        ele.outerHTML="";

    $.get(action, function (data) {
        $('<div id="activate-modal" class="modal fade">' + data + '</div>').modal();
        $(window).on('shown.bs.modal', function() {
            $("#delete-modal").find("script").each(function(i) {
                eval($(this).text());
            });
            console.log("oneActivate @ shown.bs.modal");
        });
    });
}

// Reload Date and Time Pickers
function loadDatePickers(){
    $('.oneDatePicker').datepicker({
        format: 'yyyy-mm-dd',
        orientation: 'bottom auto',
        autoclose: true,
        todayHighlight: true
    });
}

function loadTimePickers(){
    $('.oneTimePicker').clockpicker({
        default: 'now',
        donetext: "OK"
    });
}

function loadDateRangePickers(){
    $(".input-daterange").datepicker({
        format: 'yyyy-mm-dd',
        orientation: 'bottom auto',
    });
}

function convertTimezone(){
    /*
     convert and show timestamps in the user timezone
     */
    $('.convertTimezone').each(function(){
        var offset = new Date().getTimezoneOffset();
        var diference = offset/60;
        var format = $(this).attr("data-format");
        var writeDate = '';

        var signal = '+';
        if (diference > 0){
            signal = '-'
        }

        var myDate = new Date($(this).attr("data-timestamp") * 1000);

        if (!format){
            writeDate = myDate.getDate() + '-' + (myDate.getMonth()+1) + '-' + myDate.getFullYear() + ' ' + myDate.getHours() + ':' + myDate.getMinutes();
        } else {
            var year = myDate.getFullYear();
            var month = (myDate.getMonth()+1);
            var day = myDate.getDate();

            var elements = format.toLowerCase().split("-");
            var dateFormated = [];

            for (var i = 0; i < elements.length; i++) {
                switch(elements[i]) {
                    case 'y':
                        dateFormated[i] = year;
                        break;
                    case 'm':
                        dateFormated[i] = month;
                        break;
                    case 'd':
                        dateFormated[i] = day;
                        break;
                    default:
                        break
                }
            }
            writeDate = dateFormated[0] + '-' + dateFormated[1] + '-' + dateFormated[2] + ' ' + myDate.getHours() + ':' + myDate.getMinutes();
        }
        $(this).html(writeDate+'  GMT '+ signal +' '+ Math.abs(diference));
    });
}

// Load date and Time Pickers
$( document ).ready(function() {
    // Load Date Pickers
    loadDatePickers();
    loadTimePickers();
    loadDateRangePickers();
    //Load timezone converter
    convertTimezone();
});