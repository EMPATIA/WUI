@extends('private._private.index')

@section('header_styles')
    <style>
        .btn-copy-translations{
            margin-right:-5px;
            border-top: 1px solid #d2d6de;
            border-left: 1px solid #d2d6de;
            border-right: 1px solid #d2d6de;
            border-bottom-left-radius: 0;
            border-bottom-right-radius: 0;
        }
        .translation-box{
            margin: 0 -5px 20px -5px;
            border: 1px solid #d2d6de;
            padding: 5px;
        }
        .translationsNew{
            margin-bottom: 5px;
        }
    </style>
@endsection

@section('content')
    <div class="box box-primary">
        <div class="box-body">
            <div class="box-footer clearfix">
                <a type="" class="btn btn-flat empatia-dark pull-right" href="javascript:add()"><i class="fa fa-plus" aria-hidden="true"></i> {!! trans("privateCbsTranslations.addTranslation") !!}</a>
                <a type="" class="btn btn-flat btn-submit pull-left" href="javascript:copyCbTranslation()">{!! trans("privateCbsTranslations.copyTranslation") !!}</a>
            </div>
            <div id="copyTranslation">
                @if($user == 'admin')
                    <div class="col-6 col-md-8">
                        <label for="copy_translation">{{trans('privateCbsTranslations.entity')}}</label><br>
                        <select id="entity" style="width:100%;" class="form-control" name="entity" onchange="selectEntity()">
                            <option selected="selected" value="">{{trans('privateCbsTranslations.select_value')}}</option>
                            @foreach($entities as $entity)
                                <option value="{{$entity->id}}">{{$entity->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6 col-md-8">
                        </br>
                        <label for="copy_translation">{{trans('privateCbsTranslations.cbs')}}</label><br>
                        <select id="cbs" style="width:100%;" class="form-control" name="cbs">
                            <option selected="selected" value="">{{trans('privateCbsTranslations.select_value')}}</option>
                            @if($CbsEntity != '[]')
                                @foreach($CbsEntity as $Cbs)
                                    <option value="{{$Cbs->id}}">{{$Cbs->title}}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="col-6 col-md-8">
                        <a type="" id="button_confirm" class="btn btn-flat btn-submit pull-left" style="margin-top:45px;">{{trans('privateCbsTranslations.confirm')}}</a>
                    </div>
                @else
                    <div class="col-6 col-md-8">
                        </br>
                        <label for="copy_translation">{{trans('privateCbsTranslations.cbs')}}</label><br>
                        <select id="cbs" style="width:100%;" class="form-control" name="cbs" >
                            <option selected="selected" value="">{{trans('privateCbsTranslations.select_value')}}</option>
                            @if($CbsEntity != '[]')
                                @foreach($CbsEntity as $Cbs)
                                    <option value="{{$Cbs->id}}">{{$Cbs->title}}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <a type="button" id="button_confirm" class="btn btn-flat btn-success pull-left" style="margin-top:45px;">{{trans('privateCbsTranslations.confirm')}}</a>
                @endif
            </div>
        </div>
        </br></br>
        <div class="card flat" id="panel"> </div>
        @if(! empty($cb_translations->data))
            @foreach ($cb_translations->data as $code => $value)
                <div class="card flat" id="panel_{{$code}}">
                    <div class="card-header">
                        {{trans('privateCbsTranslations.CbTranslation')}}
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6 col-md-3">
                                <div class="form-group has-feedback">
                                    <label class="form-control-label" for="name">{{trans("privateCbsTranslations.code")}}</label>
                                    <input type="text" name="{{$code}}" id="{{$code}}" value="{{$code}}" class="form-control codes code">
                                    <span class="sr-only glyphicon form-control-feedback" aria-hidden="true"></span>
                                </div>
                            </div>
                            <div>
                                <a type="button" class="btn btn-flat btn-danger pull-right" style="margin-right:15px;" href="javascript:deleteTranslation({{$code}})"><i class="fa fa-times" aria-hidden="true"></i></a>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <a type="button" class="btn-sm btn-secondary pull-right btn-copy-translations" href="javascript:copyTranslations({{$code}})">
                                    <i class="fa fa-files-o" aria-hidden="true"></i>
                                    {{ trans("privateCbsTranslations.copy_to_all")}}
                                </a>
                            </div>
                        </div>
                        <div class="translation-box">
                            <div class="row">
                                <div class="col-12 col-md-3"></div>
                                @if(!empty($languages))
                                    @foreach ($languages as $language)
                                        <div class="col-12 col-md-2">
                                            <span>{{$language->name}}</span>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                            <div class="row">
                                <div class="col-12 col-md-3">
                                    <label for="name">{{trans("privateCbsTranslations.during")}}</label>
                                </div>
                                @if(!empty($languages))
                                    @foreach ($languages as $language)
                                        <div class="col-12 col-md-2">
                                            <input type="text" name="during_{{$language->code}}" value="{{$value->during->{$language->code} ?? null}}" id="during_{{$language->code}}" class="form-control translations translation during {{$code}}">
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                        <div id="panel_copy">
                            <div class="row">
                                <div class="col-12 col-md-3">
                                    <label for="name">{{trans("privateCbsTranslations.before")}}</label>
                                </div>
                                @if(!empty($languages))
                                    @foreach ($languages as $language)
                                        <div class="col-12 col-md-2">
                                            <input type="text" name="before_{{$language->code}}" value="{{$value->before->{$language->code} ?? null}}" id="before_{{$language->code}}" class="form-control translations translation before {{$code}}">
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                            <div class="row">
                                <div class="col-12 col-md-3">
                                    <label for="name">{{trans("privateCbsTranslations.after")}}</label>
                                </div>
                                @if(!empty($languages))
                                    @foreach ($languages as $language)
                                        <div class="col-12 col-md-2">
                                            <input type="text" name="after_{{$language->code}}" value="{{$value->after->{$language->code} ?? null}}" id="after_{{$language->code}}" class="form-control translations translation after {{$code}}">
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                        <a type="" class="btn btn-flat empatia" href="javascript:createTranslation('{{$code}}')" style="margin: 25px 10px 0 0">{!! trans("privateCbsTranslations.createOrEdit") !!}</a>
                        <a type="" class="btn btn-flat btn-preview" href="javascript:cancel()" style="margin: 25px 10px 0 0">{!! trans("privateCbsTranslations.cancel") !!}</a>
                    </div>
                </div>
                <label id="erro_copy"></label>
            @endforeach
        @endif
        <label id="erro"></label>
    </div>
@endsection

@section('scripts')
    <script>
        var verify = false;

        function add(){
            $.ajax({
                method: 'POST',
                url: '{{ action("CbTranslationController@viewTranslation", ['type'=>$type,'cbKey'=>$cbKey])}}',
                dataType:'html',
                success: function (response) {
                    if(response != 'false'){
                        $('#panel').html(response);

                        var element = $(this);

                        $('.code').focusout(function() {
                            var element = $(this);

                            $.ajax({
                                method: 'GET',
                                url: '{{action("CbTranslationController@getCode", ['cbKey'=>$cbKey])}}',
                                data:{
                                    code: element.val()
                                },
                                success: function (response) {
                                    if (response === 'true'){
                                        verify = false;
                                        element.parent().addClass('has-success').removeClass('has-error');
                                        element.next().removeClass('sr-only glyphicon-remove').addClass('glyphicon-ok');
                                    } else {
                                        verify = true;
                                        element.parent().addClass('has-error').removeClass('has-success');
                                        element.next().removeClass('sr-only glyphicon-ok').addClass('glyphicon-remove');
                                    }
                                },
                                error: function (jqXHR, textStatus, errorThrown) {
                                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                                }
                            });
                        });
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                }
            });
        }

        $( document ).ready(function() {
            function verifyCode(element) {
                $('.code').focusout(function (){
                    $.ajax({
                        method: 'GET',
                        url: '{{action("CbTranslationController@getCode", ['cbKey'=>$cbKey])}}',
                        data: {
                            code: element.val()
                        },
                        success: function (response) {
                            if (response === 'true') {
                                verify = false;
                                element.parent().addClass('has-success').removeClass('has-error');
                                element.next().removeClass('sr-only glyphicon-remove').addClass('glyphicon-ok');
                            } else {
                                verify = true;
                                element.parent().addClass('has-error').removeClass('has-success');
                                element.next().removeClass('sr-only glyphicon-ok').addClass('glyphicon-remove');
                            }
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                        }
                    });
                });
            }
        });

        function createTranslation(codeTranslation)
        {
            var value = $('#'+ codeTranslation).val();

            if(!value){
                $('#erro').after(
                    '<div class="alert alert-danger alert-dismissible fade in" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close">  <span aria-hidden="true">&times;</span> </button> {{trans('privateCbsTranslations.codeNotInsert')}}  </div>'
                );
                $(".alert").delay(6000).slideUp(200, function() {
                    $(this).alert('close');
                });
            } else {
                if(verify !== true){

                    var codeAnt = null;
                    var translation = null;
                    var allValues = {};

                    if (codeTranslation === 'code'){
                        allValues['code'] = value;
                        codeAnt = allValues;
                        translation = translationsNew();
                    } else {
                        codeAnt = code(codeTranslation);
                        translation = translations(codeTranslation);
                    }

                    $.ajax({
                        method: 'POST',
                        url: '{{action("CbTranslationController@storeOrUpdate", ['cbKey' => $cbKey])}}',
                        data:{
                            code: codeAnt,
                            translations:translation
                        },
                        success: function (response) {

                            if(response !== 'false'){
                                location.reload();
                            }
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                        }
                    });
                }
            }
        }

        function translations(id){

            var allValues = {};
            $('.translation').each(function () {
                if(this.classList.contains(id)){
                    allValues[$(this).attr('name')] = $(this).val();
                }
            });

            return allValues;
        }


        function translationsNew(){
            var allValues = {};
            $('.translationsNew').each(function () {
                if(this.classList.contains("translationNew")){
                    allValues[$(this).attr('name')] = $(this).val();
                }
            });
            return allValues;
        }

        function code(id){
            var allValues = {};
            allValues[$('#'+id).attr('name')] = $('#'+id).val();

            return allValues;
        }

        function cancel(){
            location.reload();
        }


        function deleteTranslation(codeTranslation){
            $.ajax({
                method: 'POST',
                url: '{{action("CbTranslationController@delete", ['type' => $type,'cbKey' => $cbKey])}}',
                data:{
                    code: codeTranslation.id
                },
                success: function (response) {
                    if(response != 'false'){
                        location.reload();
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                }
            });
        }


        function copyCbTranslation(){

            if('{{$user}}'== 'admin'){
                $('#copyTranslation').toggle();
                $('#button_confirm').toggle();
                $('#cbs').attr('disabled', 'disabled');
                $('#button_confirm').attr("disabled", true);
                $('#button_confirm').on('click', function(event){
                    event.preventDefault();
                })
            }

            else if ('{{$user}}'== 'manager'){
                $.ajax({
                    method: 'POST',
                    url: '{{action("CbTranslationController@viewCpyTranslation", ['type'=>$type,'cbKey'=>$cbKey])}}',
                    data:{
                    },
                    success: function (response) {
                        if(response != 'false'){
                            $('#cbs').html(response);
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                    }
                });

                $('#copyTranslation').toggle();
                $('#button_confirm').toggle();
                $('#cbs').attr('disabled', false);
                $('#button_confirm').attr("disabled", false);
            }
        }

        function selectEntity(){

            var entity = $('#entity').val();

            $.ajax({
                method: 'POST',
                url: '{{action("CbTranslationController@viewCpyTranslation", ['type' => $type,'cbKey' => $cbKey])}}',
                data:{
                    entity: entity
                },
                success: function (response) {
                    var element = $('#cbs');
                    if(response !== 'false'){
                        element.html(response);
                        element.attr('disabled', false);
                        $('#button_confirm').attr("disabled", false);
                    } else {
                        element.attr('disabled', true);
                        $('#button_confirm').attr("disabled", true);
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                }
            });
        }

        $('#button_confirm').on('click', function(){
            if(!$(this).attr("disabled") && '{{$user}}' === 'admin'){
                confirm();
            }
        });

        function confirm(){
            var element = $('#cbs').val();

            if (element){
                $.ajax({
                    method: 'POST',
                    url: '{{action("CbTranslationController@viewConfirmTranslation", ['cbKey' => $cbKey])}}',
                    data:{
                        cb:element
                    },
                    success: function (response) {
                        if(response !== 'false'){
                            location.reload();
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                    }
                });
            }else{
                $('#erro_copy').after(
                    '<div class="alert alert-danger alert-dismissible fade in" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close">  <span aria-hidden="true">&times;</span> </button> {{trans('privateCbsTranslations.cbNotSelected')}}  </div>');
                $(".alert").delay(6000).slideUp(200, function() {
                    $(this).alert('close');
                });
            }
        }

        function copyTranslations(codeTranslation){
            var code = null;
            var allValues = {};

            if(codeTranslation.id){
                $('.during').each(function () {
                    if(this.classList.contains(codeTranslation.id)){
                        allValues[$(this).attr('name')] = $(this).val();
                    }
                });
                code = codeTranslation.id;
            }
            else{
                $('.duringNew').each(function () {
                    if(this.classList.contains("translationNew")){
                        allValues[$(this).attr('name')] = $(this).val();
                    }
                });
                code = codeTranslation;
            }

            $('#panel_copy').find('input').each(function(){
                var inputName =  $(this).attr('name').split('_');
                var concatName = 'during_'.concat(inputName[1]);
                $(this).val(allValues[concatName]);
            });
        }

        $( document ).ready(function() {
            document.getElementById("copyTranslation").style.display = 'none';
            document.getElementById("button_confirm").style.display = 'none';

            $(".alert").delay(6000).slideUp(200, function() {
                $(this).alert('close');
            });
        });
    </script>
@endsection
