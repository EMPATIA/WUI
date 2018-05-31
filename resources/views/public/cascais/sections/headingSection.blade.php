<div class="content-title-wrapper row">
    <div class="col-12">
        <?php
            $title = collect($section->section_parameters)->where("section_type_parameter.code","=","textParameter")->first()->value ?? "";
            $size = collect($section->section_parameters)->where("section_type_parameter.code","=","headingNumber")->first()->value ?? "";
            $color = collect($section->section_parameters)->where("section_type_parameter.code","=","color")->first()->value ?? "";

            $class = "class='content-title'";
            if (empty($size))
                $size =  '1';


            if (!empty($color))
                $color = "style='color:" . $color . ";'";
        ?>
        @if (!empty($title) && !empty($size))
            <h{{ $size }} {!! $class !!} {!! $color !!}>
                {{ $title }}
            </h{{ $size }} >
        @endif
    </div>
</div>