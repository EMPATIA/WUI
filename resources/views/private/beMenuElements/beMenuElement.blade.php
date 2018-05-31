@extends('private._private.index')

@section('content')

    <div class="row">
        <div class="col-md-12">
            @php
                $form = ONE::form('BEMenuElements', trans('privateBEMenuElement.details'))
                    ->settings(["model" => isset($element) ? $element : null, 'id' => isset($element) ? $element->key : null])
                    ->show('BEMenuElementsController@edit','BEMenuElementsController@delete', ['key' => isset($element) ? $element->key : null],'BEMenuElementsController@index')
                    ->create('BEMenuElementsController@store', 'BEMenuElementsController@show', ['key' => isset($element) ? $element->key : null])
                    ->edit('BEMenuElementsController@update', 'BEMenuElementsController@show', ['key' => isset($element) ? $element->key : null])
                    ->open();
            @endphp

            {!! Form::oneText('code', trans('privateBEMenuElement.code'), isset($elementParameter) ? $elementParameter->controller : null, ['class' => 'form-control'] ) !!}
            {!! Form::oneText('controller', trans('privateBEMenuElement.controller'), isset($elementParameter) ? $elementParameter->controller : null, ['class' => 'form-control'] ) !!}
            {!! Form::oneText('method', trans('privateBEMenuElement.method'), isset($elementParameter) ? $elementParameter->method : null, ['class' => 'form-control'] ) !!}

            <div class="row">
                <div class="col-12">
                    @if(count($languages) > 0)
                        @foreach($languages as $language)
                            @php $form->openTabs('tab-translation' . $language->code, $language->name); @endphp
                            <div style="">
                                {!! Form::oneText('name_'.$language->code, ['name' => trans('privateBEMenuElement.name'),'description' => trans('privateBEMenuElement.help_name')],($element->translations->{$language->code}->name ??  null), ['class' => 'form-control', 'id' => 'name_'.$language->code]) !!}
                                {!! Form::oneTextArea('description_'.$language->code, ['name' => trans('privateBEMenuElement.description'),'description' => trans('privateBEMenuElement.help_description')],($element->translations->{$language->code}->description ??  null), ['class' => 'form-control', 'id' => 'description_'.$language->code]) !!}
                            </div>
                        @endforeach
                        @php $form->makeTabs(); @endphp
                    @endif
                </div>
                <div class="col-12">
                    <div class="card" style="border-radius: 0">
                        <div class="card-body">
                            <h4>{{ trans('privateBEMenuElement.permissions_info') }}</h4>
                            {!! Form::oneText('module_code', trans('privateBEMenuElement.module_code'), isset($elementParameter) ? $elementParameter->module_code : null, ['class' => 'form-control'] ) !!}
                            {!! Form::oneText('module_type_code', trans('privateBEMenuElement.module_type_code'), isset($elementParameter) ? $elementParameter->module_type_code : null, ['class' => 'form-control'] ) !!}
                            {!! Form::oneText('permission', trans('privateBEMenuElement.permission'), isset($elementParameter) ? $elementParameter->permission : null, ['class' => 'form-control'] ) !!}
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card" style="border-radius: 0">
                        <div class="card-body">
                            <h4>{{ trans('privateBEMenuElement.parameters_info') }}</h4>
                            @if(ONE::actionType('BEMenuElements') == "create" || ONE::actionType('BEMenuElements') == "edit")
                                <div class="row">
                                    <div class="col-12 col-md-8">
                                        <div class="container-fluid" style="background-color:#CCC;">
                                            <div class="dd" id="nestable">
                                                <ol class="dd-list">
                                                    @if (isset($element->parameters))
                                                        @forelse($element->parameters as $key => $parameter)
                                                            <li class='dd-item nested-list-item' data-key='{{ $parameter->key }}'>
                                                                <div class='dd-handle nested-list-handle'>
                                                                    <span class='glyphicon glyphicon-move'></span>
                                                                </div>
                                                                <div class='nested-list-content'>
                                                                    <input type="text" name="{{ $parameter->key }}-code" id="{{ $parameter->key }}-name"
                                                                           placeholder="{{ trans('privateBEMenuElement.parameter_name') }}"
                                                                           value="{{ $parameter->pivot->code ?? "" }}"
                                                                    />
                                                                    {{ $parameter->name }}
                                                                    <div class='pull-right' style='color: #fffbfe;'>
                                                                        <a href="#" class="btn btn-flat btn-danger btn-sm remove-parameter-selector" data-toggle="tooltip"
                                                                           data-key='{{ $parameter->key }}'>
                                                                            <i class="fa fa-remove"></i>
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </li>
                                                        @empty

                                                        @endforelse
                                                    @endif
                                                </ol>
                                            </div>
                                            <input type="hidden" name="parameters-order" id="parameters-order" value="">
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-4">
                                        @forelse($parameters as $key => $parameter)
                                            <div data-key="{!! $parameter->key!!}" class="parameter-selector @if (isset($selectedParameters) && array_key_exists($parameter->key,$selectedParameters)) selected @endif">
                                                {!! $parameter->name !!}
                                            </div>
                                        @empty
                                            {{ trans('privateBEMenuElement.no_parameters_available') }}
                                        @endforelse
                                    </div>
                                </div>
                            @else
                                @forelse ($element->parameters as $parameter)
                                    @if ($loop->first)
                                        <hr>
                                        <h4 class="box-title">{{ trans('privateBEMenuElement.parameters') }}</h4>
                                        <table id="parameters" class="table  table-hover table-striped table-responsive">
                                            <thead>
                                            <tr>
                                                <th>{{ trans('privateBEMenuElement.parameter_key') }}</th>
                                                <th>{{ trans('privateBEMenuElement.parameter_name') }}</th>
                                                <th>{{ trans('privateBEMenuElement.parameter_code') }}</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @endif
                                            <tr>
                                                <td>
                                                    <a href="{{ action("BEMenuElementParametersController@show",["key"=>$parameter->key]) }}">
                                                        {{ $parameter->key }}
                                                    </a>
                                                </td>
                                                <td>{{ $parameter->name }}</td>
                                                <td>{{ $parameter->pivot->code ?? trans("privateBEMenuElement.no_code_defined") }}</td>
                                            </tr>
                                            @if ($loop->last)
                                            </tbody>
                                        </table>
                                    @endif
                                @empty
                                    {{ trans('privateBEMenuElement.no_parameters_available') }}
                                @endforelse
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {!! $form->make() !!}
        </div>
    </div>
@endsection

@section("scripts")
    <script>
        @if(ONE::actionType('BEMenuElements') != "show")
            $(document).ready(function() {
                $('#nestable')
                    .nestable({
                        maxDepth: 1
                    })
                    .on("change",function() {
                        updateParametersOrderInput();
                    });

                updateListeners();
                updateParametersOrderInput();
            });

            function updateListeners() {
                $(".remove-parameter-selector")
                    .off("click")
                    .on("click",function (e) {
                        e.preventDefault();

                        parameterKey = $(this).attr("data-key");

                        $("li.dd-item.nested-list-item[data-key='" + parameterKey + "']").remove();
                        $("div.parameter-selector[data-key='" + parameterKey + "']").removeClass("selected");

                        updateListeners();
                        return false;
                    });

                $(".parameter-selector:not(.selected)")
                    .off("click")
                    .on("click",function(e) {
                        clickedElement = $(this);

                        html =
                            "<li class='dd-item nested-list-item' data-key='" + clickedElement.attr("data-key") + "'>" +
                                "<div class='dd-handle nested-list-handle'>" +
                                    "<span class='glyphicon glyphicon-move'></span>" +
                                "</div>" +
                                "<div class='nested-list-content'>" +
                                    "<input type='text' name='" + clickedElement.attr("data-key") + "-code' id='" + clickedElement.attr("data-key") + "-code' placeholder='{{ trans('privateBEMenuElement.parameter_name') }}'/>" +
                                    clickedElement.text() +
                                    "<div class='pull-right' style='color: #fffbfe;'>" +
                                        "<a href='#' class='btn btn-flat btn-danger btn-sm remove-parameter-selector' data-key='" + clickedElement.attr("data-key") + "'>" +
                                            "<i class='fa fa-remove'></i>" +
                                        "</a>" +
                                    "</div>" +
                                "</div>" +
                            "</li>";

                        $('#nestable > ol').append(html);
                        $('#nestable').nestable('refresh');

                        clickedElement.addClass("selected");

                        updateListeners();
                        updateParametersOrderInput();
                    });
            }
            function updateParametersOrderInput() {
                $("#parameters-order").val(JSON.stringify($('#nestable').nestable("serialize")));
            }
        @endif
    </script>
    <style>
        .parameter-selector.selected {
            pointer-events: none;
            text-decoration: line-through;
        }
        .nested-list-handle {
            padding-left: 9px;
            padding-top: 7px;
            height: 35px;
            width: 35px;
        }
        .nested-list-content {
            height: 35px;
        }
    </style>
@endsection