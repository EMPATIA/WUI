<div id="div{{ $inputId }}-{!! $languageCode !!}" class="questionOptionsWrapper div{{ $inputId }}">
    <div class="margin-bottom-20">

        <div class="row">
            <div class="@if(!empty($dependencies)) col-md-6 @else col-md-10 @endif">

                <div class="row">
                    <div class="@if(!empty($icons) && isset($icons)) col-10 @else col-12  @endif">
                        <div class="input-group">
                            <div class="input-group-addon">
                                <input type="checkbox" name="correctOpttion" id="correctOption" @if(isset($questionOption) && in_array($questionOption->question_option_key, $correctOptions)) checked="checked" @endif data-toggle="tooltip" data-original-title="{!! trans("privateQuestionnaireAddQuestionOption.correctOption") !!}" title="{!! trans("privateQuestionnaireAddQuestionOption.correctOption") !!}" >
                            </div>
                            <input id="label_{{ $inputId }}"
                                   name="label_{{ $inputId }}_{{ $languageCode }}"
                                   class="form-control inline"
                                   @if(!empty($questionOption))
                                    value="@if(!empty(collect($questionOption->question_option_translations)->keyBy('language_code')[$languageCode])){!! collect($questionOption->question_option_translations)->keyBy('language_code')[$languageCode]->label!!}@endif"
                                   @endif
                                   onchange="$('.label_{!!$inputId !!}').attr('placeholder',this.value)"
                                   placeholder="{!! trans("privateQuestionnaireAddQuestionOption.newOption") !!}"
                            >
                        </div>
                    </div>
                    @if(!empty($icons) && isset($icons))
                        <div class="col-2">
                            <!-- ICONS -->
                            @php
                            $imageIconSrc = "";
                            foreach($icons as $icon){
                                if( isset($questionOption) && isset($questionOption->icon) && $icon->icon_key == $questionOption->icon->icon_key){
                                    $imageIconSrc = action('FilesController@download', ['id' => $icon->file_id,'code' => $icon->file_code,1] );
                                }
                            }
                            @endphp
                            <div class="btn-group">
                                <button id="addOption" type="button" class="btn btn-success" data-toggle="modal" data-target="#iconsModal{{ $inputId }}">
                                    <i id="questFaPicture{{ $inputId }}"  class="fa fa-picture-o" aria-hidden="true" style="@if($imageIconSrc!="") display:none; @endif" ></i>
                                    <img class="img" src="{!! $imageIconSrc !!}" id="questIconImage{{ $inputId }}" style="height:16px; @if($imageIconSrc=="") display:none; @endif">
                                </button>
                                <button onclick="removeQuestionIcon({{ $inputId }})" id="questRemovePicture{{ $inputId }}" type="button" class="btn btn-danger" style="@if($imageIconSrc=="") display:none @endif">
                                    <i class="fa fa-times" aria-hidden="true"></i>
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            @if(!empty($dependencies))
                <div class="col-md-5">
                    <select id="dependencies_{{ $inputId }}" name="dependencies_{{ $inputId }}[]" class="select2 form-control" multiple="true" style="width:100%;" data-placeholder="{!! trans("privateQuestionnaireAddQuestionOption.clickHereToSelectDependency") !!}">
                        @foreach($dependencies as $dependency)
                            <option value="{{ $dependency->question_key }}" {{ isset($questionOption) && isset($questionOptionDependencies[$questionOption->question_option_key][$dependency->question_key])? 'selected':'' }}  >{!! $dependency->question !!}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div class="col-md-1 text-right">
                <a id="removeQuestionOption" onclick="javascript:removeQuestionOption({{ $inputId }})" class="btn btn-flat btn-danger btn-sm">
                    <i class="fa fa-remove"></i>
                </a>
            </div>
        </div>
        <!-- Question Option Key [hidden] -->
        <input id="question_option_key_{{ $inputId }}" name="question_option_key_{{ $inputId }}" value="@if(!empty($questionOption)){!! $questionOption->question_option_key !!}@endif" type="hidden" >


    </div>
</div>


@if(!empty($icons) && isset($icons))
    <!-- Modal -->
    <div id="iconsModal{{ $inputId }}" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="card-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">{{trans('privateQuestionOption.icons')}}</h4>
                </div>
                <div class="modal-body">
                    <div class="btn-group" data-toggle="buttons">
                        @foreach($icons as $icon)
                            <label onclick="changeQuestionIcon({{ $inputId }},'{{action('FilesController@download', ['id' => $icon->file_id,'code' => $icon->file_code,1] )}}')" class="inputQuestionLabel{{ $inputId }} btn btn-secondary {{isset($questionoption->icon->icon_key)? (($icon->icon_key == $questionoption->icon->icon_key)? 'active' :''):''}}" id="" title="">
                                <input class="inputQuestionIcon{{ $inputId }}" type="radio" name="icon_key_{{ $inputId }}" id="icon_key_{ $inputId }}" value="{{$icon->icon_key}}" {{isset($questionoption->icon->icon_key)? (($icon->icon_key == $questionoption->icon->icon_key)? 'checked' :''):''}}>
                                {{--<img src="http://placehold.it/20x20/35d/fff&text=f"  id="iconImage" style="height:30px">--}}
                                <img class="img" src="{{action('FilesController@download', ['id' => $icon->file_id,'code' => $icon->file_code,1] )}}"  id="questIconImage" style="height: 30px">
                            </label>
                        @endforeach
                    </div>
                </div>
                <!--
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
                -->
            </div>

        </div>
    </div>
@endif
