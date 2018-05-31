@extends('private._private.index')

@section('content')
    @php 
        $form = ONE::form('openData', trans('privateOpenData.title'))
            ->show($isEditable ? "OpenDataController@edit" : null,null)
            ->edit($isEditable ? "OpenDataController@update" : null, $isEditable ? "OpenDataController@show" : null)
            ->open();
    @endphp
    <div class="card">
        <div class="card-block" style="padding:10px 20px;">
            @if(!$isEditable)
                <p class="card-text">
                    <label for="contentStatusComment">
                        {{ trans("privateOpenData.entity") }}:
                    </label>
                    {{ $entity->name }}
                </p>
            @endif
            @if(!empty($openData))
                <p class="card-text">
                    <label for="contentStatusComment">
                        {{ trans("privateOpenData.access_url") }}:
                    </label>
                    {{ action("OpenDataController@export",$openData->token) }}
                </p>
                <p class="card-text">
                    <label for="contentStatusComment">
                        {{ trans("privateOpenData.created_by") }}:
                    </label>
                    {{ $openData->creator->name }}
                </p>
                <p class="card-text">
                    <label for="contentStatusComment">
                        {{ trans("privateOpenData.created_at") }}:
                    </label>
                    {{ $openData->created_at }}
                </p>
                <p class="card-text">
                    <label for="contentStatusComment">
                        {{ trans("privateOpenData.last_export") }}:
                    </label>
                    @if(!empty($openData->last_export_date))
                        {{ $openData->last_export_date }}
                    @else
                        {{ trans("privateOpenData.not_exported_yet") }}
                    @endif
                </p>
            @else
                {{ trans("privateOpenData.open_data_not_configured") }}
            @endif
        </div>
    </div>
    <br>
    <div id="users-data" class="card panel-default">
        <div class="card-header card-header-gray" role="tab">
            <h5 class="panel-title onoffswitch-labelTxt">
                <div class="pull-left">
                    {!! Form::oneSwitch("user_parameters_switch", null, !empty($entityOpenDataConfigurations["user_parameters"]) ,array('id' => "user_parameters_switch", 'data-target' => '#users-panel') ) !!}
                </div>
                &nbsp;
                <label class="label-module-title">
                    {{ trans("privateOpenData.user_parameters") }}
                </label>
            </h5>
        </div>
        <div id="users-panel" class="panel-collapse collapse @if(!empty($entityOpenDataConfigurations["user_parameters"])) show @endif" role="tabpanel">
            <div class="card-body">
                <div class="row">
                    @forelse($parameterUserTypes as $parameter)
                        @php
                            $readOnly = !One::isEdit() || ($parameter->anonymizable!=0) || (count($parameter->parameter_user_options)>0 && $parameter->minimum_users>0 && collect($parameter->parameter_user_options)->where("user_parameters_count","<",$parameter->minimum_users)->count()>0);
                        @endphp
                        <div class="col-12 background-hover">
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <label for="{{ $parameter->parameter_user_type_key }}">
                                        {{ $parameter->name }}
                                    </label>
                                </div>
                                <div class="col-12 col-md-2">
                                    {!! Form::oneSwitch("user_parameters[]", null, !empty($entityOpenDataConfigurations["user_parameters"][$parameter->parameter_user_type_key]) , array("readonly"=> $readOnly, 'id' => $parameter->parameter_user_type_key, 'value' => $parameter->parameter_user_type_key) ) !!}
                                </div>
                                <div class="col-12 col-md-4">
                                    @if($readOnly && One::isEdit())
                                        @if($parameter->anonymizable!=0)
                                            {{ trans("privateOpenData.anonymizable_ppi_parameters_cant_be_exported") }}
                                        @elseif(count($parameter->parameter_user_options)>0 && $parameter->minimum_users>0 && collect($parameter->parameter_user_options)->where("user_parameters_count","<",$parameter->minimum_users)->count()>0)
                                            {{ trans("privateOpenData.minimum_users_per_option_not_respected") }}
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            {{ trans("privateOpenData.no_user_parameters") }}
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    <br>
    <div id="cbs-data" class="card panel-default">
        <div class="card-header card-header-gray" role="tab">
            <h5 class="panel-title onoffswitch-labelTxt">
                <div class="pull-left">
                    {!! Form::oneSwitch("cbs_switch", null, !empty($entityOpenDataConfigurations["cbs"]),array('id' => "cbs_switch", 'data-target' => '#cbs-panel') ) !!}
                </div>
                &nbsp;
                <label class="label-module-title">
                    {{ trans("privateOpenData.cbs") }}
                </label>
            </h5>
        </div>
        <div id="cbs-panel" class="panel-collapse collapse @if(!empty($entityOpenDataConfigurations["cbs"])) show @endif" role="tabpanel">
            <div class="card-body">
                <div class="row">
                    @foreach($cbs as $cb)
                        <div class="col-12">
                            <div id="cbs-data-{{ $cb->cb_key }}" class="card panel-default">
                                <div class="card-header card-header-gray" role="tab">
                                    <h5 class="panel-title onoffswitch-labelTxt">
                                        <div class="pull-left">
                                            {!! Form::oneSwitch("cbs[" . $cb->cb_key . "][switch]", null, (!empty($entityOpenDataConfigurations["cbs"][$cb->id]["parameters"]) || !empty($entityOpenDataConfigurations["cbs"][$cb->id]["votes"])),array('id' => "cbs_switch_" . $cb->cb_key, 'data-target' => '#cb-' . $cb->cb_key . '-panel') ) !!}
                                        </div>
                                        &nbsp;
                                        <label class="label-module-title">
                                            {{ $cb->title }}
                                        </label>
                                    </h5>
                                </div>
                                <div id="cb-{{ $cb->cb_key }}-panel" class="panel-collapse collapse @if(!empty($entityOpenDataConfigurations["cbs"][$cb->id]["parameters"]) || !empty($entityOpenDataConfigurations["cbs"][$cb->id]["votes"])) show @endif" role="tabpanel">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-12">
                                                <h5>{{ trans("privateOpenData.cb_parameters") }}</h5>
                                            </div>
                                            @forelse($cb->parameters as $parameter)
                                                @php
                                                    $readOnly = !One::isEdit();
                                                @endphp 
                                                <div class="col-12 background-hover">
                                                    <div class="row">
                                                        <div class="col-12 col-md-6">
                                                            <label for="{{ $cb->cb_key }}-{{ $parameter->id }}">
                                                                {{ $parameter->parameter }}
                                                            </label>
                                                        </div>
                                                        <div class="col-12 col-md-2">
                                                            {!! Form::oneSwitch("cbs[" . $cb->cb_key ."][parameters][]", null, !empty($entityOpenDataConfigurations["cbs"][$cb->id]["parameters"][$parameter->id]), array("readonly"=> $readOnly, 'id' => $cb->cb_key . "-" .$parameter->id, 'value' => $parameter->id) ) !!}
                                                        </div>
                                                        <div class="col-12 col-md-4">

                                                        </div>
                                                    </div>
                                                </div>
                                            @empty
                                                <div class="col-12">
                                                    {{ trans("privateOpenData.no_parameters_for_cb") }}
                                                </div>
                                            @endforelse
                                            <div class="col-12">
                                                <hr>
                                                <h5>{{ trans("privateOpenData.cb_votes") }}</h5>
                                            </div>
                                            @forelse($cb->votes as $vote)
                                                <div class="col-12 background-hover">
                                                    <div class="row">
                                                        <div class="col-12 col-md-6">
                                                            <label for="{{ $cb->cb_key }}-{{ $vote->vote_key }}">
                                                                {{ $vote->name }}
                                                            </label>
                                                        </div>
                                                        <div class="col-12 col-md-2">
                                                            {!! Form::oneSwitch("cbs[" . $cb->cb_key ."][votes][]", null, !empty($entityOpenDataConfigurations["cbs"][$cb->id]["votes"][$vote->vote_key]), array('id' => $cb->cb_key . "-" .$vote->vote_key, 'value' => $vote->vote_key) ) !!}
                                                        </div>
                                                        <div class="col-12 col-md-4">

                                                        </div>
                                                    </div>
                                                </div>
                                            @empty
                                                <div class="col-12">
                                                    {{ trans("privateOpenData.no_parameters_for_cb") }}
                                                </div>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <br>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    {!! $form->make() !!}
@endsection

@section("scripts")
    @if(One::isEdit())
        <script>
            function collapseCard(element) {
                card = element.parent().attr("data-target");
                if (card !== undefined) {
                    if (element.is(":checked"))
                        $(card).collapse("show");
                    else
                        $(card).collapse("hide");
                }
            }
            @php
                $collapsibleSelectors = "#user_parameters_switch, #cbs_switch";

                foreach ($cbs as $cb) {
                    $collapsibleSelectors .= ", #cbs_switch_" . $cb->cb_key;
                }
            @endphp
            $("{{ $collapsibleSelectors }}").on("click",function() {
                collapseCard($(this));
            });
        </script>
    @endif
@endsection