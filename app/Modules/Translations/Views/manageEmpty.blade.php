@extends('private._private.index')

@section('content')
    <div class="form-group form-group translations-button-row">
        <button id="get_all_translations" class="btn btn-flat btn-success">{{trans("Translations::translation.get_all_translations")}}</button>
        <button id="save_all_translations" class="btn btn-flat btn-info">{{trans("Translations::translation.save_all_translations")}}</button>
        <i class="fa fa-spinner fa-spin fa-3x fa-fw" id="loader"></i>
        <span class="sr-only">Loading...</span>
    </div>
    <div class="form-group esenfc-no-padding">
        <select id="filterBy" class="esenfc-select form-control" name="filterBy" style="width:50%;">
            <option value=""></option>
            <option value="1">{{trans("Translations::translation.filter_by_module")}}</option>
            <option value="2">{{trans("Translations::translation.filter_by_group")}}</option>
        </select>
    </div>
    <div class="form-group esenfc-no-padding">
        <select id="modules" class="esenfc-select form-control" name="modules" disabled="disabled" style="width:50%;">
            @if (isset($modules))
                @foreach($modules as $module)
                    <option value="{{ $module }}">{{ $module }}</option>
                @endforeach
            @endif
        </select>
    </div>
    <div class="form-group esenfc-no-padding">
        <select id="groups" class="esenfc-select form-control" name="modules" disabled="disabled" style="width:50%;">
            <option value=""></option>
            @foreach($groups as $group)
                <option value="{{ $group }}">{{ $group }}</option>
            @endforeach
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
@endsection

@section('scripts')
    <script>

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
                        $('#keys-list').html(response);
                        $("#loader").hide();
                    }
                });
            }else{
                rebuildTable();
            }
        });



        $("#filterBy").select2({
            placeholder: '{{ trans("Translations::translation.selectFilter") }}',
        });

        $("#modules").select2({
            placeholder: '{{ trans("Translations::translation.selectModule") }}',
        });

        $("#groups").select2({
            placeholder: '{{ trans("Translations::translation.selectGroup") }}',
        });

        function rebuildTable() {
            $.ajax({
                method: 'POST', // Type of response and matches what we said in the route
                url: "{{action('\App\Modules\Translations\Controllers\TranslationsController@getAllEmptyTranslations')}}", // This is the url we gave in the route
                data: {
                    "_token": "{{ csrf_token() }}",
                    'module':  $('#modules').val(),
                    'group': $('#groups').val(),
                },beforeSend:function (){
                    $("#loader").show();
                },success: function (response) { // What to do if we succeed
                    $('#keys-list').html(response);
                    $("#loader").hide();
                }
            });
        }
        $( document ).ready(function() {
            rebuildTable();
        });
        $(document).on('change','#modules', function(){
            rebuildTable();
        });

        $(document).on('change','#groups', function(){
            rebuildTable();
        });

        $(document).on('change','#filterBy', function(){

            if($("#filterBy").val() == 1){
                $('#modules').attr('disabled',false);
                $('#groups').attr('disabled','disabled');
                $('#groups').val([]);
            }else{
                $('#modules').attr('disabled','disabled');
                $('#groups').attr('disabled',false);
                $('#modules').val([]);
            }


        });


    </script>
    @include('Translations::partials.translationsJs')

@endsection