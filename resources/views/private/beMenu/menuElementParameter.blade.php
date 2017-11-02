@php
    use App\ComModules\CM;
    use App\ComModules\Orchestrator;

    if (isset($elementParameters)) {
        $elementParameters = collect($elementParameters);
        $parameters = $elementParameters->pluck("parameter");
    }
@endphp

@forelse($parameters as $parameter)
    @php
        $elementValue = "";

        if (isset($elementParameters) && $elementParameters->where("be_menu_element_parameter_id",$parameter->id)->count()>0)
            $elementValue = $elementParameters->where("be_menu_element_parameter_id",$parameter->id)->first()->value;

        $name = "parameters[" . $parameter->key . "]";
        $label = $parameter->name ?? trans("privateBEMenu.unnamed_parameter");
        $description = $parameter->description ?? "";
        $baseClasses = "form-control";
    @endphp
    {{-- Text Related Parameters --}}
    @if($parameter->code == 'url')
        {!! Form::oneText($name, ["name"=>$label,"description"=>$description], $elementValue,['id'=> $name,'class' => $baseClasses,'required'=>'required']) !!}
    @elseif($parameter->code == 'cb_type')
        @php
            $baseClasses .= " select2-default";
            $cbTypesFromComponent = Orchestrator::getCbTypesList();
            $cbTypes = collect($cbTypesFromComponent)->pluck('code','code')->toArray();
        @endphp
        {!! Form::oneSelect($name, ["name"=>$label,"description"=>$description], $cbTypes, $elementValue, null, ['id'=> $name,'class' => $baseClasses,'required'=>'required']) !!}
    @elseif($parameter->code == 'cb_key')
        @php
            $baseClasses .= " select2-default";
        @endphp
        {!! Form::oneText($name, ["name"=>$label,"description"=>$description], $elementValue,['id'=> $name,'class' => $baseClasses,'required'=>'required']) !!}
    @elseif($parameter->code == 'content_type_old' || $parameter->code == 'content_type')
        @php
            $baseClasses .= " select2-default";
            $contentTypesFromComponent = CM::getAllContentTypes();
            $contentTypes = collect($contentTypesFromComponent)->pluck('title','code')->toArray();
            foreach ($contentTypes as $contentTypeKey=>$contentType) {
                if (empty($contentType)) {
                    $contentTypes[$contentTypeKey]=$contentTypeKey;
                }
            }
        @endphp
        {!! Form::oneSelect($name, ["name"=>$label,"description"=>$description], $contentTypes, $elementValue, null, ['id'=> $name,'class' => $baseClasses,'required'=>'required']) !!}
    @else
        {{-- Fallback for unknown parameter --}}
        <div>
            {{ trans("privateBEMenu.unrecognized_parameter") }}: {{ $parameter->code }}
        </div>
    @endif
@empty
    {{ trans("privateBEMenu.no_parameters_for_element") }}
@endforelse