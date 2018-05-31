<div class="row">
    <div class="col-12 contentSection">
        @if (!empty(collect($section->section_parameters)->where("section_type_parameter.code","=","htmlTextArea")->first()->value ?? ""))
            {!! collect($section->section_parameters)->where("section_type_parameter.code","=","htmlTextArea")->first()->value !!}
        @endif
    </div>
</div>