function goSidebar(elem, link){
    $(".main-sidebar1").css('opacity', '1')
    $(".main-sidebar1").css('z-index', 100)

    //Fetch the content of sub-menu
    $.ajax({
        url: 'link',
        data: {name: elem.id},
        type: 'post',
        success: function (response) {
            if (response !== null) {
                $(".main-sidebar1").html(response);

//            $(".main-sidebar1").show();


                $(".main-sidebar").animate({
                        left: "-=230",
                    }
                );
            }
        },
        error: function () {
        },
        complete: function () {
        }
    })
}

function getSidebar(link, url, params, view)
{
    $.ajax({
        url: link,
        data: {'url' : url, 'paramsToSidebar': params, 'view': view},
        type: 'post',
        success: function (response) {
            if (response !== null) {
                $(".main-sidebar").html(response);

            }
        },
        error: function () {
        },
        complete: function () {
        }
    })
}

$('.collapse')
    .on('shown.bs.collapse', function() {
        $(this)
            .parent()
            .find(".fa-chevron-down")
            .removeClass("fa-chevron-down")
            .addClass("fa-chevron-up");
    })
    .on('hidden.bs.collapse', function() {
        $(this)
            .parent()
            .find(".fa-chevron-up")
            .removeClass("fa-chevron-up")
            .addClass("fa-chevron-down");
    });