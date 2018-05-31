<div class="box box-success" data-section-type="{{$sectionType->section_type_key}}" data-section-id="{{ $sectionNumber }}">
    <div class="box-header box-header-margins box-header-blue">
        <a href="#{{$sectionType->section_type_key.'_'.$sectionNumber}}" aria-expanded="true" data-toggle="collapse">
            <div class="box-title section-template-boxtitle">
                <div>
                    <span class="buttons section-template-buttons">
                        @if (One::isEdit())
                            <a href="#" class="fa fa-bars sort-handler ui-sortable-handle" aria-hidden="true"></a>
                            <a class="fa fa-remove" href="#" aria-hidden="true" onclick="removeSection(this);"></a>
                        @endif
                        <i href="#{{$sectionType->section_type_key.'_'.$sectionNumber}}" class="fa fa-angle-down" aria-expanded="true"></i>
                    </span>
                    <i class="{{ $sectionIcons[$sectionType->code] or $sectionIcons["default"] }}" title="{{trans("privateContentManager.section_icon")}}"></i>
                    {{ $sectionType->value }}
                </div>
                <div class="counter section-number">{{$sectionNumber}}</div>
            </div>
        </a>
    </div>
    <div class="panel-collapse collapse @if (ONE::actionType("ContentManager")!="show") show @endif" id="{{$sectionType->section_type_key.'_'.$sectionNumber}}" aria-expanded="true">
        <div class="box-body">
            {!! Form::oneText($sectionType->section_type_key ."_" . $sectionNumber.'_code', trans('privateContentManager.sectionCode'), isset($sectionCode) ? $sectionCode : null,['id'=> $sectionType->section_type_key ."_" . $sectionNumber.'_code','class' => 'form-control']) !!}
            @php
                $defaultType = 'multi';
                if (count($languages)<2)
                    $defaultType = "single";
                else if(isset($sectionType->section_type_parameters)) {
                    if(isset(collect($sectionType->section_type_parameters)->first()->section_param)){
                        if(count(collect($sectionType->section_type_parameters)->first()->section_param->translations) == 0){
                            $defaultType = 'single';
                        }
                        if(!is_null(head($sectionType->section_type_parameters)->section_param->value)){
                            $defaultType = 'single';
                        }
                    }

                    if($sectionType->translatable == 0){
                        $defaultType = 'single';
                    }
                }
            @endphp

            @if (One::isEdit() || (!One::isEdit() && $defaultType=="single"))
                <div class="untranslatableData @if (One::isEdit() && $defaultType!="single") hidden @endif">
                    @if($sectionType->code == 'padsList')
                        @include("private.contentManager.sections.padsList",["parameters" => $sectionType->section_type_parameters])
                    @elseif($sectionType->code == 'contentsList')
                        @include("private.contentManager.sections.contentsList",["parameters" => $sectionType->section_type_parameters])
                    @elseif($sectionType->code == 'linkedBanner')
                        @include("private.contentManager.sections.linkedBanner",["parameters" => $sectionType->section_type_parameters])
                    @elseif($sectionType->code == 'homepageItemSection')
                        @include("private.contentManager.sections.homepageItemSection",["parameters" => $sectionType->section_type_parameters])
                    @else
                        @include("private.contentManager.sectionTemplateParameter",["parameters" => $sectionType->section_type_parameters])
                    @endif
                </div>
            @endif

            @if ((One::isEdit() && $sectionType->translatable==1 && count($languages)>1) || (!One::isEdit() && $defaultType=="multi"))
                <div class="translatableData @if ($defaultType!="multi") hidden @endif">
                    <div>
                        <ul class="nav nav-tabs" role="tablist">
                            @foreach($languages as $language)
                                <li role="presentation" class="nav-item">
                                    <a class="@if ($loop->first) active  @endif nav-link" href="#{{ $sectionType->section_type_key . "_" . $language->code . "_" . $sectionNumber }}" aria-controls="affa" role="tab" data-toggle="tab" @if (One::isEdit()) data-parameter="{{ $language->code }}"@endif>
                                        {{$language->name}}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                        <div class="tab-content">
                            @foreach($languages as $language)
                                <div role="tabpanel" class="tab-pane @if ($loop->first) active @else fade @endif" id="{{ $sectionType->section_type_key . "_" . $language->code . "_" . $sectionNumber }}" @if (One::isEdit()) data-parameter="{{ $language->code }}" @endif>
                                    @if($sectionType->code == 'padsList')
                                        @include("private.contentManager.sections.padsList",["parameters" => $sectionType->section_type_parameters,"sectionNumber" => $sectionNumber,"language"  => $language->code])
                                    @elseif($sectionType->code == 'contentsList')
                                        @include("private.contentManager.sections.contentsList",["parameters" => $sectionType->section_type_parameters,"sectionNumber" => $sectionNumber,"language"  => $language->code])
                                    @elseif($sectionType->code == 'linkedBanner')
                                        @include("private.contentManager.sections.linkedBanner",["parameters" => $sectionType->section_type_parameters,"sectionNumber" => $sectionNumber,"language"  => $language->code])
                                    @elseif($sectionType->code == 'homepageItemSection')
                                        @include("private.contentManager.sections.homepageItemSection",["parameters" => $sectionType->section_type_parameters,"sectionNumber" => $sectionNumber,"language"  => $language->code])
                                    @else
                                        @include("private.contentManager.sectionTemplateParameter",["parameters" => $sectionType->section_type_parameters,"sectionNumber" => $sectionNumber,"language"  => $language->code])

                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
        @if (One::isEdit())
            <div class="bg-success card-footer container-fluid">
                <input type="hidden" name="{{ "translationStatus_" . $sectionNumber }}" data-parameter="translationStatus" value="@if ($defaultType!="single") 1 @else 0 @endif"/>
                <span>
                    @if ($sectionType->translatable==1 && count($languages)>1)
                        <a href="#" class="translatable-status-toggler">
                            <span class="translateMessage @if ($defaultType!="single") hidden @endif">
                                {{ trans("privateContentManager.translate_section_message") }}
                            </span>
                            <span class="untranslateMessage @if ($defaultType!="multi") hidden @endif">
                                {{ trans("privateContentManager.untranslate_section_message") }}
                            </span>
                        </a>
                    @else
                        {{ trans("privateContentManager.untranslatable_section") }}
                    @endif
                </span>
            </div>
        @endif
    </div>
</div>