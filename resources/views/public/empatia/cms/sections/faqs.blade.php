<?php $content = collect($content)->sortBy('publish_date'); ?>
@foreach($content as $item)
    <div class="col-xs-12">

        <?php
        $title = '';
        $description = '';

        $titleSection =  collect($item->sections)->where("section_type.code","=","headingSection")->first();
        if($titleSection){
            $titleParameters = collect($titleSection->section_parameters)->where("section_type_parameter.code","=","textParameter")->first();
            if($titleParameters){
                $title = $titleParameters->value;
            }
        }

        $descriptionSection =  collect($item->sections)->where("section_type.code","=","contentSection")->first();
        if($descriptionSection){
            $descriptionParameters = collect($descriptionSection->section_parameters)->first();
            if($descriptionParameters){
                $description = $descriptionParameters->value;
            }
        }

        ?>
        <div class="faq-wrapper">
            <a class="faqsAccordionBtn"><h6>{{ !empty($title) ? $title : trans("public.emaptia.cms.sections.defaultMunicipalFaqsName") }}</h6></a>
            <div class="faqs-panel">
                <p>{!! $description !!}</p>
            </div>
        </div>
    </div>

@endforeach

<script>
    var acc = document.getElementsByClassName("faqsAccordionBtn");
    var i;

    for (i = 0; i < acc.length; i++) {
        acc[i].onclick = function() {
            this.classList.toggle("active");
            var panel = this.nextElementSibling;
            if (panel.style.maxHeight){
                panel.style.maxHeight = null;
                panel.style.border ="none";
            } else {
                panel.style.maxHeight = panel.scrollHeight + "px";
                panel.style.border ="solid 2px #7fb5b7";
                panel.style.borderRadius ="4px";
            }
        }
    }
</script>
