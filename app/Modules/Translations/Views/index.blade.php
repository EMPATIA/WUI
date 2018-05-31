@extends('private._private.index')

@section('content')

    <div class="box box-primary" style="margin-top: 10px">
        <div class="box-body">
            <div class="form-group">
                <div class="row">
                    @include('Translations::partials.filterState')
                </div>
            </div>
            <div class="form-group translations-button-row">

                <button id="get_all_translations" class="btn btn-flat empatia-dark" >{{trans("Translations::translation.get_all_translations")}}</button>
                <button id="save_all_translations" class="btn btn-flat empatia">{{trans("Translations::translation.save_all_translations")}}</button>
                @if(!isset($restrition))
                    <a class="btn btn-flat btn-preview" href="{!! action('\App\Modules\Translations\Controllers\TranslationsController@manageAllEmptyTranslations') !!}">{{trans("Translations::translation.manage_empty")}}</a>
                @endif
                <i class="fa fa-spinner fa-spin fa-3x fa-fw" id="loader" style="display: none;"></i>
                <span class="sr-only">Loading...</span>
            </div>
            <div class="form-group esenfc-no-padding">
                <select id="modules1" class="esenfc-select form-control" name="modules1" style="width:50%;">
                    <option value=""></option>
                    @foreach($modules as $module)
                        <option value="{{ $module }}">{{ $module }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group esenfc-no-padding">
                <select id="groups1" class="esenfc-select form-control" name="group" style="display: none; width:50%;">
                    <option value=""></option>
                </select>
            </div>
            <div class="form-group esenfc-no-padding">
                <input type="text" id="keys" class="form-control" name="keys" placeholder="{{trans("Translations::translation.filter_by_key")}}" style="width:50%;">

                </input>
            </div>
            <div class="form-group">
                <div id="keys-list" style="overflow-x:auto;">

                </div>
            </div>

        </div>
    </div>
@endsection


@section('scripts')
    <script>
        window.arrayTmpTranslations = [];

        $(document).on('keyup','#keys', function(e){
            if($(this).val().length > 3) {
                $.ajax({
                    method: 'POST', // Type of response and matches what we said in the route
                    url: "{{action('\App\Modules\Translations\Controllers\TranslationsController@searchKey')}}", // This is the url we gave in the route
                    data: {
                        "_token": "{{ csrf_token() }}",
                        'term': $(this).val(),
                    }, beforeSend: function () {
                        $("#loader").show();
                    }, success: function (response) { // What to do if we succeed
                        // Load keys list
                        $('#keys-list').html(response);
                        $("#loader").hide();
                        // Reload Hide Show langs
                        reloadHideShowValues();
                        storeHideShowLanguagesValues();
                    }, error: function () { // What to do if we fail
                        var errorMessage = "{!! trim(preg_replace('/\s\s+/', ' ', trans('Translations::translation.something_went_wrong'))) !!}";
                        toastr.error(errorMessage);
                    }
                });
            } else {
                if($('#modules1').val() != '' && $('#groups1').val() != '') {
                    reload();
                }else{
                    // Empty translations list
                    $('#keys-list').html('');
                }
                // Reload Hide Show langs
                reloadHideShowValues();
                storeHideShowLanguagesValues();
            }
        });


        $("#modules1").select2({
            placeholder: '{{ trans("Translations::translation.selectModule") }}',
            cache: false,
        });

        $('#modules1').change(function() {
            // Load translations list
            $('#keys-list').html('');
            $('#groups1').val([]);
            // Reload Hide Show langs
            reloadHideShowValues();
            storeHideShowLanguagesValues();
            // Initialize Groups
            initializeGroups();
        });

        function initializeGroups() {
            $("#groups1").select2({
                placeholder: '{{ trans("Translations::translation.selectGroup") }}',
                ajax: {
                    "url": '{!! action('\App\Modules\Translations\Controllers\TranslationsController@getGroups') !!}',
                    "type": "POST",
                    "data": function (term, page) {
                        return {
                            "_token": "{{ csrf_token() }}",
                            "module": $('#modules1').val(), // search term
                            "group": term,
                            "restrition": "{{ $restrition }}"
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: $.map(data, function (obj) {
                                return {id: obj, text: obj};
                            })
                        };
                    }
                }
            });
            $("#groups1").show();
        }



        $('#groups1').change(function() {
            $.ajax({
                method: 'POST', // Type of response and matches what we said in the route
                url: "{{action('\App\Modules\Translations\Controllers\TranslationsController@getKeys')}}", // This is the url we gave in the route
                data: {
                    "_token": "{{ csrf_token() }}",
                    'module':  $('#modules1').val(),
                    'group': $('#groups1').val(),
                    'states': buildSearchDataStates()
                }, beforeSend: function () {
                    $("#loader").show();
                }, success: function (response) { // What to do if we succeed
                    // Load translation list
                    $('#keys-list').html(response);
                    $("#loader").hide();
                    // Reload Hide Show langs
                    reloadHideShowValues();
                    storeHideShowLanguagesValues();
                }, error: function () { // What to do if we fail
                    var errorMessage = "{!! trim(preg_replace('/\s\s+/', ' ', trans('Translations::translation.something_went_wrong'))) !!}";
                    toastr.error(errorMessage);
                }
            });
        });

        //Detect change in states filter
        $('input[type="checkbox"]').click(function(){
            if($('#modules1').val() != '' && $('#groups1').val() != '')
                reload();
        });

        function hideShowLanguages(obj){
            if($(obj).is(':checked')){
                $(".translations-table-"+$(obj).val()).show();
            } else {
                $(".translations-table-"+$(obj).val()).hide();
            }
            $( ".toggle-vis" ).each(function( index ) {
                window.arrayTmpTranslations[$(this).val()] = $(this).is(':checked');
            });
        }

        function storeHideShowLanguagesValues() {
            $( ".toggle-vis" ).each(function( index ) {
                window.arrayTmpTranslations[$(this).val()] = $(this).is(':checked');
            });
        }

        function reloadHideShowValues(){
            for (var k in window.arrayTmpTranslations){
                if (typeof window.arrayTmpTranslations[k] !== 'function') {
                    if( window.arrayTmpTranslations[k] == true){
                        $(".translations-table-"+k).show();
                        $('#toggle-vis-'+k).prop('checked', true);
                    } else {
                        $(".translations-table-"+k).hide();
                        $('#toggle-vis-'+k).prop('checked', false);
                    }
                }
            }
        }
    </script>
    @include('Translations::partials.translationsJs')

@endsection