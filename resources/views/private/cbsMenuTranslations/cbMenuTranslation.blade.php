@extends('private._private.index')

@section('header_styles')
    <style>
        .translation-box{
            margin: 0 -5px 5px -5px;
            border: 1px solid #d2d6de;
            padding:15px;
        }
    </style>
@endsection

@section('content')
    <div class="box box-primary">
        <div class="box-footer clearfix">
            <a type="" class="btn btn-flat btn-preview" href="#" data-toggle="modal" data-target="#copyTranslationModal">
                <i class="fa fa-copy" aria-hidden="true"></i> {{ trans("privateCbsMenuTranslations.copyTranslation") }}
            </a>
            <a type="" class="btn btn-flat empatia" href="javascript:addTranslation()">
                <i class="fa fa-plus" aria-hidden="true"></i> {{ trans("privateCbsMenuTranslations.addTranslation") }}
            </a>
        </div>
        <div id="translations-container">
            @foreach ($cbMenuTranslations as $code => $currentTranslation)
                @include("private.cbsMenuTranslations.form")
            @endforeach
        </div>
    </div>

    <div id="copyTranslationModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="card-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">{{ trans("privateCbsMenuTranslations.copyTranslation") }}</h4>
                </div>
                <div class="modal-body">
                    @if($user == 'admin')
                        <div class="form-group">
                            <label for="copy_translation">{{trans('privateCbsMenuTranslations.entity')}}</label><br>
                            <select id="entity" style="width:100%;" class="form-control" name="entity" onchange="selectEntity()">
                                <option selected="selected" value="">{{trans('privateCbsMenuTranslations.select_value')}}</option>
                                @foreach($entities as $entity)
                                    <option value="{{$entity->entity_key}}">{{$entity->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                    <div class="form-group">
                        <label for="copy_translation">{{trans('privateCbsMenuTranslations.cbs')}}</label><br>
                        <select style="width:100%;" class="form-control" name="cbs" id="cbs" >
                            @include("private.cbsMenuTranslations.cbMenuTranslationCopyEntities")
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <a type="button" id="copyTranslationConfirm" class="btn btn-flat empatia">
                        {{trans('privateCbsMenuTranslations.confirm')}}
                    </a>
                    <button type="button" class="btn btn-flat btn-preview" data-dismiss="modal">
                        {{trans('privateCbsMenuTranslations.cancel')}}
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function addTranslation(){
            $('#translations-container').prepend('<div class="loader text-center" style="margin:15px"><img src="{{ asset('images/preloader.gif') }}" alt="Loading"/></div>');
            $.ajax({
                method: 'POST',
                url: '{{ action("CbMenuTranslationController@getNewTranslationForm", ['type'=>$type,'cbKey'=>$cbKey])}}',
                dataType:'html',
                success: function (response) {
                    if(response != 'false'){
                        $('#translations-container').find("div.loader").remove();
                        $('#translations-container').prepend(response);
                        renewUsedCodeListeners();
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                }
            });
        }
        function submitTranslation(code) {
            var panel = $('#'+ code);

            if (panel.find("select[name='code']").attr("data-valid") == "1") {
                translationCode = panel.find("select[name='code']").val();
                var translations = {};
                panel.find("input.translation").each(function () {
                    translations[$(this).attr('name')] = $(this).val();
                });

                $.ajax({
                    method: 'POST',
                    url: '{{action("CbMenuTranslationController@storeOrUpdate", ['type' => $type, 'cbKey' => $cbKey])}}',
                    data: {
                        code: translationCode,
                        translations: translations
                    },
                    success: function () {
                        toastr.success("{{ trans("privateCbsMenuTranslations.successfully_stored_or_updated") }}");
                        updateStatusIndicator(panel,"stored");
                        panel.find("select").attr("disabled","disabled").removeClass('has-success').removeClass('has-error');
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                    }
                });
            } else {
                panel.find("select[name='code']").parent().addClass('has-error');
                toastr.error("{{ trans("privateCbsMenuTranslations.already_used_code") }}");
            }
        }
        function deleteTranslation(code){
            $.ajax({
                method: 'POST',
                url: '{{action("CbMenuTranslationController@delete", ['type' => $type,'cbKey' => $cbKey])}}',
                data:{
                    code: $('#'+ code).find("select[name='code']").val()
                },
                success: function (response) {
                    location.reload();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                }
            });
        }
        function renewUsedCodeListeners() {
            $('select.code')
                .off("change")
                .change(function (){
                    currentSelect = $(this);
                    updateStatusIndicator(currentSelect.closest("div.translation-panel"),"stored");
                    $.ajax({
                        method: 'GET',
                        url: '{{action("CbMenuTranslationController@isCodeUsed", ['type' => $type, 'cbKey'=>$cbKey])}}',
                        data: {
                            code: currentSelect.val()
                        },
                        success: function (response) {
                            if (response === 'true') {
                                currentSelect.attr("data-valid","1");
                                currentSelect.parent().addClass('has-success').removeClass('has-error');
                            } else {
                                currentSelect.attr("data-valid","0");
                                currentSelect.parent().addClass('has-error').removeClass('has-success');
                            }
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                        }
                    });
                })
                .on("click",function(event){
                    event.stopPropagation();
                });

            $("div.translation-panel input, div.translation-panel select").on("change",function(){
                updateStatusIndicator($(this).closest("div.translation-panel"),"edited");
            })
        }
        function updateStatusIndicator(panel, status) {
            statusIndicator = panel.find(".status-indicator");

            if (status=="stored")
                statusIndicator
                    .removeClass("fa-pencil").addClass("fa-check")
                    .removeClass("text-warning").addClass("text-success")
                    .attr("title","{{ trans("privateCbsMenuTranslations.saved") }}");
            else
                statusIndicator
                    .removeClass("fa-check").addClass("fa-pencil")
                    .removeClass("text-success").addClass("text-warning")
                    .attr("title","{{ trans("privateCbsMenuTranslations.not_saved") }}");
        }

        @if($user == 'admin')
            function selectEntity(){
                $.ajax({
                    method: 'POST',
                    url: '{{action("CbMenuTranslationController@getEntityCbsWithMenuTranslation", ['type' => $type,'cbKey' => $cbKey])}}',
                    data:{
                        entity: $('#entity').val()
                    },
                    success: function (response) {
                        var element = $('#cbs');
                        if (response.noCbs == true) {
                            toastr.warning("{{ trans("privateCbsMenuTranslations.no_cbs_for_entity") }}")
                            element.html('<option selected="selected" value="">{{trans("privateCbsMenuTranslations.select_value")}}</option>');
                            $('#copyTranslationConfirm').attr("disabled", true);
                        } else {
                            element.html(response);
                            element.attr('disabled', false);
                            $('#copyTranslationConfirm').attr("disabled", false);
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                        element.html('<option selected="selected" value="">{{trans("privateCbsMenuTranslations.select_value")}}</option>');
                        $('#copyTranslationConfirm').attr("disabled", true);
                    }
                });
            }
        @endif

        $(document).ready(function() {
            renewUsedCodeListeners();
            $('#copyTranslationConfirm').on('click', function(){
                if(!$(this).attr("disabled")){
                    var element = $('#cbs').val();

                    if (element){
                        $.ajax({
                            method: 'POST',
                            url: '{{action("CbMenuTranslationController@copyMenuTranslationsFromCb", ['type' => $type, 'cbKey' => $cbKey])}}',
                            data:{
                                origin: element
                            },
                            success: function (response) {
                                location.reload();
                            },
                            error: function (jqXHR, textStatus, errorThrown) {
                                console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                            }
                        });
                    } else
                        toastr.warning("{{ trans("privateCbsMenuTranslations.no_cb_selected") }}")
                }
            });
        });
    </script>
@endsection
