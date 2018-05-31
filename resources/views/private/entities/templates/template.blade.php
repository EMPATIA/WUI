@extends('private._private.index')

@section('content')
    @php
    	$form = ONE::form('emailTemplate', trans('privateEmailTemplates.emailTemplate'), 'orchestrator', 'site_email_template')
            ->settings(["model" => isset($emailTemplate) ? $emailTemplate : null, 'id' => isset($emailTemplate) ? $emailTemplate->email_template_key : null])
            ->show('EmailTemplatesController@edit', 'EmailTemplatesController@delete', ['templateKey' => isset($templateKey) ? $templateKey : null],
                    'EntitiesSitesController@showEmailTemplates', ['siteKey' => isset($siteKey) ? $siteKey : null])
            ->create('EmailTemplatesController@store', 'EntitiesSitesController@showEmailTemplates', ['siteKey' => isset($siteKey) ? $siteKey : null])
            ->edit('EmailTemplatesController@update', 'EmailTemplatesController@show', ['templateKey' => isset($templateKey) ? $templateKey : null])
            ->open();
    @endphp


    {!! Form::hidden('siteKey', isset($siteKey)? $siteKey : null) !!}
    {!! Form::hidden('typeKey', isset($typeKey)? $typeKey: null) !!}
    {!! Form::hidden('templateKey', isset($templateKey)? $templateKey :null) !!}

    {{--{!! Form::oneSelect("types", trans("privateEmails.Type"), isset($typesName) ? $typesName: null, isset($emailTemplate->type) ? $emailTemplate->type->type_key : null, isset($emailTemplate->type->name) ? $emailTemplate->type->name : null, ['class' => 'form-control', 'id' => 'types','required']) !!}--}}
    {!! Form::hidden('types', isset($emailTemplate->type->type_key)? $emailTemplate->type->type_key :null) !!}
    <div class="">
        <div class="box-header">
            <h3 class="box-title"><i class="fa"></i> {{ trans('privateEmailTemplates.emailTemplateTranslations') }}</h3>
        </div>
        <div class="">
            <div class="@if(ONE::actionType('emailTemplate') == 'show') col-md-12 @endif col-12">
                @if(count($languages) > 0)
                    @foreach($languages as $language)
                        @php $form->openTabs('tab-translation' . $language->code, $language->name); @endphp
                        <div style="padding:10px;">
                            {!! Form::oneText('subject_'.$language->code, trans('privateEmails.subject'), !empty($emailTemplate->translations->{$language->code}->subject) ? $emailTemplate->translations->{$language->code}->subject : null, ['class' => 'form-control', 'id' => 'subject_'.$language->code,
                             (isset($language->default) && $language->default == true ? 'required' : null)]) !!}

                            @if(ONE::actionType('emailTemplate') == 'show')
                                <dt><i class="fa fa-eye"></i> {{ trans('privateSites.preview') }}</dt>

                                <div style="border:1px solid #999999;width:100%;height:350px;overflow-y: auto">
                                    {{ html_entity_decode(!empty($emailTemplate->translations->{$language->code}->content) ? $emailTemplate->translations->{$language->code}->content : null) }}
                                </div>
                                <hr style="margin: 10px 0 10px 0">
                            @else
                                {!! Form::textarea($language->default == true ? 'content_'.$language->code : 'content_'.$language->code,
                                    !empty($emailTemplate->translations->{$language->code}->content) ? $emailTemplate->translations->{$language->code}->content : null,
                                    ['class' => 'form-control templates', 'id' => 'content_'.$language->code]) !!}
                            @endif
                        </div>
                    @endforeach
                    @php $form->makeTabs(); @endphp
                @endif
            </div>
            @if (ONE::isEdit() && !empty($emailTemplate->type->tags ?? []))
                <div class="col-xs-12">
                    <div style="padding:10px;">
                        <h3>
                            {{ trans('privateEmailTemplates.existing_tags') }}
                        </h3>
                        <table id="tags_list" class="table table-striped dataTable no-footer">
                            <thead>
                                <tr>
                                    <th>{{ trans('privateEmailTemplates.tag_code') }}</th>
                                    <th>{{ trans('privateEmailTemplates.tag_name') }}</th>
                                    <th>{{ trans('privateEmailTemplates.tag_description') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($emailTemplate->type->tags as $tag)
                                    <tr>
                                        <td>
                                            #{{ $tag->code }}
                                        </td>
                                        <td>
                                            {{ $tag->name }}
                                        </td>
                                        <td>
                                            {{ $tag->description }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                    </div>
                </div>
            @endif
        </div>
    </div>
    {!! $form->make() !!}
@endsection

@section('scripts')
    <script>
        $(function() {
            getSidebar('{{ action("OneController@getSidebar") }}', 'emailTemplates', "{{(isset($siteKey) ? $siteKey : null)}}", 'site' )
        });
        $(document).ready(function() {
            $('#tags_list').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },
                columns: [
                    { name: 'code', width: "100px" },
                    { name: 'name', width: "200px" },
                    { name: 'description'}
                ],
            });
        } );
    </script>

    <script src="{{ asset("js/tinymce/tinymce.min.js") }}"></script>


    <script>
        $(document).ready(function(){

            {!! ONE::addTinyMCE(".templates", ['action' => action('ContentManagerController@getTinyMCE')]) !!}

        });

    </script>
@endsection