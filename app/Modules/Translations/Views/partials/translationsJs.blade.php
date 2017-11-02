<script>
    $(document).on('click','.remove-translation', function(){
        var key_id = $(this).parent().find('input[id="key_id"]').val();
        var language_id = $(this).parent().find('input[id="language_id"]').val();
        var element = $(this);
        $.ajax({
            method: 'POST', // Type of response and matches what we said in the route
            url: "{{action('\App\Modules\Translations\Controllers\TranslationsController@removeKeyTranslation')}}", // This is the url we gave in the route
            data: {
                "_token": "{{ csrf_token() }}",
                'key_id': key_id,
                'language_id': language_id
            },beforeSend:function (){
                $("#loader").show();
            },success: function (response) { // What to do if we succeed
                element.parent().addClass('empty-translation');
                element.parent().html(response);
                $("#loader").hide();
            }
        });
    });

    $(document).on('click','.empty-translation', function(){
        var key_id = $(this).find('input[id="key_id"]').val();
        var language_id = $(this).find('input[id="language_id"]').val();
        var element = $(this);
        $(this).removeClass('empty-translation');
        $.ajax({
            method: 'POST', // Type of response and matches what we said in the route
            url: "{{action('\App\Modules\Translations\Controllers\TranslationsController@getAddTranslationBox')}}", // This is the url we gave in the route
            data: {
                "_token": "{{ csrf_token() }}",
                'key_id':  key_id,
                'language_id':  language_id
            },success: function (response) { // What to do if we succeed
                element.html(response);
                element.find("input[id=translation]").focus();
            }
        });
    });

    $(document).on('click','.edit-translation', function(event){

        if(!$(event.target).hasClass('fa-trash')){
            var key_id = $(this).find('input[id="key_id"]').val();
            var language_id = $(this).find('input[id="language_id"]').val();
            var element = $(this);
            $(this).removeClass('edit-translation');
            $.ajax({
                method: 'POST', // Type of response and matches what we said in the route
                url: "{{action('\App\Modules\Translations\Controllers\TranslationsController@getEditTranslationBox')}}", // This is the url we gave in the route
                data: {
                    "_token": "{{ csrf_token() }}",
                    'key_id':  key_id,
                    'language_id':  language_id
                },success: function (response) { // What to do if we succeed
                    element.html(response);
                    element.find("input[id=translation]").focus();
                    var $thisVal =  element.find("input[id=translation]").val();
                    element.find("input[id=translation]").val('').val($thisVal);

                }
            });
        }

    });

    $(document).on('click','.cancel-translation', function(){
        var key_id = $(this).parent().find('input[id="key_id"]').val();
        var language_id = $(this).parent().find('input[id="language_id"]').val();
        var element = $(this);
        $.ajax({
            method: 'POST', // Type of response and matches what we said in the route
            url: "{{action('\App\Modules\Translations\Controllers\TranslationsController@reloadTranslation')}}", // This is the url we gave in the route
            data: {
                "_token": "{{ csrf_token() }}",
                'key_id':  key_id,
                'language_id':  language_id
            },success: function (response) { // What to do if we succeed
                element.parent().addClass('edit-translation');
                element.parent().html(response);

            }
        });
    });

    $(document).on('click','.cancel-translation-new', function(){
        var key_id = $(this).parent().find('input[id="key_id"]').val();
        var language_id = $(this).parent().find('input[id="language_id"]').val();
        var element = $(this);
        $.ajax({
            method: 'POST', // Type of response and matches what we said in the route
            url: "{{action('\App\Modules\Translations\Controllers\TranslationsController@reloadTranslation')}}", // This is the url we gave in the route
            data: {
                "_token": "{{ csrf_token() }}",
                'key_id':  key_id,
                'language_id':  language_id
            },success: function (response) { // What to do if we succeed
                element.parent().addClass('empty-translation');
                element.parent().html(response);

            }
        });
    });

    function reload(){
        $.ajax({
            method: 'POST', // Type of response and matches what we said in the route
            url: "{{action('\App\Modules\Translations\Controllers\TranslationsController@getKeys')}}", // This is the url we gave in the route
            data: {
                "_token": "{{ csrf_token() }}",
                'module':  $('#modules').val(),
                'group': $('#groups').val(),
                'states': buildSearchDataStates()
            },beforeSend:function (){
                $("#loader").show();
            },success: function (response) { // What to do if we succeed
                $('#keys-list').html(response);
                $("#loader").hide();
                // Reload Hide Show langs
                reloadHideShowValues();
                storeHideShowLanguagesValues();
            }
        });
    }

    $(document).on('click','#get_all_translations', function(){
        $.ajax({
            method: 'GET', // Type of response and matches what we said in the route
            url: "{{action('\App\Modules\Translations\Controllers\TranslationsController@getAllStrings')}}", // This is the url we gave in the route
            beforeSend:function (){
                $("#loader").show();
            },
            success: function (response) { // What to do if we succeed
                $("#loader").hide();
                toastr.info(response);
                if($('#modules').val() != '' && $('#groups').val() != '')
                    reload();
            },error: function(){
                toastr.error("{{ trans("Translations::translation.get_all_error") }}");
                $("#loader").hide();
            }
        });
    });

    $(document).on('click','#save_all_translations', function(){
        $.ajax({
            method: 'GET', // Type of response and matches what we said in the route
            url: "{{action('\App\Modules\Translations\Controllers\TranslationsController@saveAllStrings')}}", // This is the url we gave in the route
            beforeSend:function (){
                $("#loader").show();
            },
            success: function (response) { // What to do if we succeed
                $("#loader").hide();
                window.location = response;

            },error: function(){
                toastr.error("{{ trans("Translations::translation.save_error") }}");
                $("#loader").hide();
            }
        });
    });

    $(document).on('click','.save-translation', function(){
        var key_id = $(this).parent().find('input[id="key_id"]').val();
        var language_id = $(this).parent().find('input[id="language_id"]').val();
        var translation = $(this).parent().find('input[id="translation"]').val();
        var element = $(this);
        $.ajax({
            method: 'POST', // Type of response and matches what we said in the route
            url: "{{action('\App\Modules\Translations\Controllers\TranslationsController@saveKeyTranslation')}}", // This is the url we gave in the route
            data: {
                "_token": "{{ csrf_token() }}",
                'key_id':  key_id,
                'language_id':  language_id,
                'translation':  translation
            },beforeSend:function (){
                $("#loader").show();
            },success: function (response) { // What to do if we succeed
                if(translation === "") {
                    element.parent().addClass('empty-translation');
                }else{
                    element.parent().addClass('edit-translation');
                }
                element.parent().html(response);
                $("#loader").hide();
            }
        });
    });

    $(document).on('keyup','#translation', function(e){
        var element = $(this);
        if (e.keyCode == 13) {
            element.parent().find('.save-translation').trigger('click');
            var nextRow = element.parent().parent().next('tr');
            nextRow.find('td').eq(element.parent().index()).trigger('click');
        }
        if (e.keyCode == 27) {
            if(element.parent().find('.cancel-translation-new').length !== 0) {
                element.parent().find('.cancel-translation-new').trigger('click');
            }else {
                element.parent().find('.cancel-translation').trigger('click');
            }
        }
    });
    $(document).on('keydown','#translation', function(e){
        var element = $(this);
        if (e.keyCode == 9) {
            e.preventDefault();
            element.parent().find('.save-translation').trigger('click');
            var nextRow = element.parent().parent().next('tr');
            nextRow.find('td').eq(element.parent().index()).trigger('click');
        }
    });
    function buildSearchDataStates(){
        var allValues = {};
        $('input[name="states[]"]:checked').each(function () {
            allValues[$(this).attr('value')] = $(this).val();
        });
        return allValues;
    }
</script>