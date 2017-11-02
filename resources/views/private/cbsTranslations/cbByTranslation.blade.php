<div class="card-header">
    {{trans('privateCbsTranslations.CbTranslation')}}
</div>
<div class="card-body">
    <div class="row">
        <div class="col-12 col-md-5">
            <div class="form-group has-feedback">
                <label class="form-control-label" for="codeInput">{{trans("privateCbsTranslations.code")}}</label>
                <input id="code" class="form-control codes code" type="text" aria-describedby="codeStatus">
                <span class="sr-only glyphicon form-control-feedback" aria-hidden="true"></span>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <a type="" class="btn-sm btn-secondary pull-right btn-copy-translations" href="javascript:copyTranslations('code')">
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
                <span>{{trans("privateCbsTranslations.during")}}</span>
            </div>
            @if(!empty($languages))
                @foreach ($languages as $language)
                    <div class="col-12 col-md-2">
                        <input type="text" name="during_{{$language->code}}" id="during_{{$language->code}}" class="form-control translationsNew translationNew duringNew pull-down">
                    </div>
                @endforeach
            @endif
        </div>
    </div>
    <div id="panel_copy">
        <div class="row">
            <div class="col-12 col-md-3">
                <span>{{trans("privateCbsTranslations.before")}}</span>
            </div>
            @if(! empty($languages))
                @foreach ($languages as $language)
                    <div class="col-12 col-md-2">
                        <input type="text" name="before_{{$language->code}}" id="before_{{$language->code}}" class="form-control translationsNew translationNew">
                    </div>
                @endforeach
            @endif
        </div>
        <div class="row">
            <div class="col-12 col-md-3">
                <span>{{trans("privateCbsTranslations.after")}}</span>
            </div>
            @if(! empty($languages))
                @foreach ($languages as $language)
                    <div class="col-12 col-md-2">
                        <input type="text" name="after_{{$language->code}}" id="after_{{$language->code}}" class="form-control translationsNew translationNew">
                    </div>
                @endforeach
            @endif
        </div>
    </div>
    <div>
        <a type="" class="btn btn-flat empatia" href="javascript:createTranslation('code')" style="margin: 25px 10px 0 0">{!! trans("privateCbsTranslations.createOrEdit") !!}</a>
        <a type="" class="btn btn-flat btn-preview" href="javascript:cancel('code')" style="margin: 25px 10px 0 0">{!! trans("privateCbsTranslations.cancel") !!}</a>
    </div>
</div>

<label id="erro"></label>
