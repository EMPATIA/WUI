@extends('private._private.index')

@section('content')
    <!-- Plupload Javascript fix and bootstrap fix @ start -->
    <link rel="stylesheet" href="/bootstrap/plupload-fix/bootstrap.css">
    <script src="/bootstrap/plupload-fix/bootstrap.min.js"></script>
    <!-- Plupload Javascript fix and bootstrap fix @ End -->
    <script src="{{ asset('vendor/jildertmiedema/laravel-plupload/js/plupload.full.min.js') }}"></script>

    @php
        $form = ONE::form('siteConfValues', trans('privateSiteConfValues.configurations'), 'orchestrator', 'site_configurations')
            ->settings(["model" => isset($siteConfs) ? $siteConfs : null, 'id' => isset($siteConfs[0]) ? $siteConfs[0]->id : null])
            ->show('SiteConfValuesController@edit', null, ['id' => $siteKey],
                null, null)
            ->create('EntitiesSitesController@store', 'EntitiesSitesController@index', ['entityKey' => isset($entityKey) ? $entityKey : null])
            ->edit('SiteConfValuesController@update', 'SiteConfValuesController@index', ['id' => $siteKey ? $siteKey : null])
            ->open();
    @endphp
    {{--<div class="box box-primary">
        <div class="box-body">
            <table class="table table-hover table-striped dataTable no-footer table-responsive">
                <tbody>
                @foreach($siteConfGroups as $group)
                    <tr>
                        <th> <a onclick="getSiteConf(this)" id="{{ $group->code }}_{{$group->site_conf_group_key}}" href="#">{{$group->name}}</a></th>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>--}}
    {!! Form::hidden('siteKey',$siteKey) !!}

    <div class=" top-2" id="accordion" role="tablist" aria-multiselectable="true">
        {{--{{ dd($siteConfGroups) }}--}}
        @foreach($siteConfGroups as $group)
            <div class="card">
                <div class="card-header" role="tab" id="collapse-summary-title">
                    <h5 class="no-margin">
                        <a role="button" data-toggle="collapse" data-parent="#collapse-{{$group->code}}" href="#collapse-{{$group->code}}" aria-expanded="true" aria-controls="collapse-{{$group->code}}">
                            <i class="fa fa-chevron-down" aria-hidden="true"></i>
                            {{$group->name}}
                        </a>
                    </h5>
                </div>
                <div id="collapse-{{$group->code}}" class="panel-collapse collapse" role="tabpanel" aria-labelledby="collapse-summary-title">
                    <div class="card-body">
                        @if(!empty($group->subgroup))
                            @foreach($group->subgroup as $subgroup)
                                @if(str_contains($subgroup->code,'file_'))
                                    {!! Form::oneFileUpload($subgroup->code, $subgroup->name, (isset($subgroup->siteConfValues[0]) ? json_decode($subgroup->siteConfValues[0]->value) : []),$uploadKey)!!}
                                @elseif(str_contains($subgroup->code,'color_'))
                                    {!! Form::oneColor($subgroup->code, $subgroup->name, $subgroup->siteConfValues[0]->value ?? '', ['class' => 'form-control', 'id' => $subgroup->code]) !!}
                                @elseif(str_contains($subgroup->code,'boolean_'))
                                    {!! Form::hidden($subgroup->code, 0) !!}
                                    {!! Form::oneSwitch($subgroup->code, $subgroup->name, isset($subgroup->siteConfValues[0]->value) ? $subgroup->siteConfValues[0]->value : null) !!}
                                @elseif(str_contains($subgroup->code, 'html_'))
                                    {!! Form::oneTextArea($subgroup->code, $subgroup->name, $subgroup->siteConfValues[0]->value ?? '', ['class' => 'form-control tinyMCE', 'size' => '30x2', 'style' => 'resize: vertical', 'id' => $subgroup->code]) !!}
                                @else
                                    {!! Form::oneText($subgroup->code, $subgroup->name, $subgroup->siteConfValues[0]->value ?? '', ['class' => 'form-control', 'id' => $subgroup->code]) !!}
                                @endif
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        @endforeach

    </div>


    {{--
    <div class="box box-primary margin-top" >
        <div class="box-body" id="siteConfsValues">

        </div>
    </div>
    --}}


    {!! $form->make() !!}
@endsection

@section('scripts')
    <script src="{{ asset("js/tinymce/tinymce.min.js") }}"></script>
    <script>
        {!! ONE::addTinyMCE(".tinyMCE", ['action' => action('ContentManagerController@getTinyMCE')]) !!}
        function getSiteConf(elem){
            var val = elem.id;
            $("#siteConfsValues").empty();
            $.ajax({
                'method': 'get', // Type of response and matches what we said in the route,
                'url': '{{action('SiteConfValuesController@getSiteConfsFromGroup')}}', // This is the url we gave in the route
                'data': {key: val}, // a JSON object to send back
                success: function (response) { // What to do if we succeed
                    $.each(response, function(index, value){
                        var title = '<label for="value'+index+'">'+value.name+'</label>';
                        var content = '<input type="text" class="form-control" name="value'+index+'" value="'+value.value+'">';
                        $("#siteConfsValues").append(title)
                        $("#siteConfsValues").append(content)
                    })
                },
                error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                }
            })
        }
    </script>
@endsection