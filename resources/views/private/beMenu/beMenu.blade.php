@extends('private._private.index')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <?php
                if(!empty($currentUser) && !empty($userKey))
                    $form = ONE::form('BEUserMenu', trans('privateBEMenu.details'))
                        ->settings(["model" => isset($element) ? $element : null, 'id' => isset($element) ? $element->menu_key : null])
                        ->show('UserBEMenuController@edit','UserBEMenuController@delete', ['key' => isset($element) ? $element->menu_key : null],'UserBEMenuController@index')
                        ->create('UserBEMenuController@store', 'UserBEMenuController@show', ['key' => isset($element) ? $element->menu_key : null])
                        ->edit('UserBEMenuController@update', 'UserBEMenuController@show', ['key' => isset($element) ? $element->menu_key : null])
                        ->open();
                    
                elseif(!empty($userKey))
                   $form = ONE::form('BEUserMenu', trans('privateBEMenu.details'))
                        ->settings(["model" => isset($element) ? $element : null, 'id' => isset($element) ? $element->menu_key : null])
                        ->show('UserBEMenuController@userEdit','UserBEMenuController@userDelete', ['userKey' => $userKey, 'key' => isset($element) ? $element->menu_key : null],'UserBEMenuController@userIndex',['userKey' => $userKey])
                        ->create('UserBEMenuController@userStore', 'UserBEMenuController@userShow', ['userKey' => $userKey, 'key' => isset($element) ? $element->menu_key : null])
                        ->edit('UserBEMenuController@userUpdate', 'UserBEMenuController@userShow', ['userKey' => $userKey, 'key' => isset($element) ? $element->menu_key : null])
                        ->open();
                else
                    $form = ONE::form('BEMenu', trans('privateBEMenu.details'))
                        ->settings(["model" => isset($element) ? $element : null, 'id' => isset($element) ? $element->menu_key : null])
                        ->show('BEMenuController@edit','BEMenuController@delete', ['key' => isset($element) ? $element->menu_key : null],'BEMenuController@index')
                        ->create('BEMenuController@store', 'BEMenuController@show', ['key' => isset($element) ? $element->menu_key : null])
                        ->edit('BEMenuController@update', 'BEMenuController@show', ['key' => isset($element) ? $element->menu_key : null])
                        ->open();
            ?>
            @if(ONE::actionType(!empty($userKey)?'BEUserMenu':'BEMenu') == "create")
                <div class="row">
                    <div class="col-12">
                        <h4>{{ trans('privateBEMenu.choose_menu_link_type') }}</h4>
                    </div>
                    <div class="col-12" style="padding-bottom: 20px">
                        <select id="menuElement" name="menuElement" class="form-control">
                            <option value=""></option>
                            @forelse($menuElements as $key => $element)
                                <option value="{!! $element->key!!}">{!! $element->name !!}</option>
                            @empty
                                <option selected>{{ trans('privateBEMenu.no_elements_available') }}</option>
                            @endforelse
                        </select>
                    </div>

                    <div class="col-12"><hr></div>

                    <div id="second-step-title" class="col-12 hidden">
                        <h4>{{ trans('privateBEMenu.fill_the_parameters') }}</h4></div>
                    <div id="second-step-loader" class="col-12 text-center hidden" style="padding-bottom: 20px">
                        <img src="{{ asset('images/bluePreLoader.gif') }}" alt="Loading"  style="width: 40px;"/>
                    </div>
                    <div id="second-step-container" class="col-12 hidden" style="padding-bottom: 20px"></div>

                    <div class="col-12"><hr></div>

                    <div class="col-12 hidden translations">
                        <h4>{{ trans('privateBEMenu.translations') }}</h4>
                    </div>
                    <div class="col-12 hidden translations">
                        @if(count($languages) > 0)
                            @foreach($languages as $language)
                                @php $form->openTabs('tab-translation' . $language->code, $language->name); @endphp
                                {!! Form::oneText('name_'.$language->code, ['name' => trans('privateBEMenu.name'),'description' => trans('privateBEMenu.help_name')],($element->translations->{$language->code}->name ??  null), ['class' => 'form-control', 'id' => 'name_'.$language->code]) !!}
                            @endforeach
                            @php $form->makeTabs(); @endphp
                        @endif
                    </div>
                </div>
            @elseif(ONE::actionType(!empty($userKey)?'BEUserMenu':'BEMenu') == "edit")
                {!! Form::oneText("",trans('privateBEMenu.menu_link_type'),$element->menu_element->name,["disabled"=>"disabled"]) !!}
                @include("private.beMenu.menuElementParameter",["elementParameters"=>$element->parameters])
                {{--@foreach ($element->parameters as $parameter)--}}
                {{--{!! Form::oneText("",['name' => $parameter->parameter->name,'description' => $parameter->parameter->description ?? ""],$parameter->value) !!}--}}
                {{--@endforeach--}}

                @if(count($languages) > 0)
                    @foreach($languages as $language)
                        @foreach($element->translations as $translation)
                            @if ($translation->language_code==$language->code)
                                @php $form->openTabs('tab-translation' . $language->code, $language->name); @endphp
                                {!! Form::oneText('name_'.$language->code,trans('privateBEMenu.name'),($translation->name ??  null), ['class' => 'form-control', 'id' => 'name_'.$language->code]) !!}
                            @endif
                        @endforeach
                    @endforeach
                    @php $form->makeTabs(); @endphp
                @endif
            @else
                {!! Form::oneText("",trans('privateBEMenu.position'),$element->position) !!}
                {!! Form::oneText("",trans('privateBEMenu.menu_link_type'),$element->menu_element->name) !!}

                @foreach ($element->parameters as $parameter)
                    {!! Form::oneText("",['name' => $parameter->parameter->name,'description' => $parameter->parameter->description ?? ""],$parameter->value) !!}
                @endforeach

                @if(count($languages) > 0)
                    @foreach($languages as $language)
                        @foreach($element->translations as $translation)
{{--
                            @if ($translation->language_code==$language->code)
--}}
                                @php $form->openTabs('tab-translation' . $language->code, $language->name); @endphp
                                {!! Form::oneText('name_'.$language->code,trans('privateBEMenu.name'),($translation->name ??  null), ['class' => 'form-control', 'id' => 'name_'.$language->code]) !!}
{{--
                            @endif
--}}
                        @endforeach
                    @endforeach
                    @php $form->makeTabs(); @endphp
                @endif
            @endif
            {!! $form->make() !!}
        </div>
    </div>
@endsection

@section("scripts")
    <script>
        @if(ONE::actionType(!empty($userKey)?'BEUserMenu':'BEMenu') == "create")
            $(document).ready(function () {
                $("#menuElement")
                    .select2({
                        'placeholder': "{{ trans('privateBEMenuParameters.select_element') }}"
                    })
                    .on("select2:select", function () {
                        selectElement = $("#menuElement");
                        secondStepTitle = $("#second-step-title");
                        secondStepLoader = $("#second-step-loader");
                        secondStepContainer = $("#second-step-container");

                        selectElement.attr("disabled","disabled");

                        secondStepTitle.removeClass("hidden");
                        secondStepLoader.removeClass("hidden");

                        secondStepContainer.html("").addClass("hidden");

                        $.ajax({
                            url: '{{ action("BEMenuController@getElementParameters") }}',
                            method: 'POST',
                            data: {
                                menuElement: selectElement.val(),
                                _token: "{{ csrf_token()}}"
                            }, success: function(response){
                                secondStepContainer
                                    .html(response)
                                    .removeClass("hidden");

                                secondStepLoader.addClass("hidden");

                                selectElement.removeAttr("disabled");

                                $(".translations").removeClass("hidden");
                            }, error: function(msg){
                                console.log(msg);
                                selectElement.removeAttr("disabled");
                            }
                        });
                    });
            });
        @endif
    </script>
@endsection