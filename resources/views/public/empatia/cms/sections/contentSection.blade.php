<div class="contentSection">
        {!! collect($section->section_parameters)->where("section_type_parameter.code","=","htmlTextArea")->first()->value !!}
</div>