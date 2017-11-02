@extends('private._private.index')

@section("header_scripts")
    <!-- Plupload Javascript fix and bootstrap fix @ start -->
    <link rel="stylesheet" href="/bootstrap/plupload-fix/bootstrap.css">
    <script src="/bootstrap/plupload-fix/bootstrap.min.js"></script>
    <!-- Plupload Javascript fix and bootstrap fix @ End -->
    <script src="{{ asset('vendor/jildertmiedema/laravel-plupload/js/plupload.full.min.js') }}"></script>
@endsection

@section('content')
    @php
        $form = ONE::form('ContentManager',$secondTitle, 'cm', $contentType)
            ->settings(["model" => isset($content) ? $content->id : null,'id'=>isset($content) ? $content->content_key : null])
            ->show('ContentManagerController@edit', 'ContentManagerController@delete', ['contentType'=>$contentType ?? null,'id' => isset($content->content_key) ? $content->content_key : null,'version' => isset($content->version) ? $content->version : null, 'siteKey'=>$siteKey,"topicKey" => $topicKey], 'ContentManagerController@index',["contentType"=>$contentType,"topicKey" => $topicKey, 'siteKey'=>$siteKey])
            ->create('ContentManagerController@store', 'ContentManagerController@index',["contentType"=>$contentType, 'siteKey'=>$siteKey,'topicKey' => $topicKey])
            ->edit('ContentManagerController@update', 'ContentManagerController@show', ['contentType'=>$contentType,'contentId' => isset($content->content_key) ? $content->content_key : null,'versionNumber' => isset($content->version) ? $content->version : null, 'siteKey'=>$siteKey,'topicKey' => $topicKey])
            ->open();
    @endphp

    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title">{{ trans("privateContentManager.generic_data") }}</h3>
        </div>

        <div class="box-body">
            @if (ONE::actionType("ContentManager")=="show")
                <div class="form-group row">
                    <div class="col-12">
                        <select name="versions" onchange="location = this.value;"  class="form-control" style="font-family: 'FontAwesome','Open Sans', sans-serif!important;">
                            @foreach ($content->versions_list as $version)
                                <option value="{{ action('ContentManagerController@show',["contentType"=>$contentType,"contentKey"=>$content->content_key,"version"=>$version->version, 'siteKey'=>$siteKey, "topicKey" => $topicKey]) }}" @if($version->version==$content->version) selected @endif>
                                    {{ trans("privateContentManager.version") }} {{ $version->version }} ({{ \Carbon\Carbon::parse($version->created_at->date)->format("Y-m-d H:i") }})
                                    @if($version->version==$content->version)
                                        &#xf06e;
                                    @endif
                                    @if($version->active==1)
                                        &#xf00c;
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12"></div>
                    <div class="col-12">
                        <a class="btn btn-flat btn btn-primary pull-left" target="_blank"
                           href="{{ action("ContentManagerController@previewVersion", ["contentType"=> $contentType, "contentKey" => $content->content_key, "versionNumber" => $content->version,"topicKey" => $topicKey]) }}">
                            <i class="fa fa-eye"></i> {{ trans('privateContentManager.preview_version') }}
                        </a>

                        @if ($content->active==1)
                            <a class="btn btn-flat btn btn-danger pull-right"
                               href="{{ action("ContentManagerController@changeVersionActiveStatus", ["contentType"=> $contentType, "contentKey" => $content->content_key, "versionNumber" => $content->version, "newStatus" => 0,"topicKey" => $topicKey]) }}">
                                <i class="fa fa-times"></i> {{ trans('form.unpublish') }}
                            </a>
                        @else
                            <a class="btn btn-flat btn btn-success pull-right"
                               href="{{ action("ContentManagerController@changeVersionActiveStatus", ["contentType"=> $contentType, "contentKey" => $content->content_key, "versionNumber" => $content->version, "newStatus" => 1,"topicKey" => $topicKey]) }}">
                                <i class="fa fa-check"></i> {{ trans('form.publish') }}
                            </a>
                        @endif
                    </div>
                </div>
            @endif
            @if ($contentType=="news" || $contentType=="events")
                {!! Form::oneDate('startdate', trans('privateContentManager.start_date'), isset($content->start_date) ? \Carbon\Carbon::parse($content->start_date)->toDateString() : null, ['class' => 'form-control oneDatePicker', 'id' => 'startdate']) !!}
                {!! Form::oneDate('publishdate', trans('privateContentManager.publish_date'), isset($content->publish_date) ? \Carbon\Carbon::parse($content->publish_date)->toDateString() : null, ['class' => 'form-control oneDatePicker', 'id' => 'publishdate']) !!}
                @if ($contentType=="events")
                    {!! Form::oneDate('endate', trans('privateContentManager.end_date'), isset($content->end_date) ? \Carbon\Carbon::parse($content->end_date)->toDateString() : null, ['class' => 'form-control oneDatePicker', 'id' => 'endate']) !!}
                @endif
            @endif

            @if($contentType=="articles" || $contentType=="faqs" || $contentType=="municipal_faqs")
                {!! Form::oneDate('publishdate', trans('privateContentManager.publish_date'), isset($content->publish_date) ? \Carbon\Carbon::parse($content->publish_date)->toDateString() : null, ['class' => 'form-control oneDatePicker', 'id' => 'publishdate']) !!}
            @endif
            <div class="form-group">
                @if (ONE::actionType("ContentManager")=="show")
                    <dt>{{ trans("privateContentManager.site_of_content") }}</dt>
                    <dd>
                        @forelse($selectedSites as $key => $site)
                            <a class="btn btn-secondary" href="#" role="button">
                                {{ collect($sites)->where("key",$site)->first()->name }}
                            </a>
                        @empty
                            {{ trans('privateContentManager.no_sites_associated') }}
                        @endforelse
                    </dd>
                @else
                    <label for="sites[]">{{ trans("privateContentManager.site_of_content") }}</label>
                    <select id="sites" name="sites[]" class="form-control" multiple>
                        @forelse($sites as $key => $site)
                            <option value="{!! $site->key !!}"
                                @if ((isset($content->content_sites) && collect($content->content_sites)->contains("site_key",$site->key)) || $siteKey==$site->key)) selected @endif>
                                {!! $site->name !!}
                            </option>
                        @empty
                            {{ trans('privateContentManager.no_sites_available') }}
                        @endforelse
                    </select>
                @endif
            </div>
            {!! Form::oneText('name', trans('privateContentManager.name'), isset($content->name) ? $content->name : null, ['class' => 'form-control', 'id' => 'name']) !!}
            {!! Form::oneText('code', trans('privateContentManager.code'), isset($content->code) ? $content->code : null, ['class' => 'form-control', 'id' => 'code', (isset($content->code) && !empty($content->code)) ? "disabled" : ""]) !!}
        </div>
    </div>

    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title"><i class="fa"></i> {{ trans("privateContentManager.sections") }}</h3>
            <div class="pull-right">
                <a href="#" class="btn btn-flat btn-sm" role="button" onclick="collapsePanels()">{{ trans("privateContentManager.collapse_all_sections") }}</a>
                @if (ONE::actionType("ContentManager")!="show")
                    <a href="#" class="btn btn-flat btn-success btn-sm" data-toggle="modal" data-target="#sectionCreateModal"><span class="fa fa-plus"></span></a>
                @endif
            </div>
            @if (ONE::actionType("ContentManager")!="show")
                <input type="hidden" name="sortOrder"/>
                <input type="hidden" name="topicKey" value="{!! $topicKey !!}"/>
            @endif
        </div>
        <div class="box-body">
            <div id="sectionsDiv">
                @if (isset($content->sections))
                    {{-- Sections Panels START --}}
                    @foreach ($content->sections as $section)
                        @include("private.contentManager.sectionTemplate",["sectionNumber"=>$loop->iteration,"section"=>$section,"languages" => $languages,"sectionType" => $section->section_type_data, "sectionCode" => $section->code])
                    @endforeach
                    {{-- Sections Panels END --}}
                @endif
            </div>
            <div class="loader text-center hidden">
                <img src="{{ asset('images/preloader.gif') }}" alt="Loading"/>
            </div>
        </div>
    </div>
    {!! $form->make() !!}

    @if (ONE::actionType("ContentManager")!="show")
        <div id="sectionCreateModal" class="modal fade">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="card-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title">{{ trans("privateContentManager.create_section_modal_title") }}</h4>
                    </div>
                    <div class="modal-body" id="sortable-panels">
                        <p>{{ trans("privateContentManager.create_section_modal_content") }}</p>
                        <div class="list-group">
                            @forelse($sectionTypes as $key => $sectionType)
                                <a href="#createSection" data-section-key="{{ $sectionType->section_type_key }}" class="create-section list-group-item-action list-group-item">
                                    <span class="pull-left text-xs-center" style="width:50px">
                                        <i class="{{ $sectionIcons[$sectionType->code] or $sectionIcons["default"] }}"></i>
                                    </span>
                                    {{ $sectionType->value }}
                                </a>
                            @empty
                                {{ trans('privateContentManager.no_sections_available') }}
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="sectionRemoveModal" class="modal fade">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="card-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title">{{ trans("privateContentManager.removeSectionModalTitle") }}</h4>
                    </div>
                    <div class="modal-body">
                        <p>{{ trans("privateContentManager.removeSectionModalContent") }}</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ trans("privateContentManager.cancel") }}</button>
                        <button type="button" class="btn btn-primary" data-dismiss="modal">{{ trans("privateContentManager.remove") }}</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@section('scripts')
    @if (ONE::actionType("ContentManager")!="show")
        <script src="{{ asset("js/jquery-ui.js") }}"></script>
        <script src="{{ asset("js/tinymce/tinymce.min.js") }}"></script>
        <script src="{{ asset("js/bootstrap-datepicker/bootstrap-datepicker.min.js") }}"></script>
    @endif
    <script>
        function collapsePanels() {
            panels = $("div#sectionsDiv:first div.panel-collapse");
            if (panels.is(":visible") && panels.is(":hidden"))
                panels.collapse('hide');
            else
                panels.collapse('toggle');
        }
        @if (ONE::actionType("ContentManager")!="show")
                $(document).ready(function() {
                    @if (!empty($siteKey))
                        getSidebar('{{ action("OneController@getSidebar") }}', 'cm.{{ $contentType }}',@php if(is_null($topicKey)): @endphp "{{($siteKey)}}" @php else: @endphp ["{{$siteKey}}","{{$topicKey}}"] @php endif @endphp, 'site' )
                    @endif

                    $("select#sites").select2();
                    $("#sectionsDiv").sortable({
                        placeholder: "card sortable-placeholder",
                        axis: "y",
                        cursor: "move",
                        helper: "clone",
                        opacity: 0.7,
                        handle: 'div.box-header a.sort-handler',
                        stop: function(event, ui) {
                            if (ui.item.hasClass("draggable-element"))
                                ui.item.replaceWith(getHtmlForSection(ui.item[0].href));

                            calculateIndexes();
                        },
                    });
                    $("form").on("submit",function() {
                        calculateIndexes();
                        return true;
                    });
                    $("a.create-section").on("click",function(e){
                        var sectionNumber = 0;
                        $("#sectionsDiv > div").each(function(index, section) {
                            if ($(section).attr("data-section-id")>sectionNumber)
                                sectionNumber=$(section).attr("data-section-id");
                        });

                        if ($('#sectionCreateModal').is(':visible')) {
                            $('#sectionCreateModal').modal('hide');
                        }

                        $("#sectionsDiv").parent().find(".loader").removeClass("hidden")

                        $.ajax({
                            url: "{{ action("ContentManagerController@serveSection") }}",
                            data: {
                                'sectionTypeKey': $(this).attr("data-section-key"),
                                'section_id': parseInt(sectionNumber)+1,
                                'upload_key': '{{ $uploadKey ?? "" }}'
                            },
                            type: 'post',
                            success: function (response) {
                                if(response != 'false')
                                    appendSectionToEnd(response);

                                tinyMCE.remove();
                                {!! ONE::addTinyMCE(".mceEdit", ['action' => action('ContentsController@getTinyMCE')]) !!}
                                $("a.translatable-status-toggler").off("click").on("click",toggleTranslatableStatus);
                                calculateIndexes();
                                $("#sectionsDiv").parent().find(".loader").addClass("hidden");
                            },
                            error: function () {

                            },
                            complete: function () {

                            }
                        });

                        e.preventDefault();
                    });
                    $("a.translatable-status-toggler").on("click",toggleTranslatableStatus);

                    {!! ONE::addTinyMCE(".mceEdit", ['action' => action('ContentsController@getTinyMCE')]) !!}
                    calculateIndexes();

                    @if (ONE::actionType("ContentManager")=="create" && ($contentType=="news" || $contentType=="events"))
                        $(document).ready(function() {
                            $("[data-section-key='{{ collect($sectionTypes)->where("code","headingSection")->first()->section_type_key }}']").trigger("click");
                        });
                    @endif
                    @if (ONE::actionType("ContentManager")=="create" && ($contentType=="articles"))
                        $(document).ready(function() {
                            $("[data-section-key='{{ collect($sectionTypes)->where("code","headingSection")->first()->section_type_key }}']").trigger("click");
                            setTimeout(function(){
                                $("[data-section-key='{{ collect($sectionTypes)->where("code","contentSection")->first()->section_type_key }}']").trigger("click");
                            }, 500);
                            setTimeout(function(){
                                $("[data-section-key='{{ collect($sectionTypes)->where("code","contentSection")->first()->section_type_key }}']").trigger("click");
                            }, 1000);
                            setTimeout(function(){
                                $("[data-section-key='{{ collect($sectionTypes)->where("code","multipleImagesSection")->first()->section_type_key }}']").trigger("click");
                            }, 1500);

                        });
                    @endif
                    @if (ONE::actionType("ContentManager")=="create" && ($contentType=="gatherings"))
                        $(document).ready(function() {

                        setTimeout(function(){
                            $("[data-section-key='{{ collect($sectionTypes)->where("code","dateSection")->first()->section_type_key }}']").trigger("click");
                        }, 1500);
                        setTimeout(function(){
                            $("[data-section-key='{{ collect($sectionTypes)->where("code","gathering_item")->first()->section_type_key }}']").trigger("click");
                        }, 1500);

                        });
                    @endif

                    @if (ONE::actionType("ContentManager")=="create" && (($contentType=="municipal_faqs") || ($contentType=="faqs")))

                            $(document).ready(function() {
                                $("[data-section-key='{{ collect($sectionTypes)->where("code","headingSection")->first()->section_type_key }}']").trigger("click");
                                setTimeout(function(){
                                    $("[data-section-key='{{ collect($sectionTypes)->where("code","contentSection")->first()->section_type_key }}']").trigger("click");
                                }, 500);

                            });
                    @endif
                });

                function appendSectionToEnd(htmlToAppend) {
                    $("#sectionsDiv").append(htmlToAppend);
                    var newElement = $("#sectionsDiv > div:last");
                    $('html, body').animate({scrollTop: newElement.offset().top}, 500)
                };
                function calculateIndexes() {
                    $("#sectionsDiv").sortable("refresh");
                    var newOrder = "";
                    $("#sectionsDiv > div").each(function(index, section) {
                        newOrder += $(section).attr("data-section-id") + "=" + $(section).attr("data-section-type") + "&";
                        $(section).find("div.box-header div.counter").html(index+1);
                    });
                    $('input[name="sortOrder"]').val(newOrder);
                }
                function removeSection(element) {
                    $("div#sectionRemoveModal").modal();
                    $("div#sectionRemoveModal button.btn").on("click",function(e) {
                        if ($(e.target).hasClass("btn-primary")) {
                            $(element).parent().parent().parent().parent().remove();
                        }

                        $("div#sectionRemoveModal button.btn").unbind("click");
                        calculateIndexes();
                    });
                }
                function toggleTranslatableStatus(event) {
                    boxElement = $(this).parent().parent().parent();
                    currentStatusInput = boxElement.find("input[name*='translationStatus']");
                    if (currentStatusInput.val()==1) {
                        currentStatusInput.val(0);

                        boxElement.find(".translatableData").addClass("hidden");
                        boxElement.find(".untranslatableData").removeClass("hidden");

                        boxElement.find(".translateMessage").addClass("hidden");
                        boxElement.find(".untranslateMessage").removeClass("hidden");
                    } else {
                        currentStatusInput.val(1);

                        boxElement.find(".translatableData").removeClass("hidden");
                        boxElement.find(".untranslatableData").addClass("hidden");

                        boxElement.find(".translateMessage").removeClass("hidden");
                        boxElement.find(".untranslateMessage").addClass("hidden");
                    }
                    event.preventDefault();
                }
        @endif
    </script>
    <style>
        .sortable-placeholder {
            border: 1px solid red;
            height:50px;
            background-color: #aaaaaa;
        }
    </style>
@endsection
