<div class="default-paddding">
    @php $randomCode = str_random(4); @endphp
    <div class="card panel-default translation-panel" id="{{ $randomCode }}" style="margin-bottom:10px;">
        <div class="card-header card-header-grey cursor-pointer" data-toggle="collapse" data-target="#body-{{ $randomCode }}">
            <div class="has-feedback">
                <i class="fa fa-chevron-down"></i>
                <select name="code" class="form-control codes code"
                        style="width:auto;display:inline-block;margin-right:15px;" @if(isset($code)) disabled data-valid="1" @endif>
                    @if(!isset($code))
                        <option value="" disabled selected>{{ trans("privateCbsMenuTranslations.select_value") }}</option>
                    @endif
                    @foreach ($menuCodes as $menuCode)
                        <option value="{{ $menuCode }}" @if(isset($code) && $code==$menuCode) selected @endif>{{ $menuCode }}</option>
                    @endforeach
                </select>
                {{trans("privateCbsMenuTranslations.code")}}
                <i class="status-indicator pull-right fa fa-2x @if(isset($code)) fa-check text-color-success @else fa-pencil text-color-warning @endif"
                    title="@if(isset($code)){{ trans("privateCbsMenuTranslations.saved") }}@else{{ trans("privateCbsMenuTranslations.not_saved") }}@endif"></i>
            </div>
        </div>
        <div class="card-body collapse @if(!isset($code)) show @endif" id="body-{{ $randomCode }}">
            <div class="container-fluid">
                <div class="row">
                    @if(!empty($languages))
                        @foreach ($languages as $language)
                            <div class="col-12 col-md-4 col-lg-3">
                                <span>{{ $language->name }}</span><br>
                                <input type="text" name="{{$language->code}}" class="form-control translation pull-down"
                                    @if(isset($currentTranslation)) value="{{ $currentTranslation->{$language->code} ?? "" }}" @endif>
                            </div>
                        @endforeach
                    @endif
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="text-right" style="padding-top:10px;">
                            <a type="button" class="btn btn-flat empatia-dark" href="javascript:submitTranslation('{{ $randomCode }}')" style="margin: 0 10px 0 0">
                                {!! trans("privateCbsMenuTranslations.createOrEdit") !!}
                            </a>
                            <a type="button" class="btn btn-flat btn-preview" href="javascript:deleteTranslation('{{ $randomCode }}')" style="margin: 0 10px 0 0">
                                {!! trans("privateCbsMenuTranslations.cancel") !!}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>