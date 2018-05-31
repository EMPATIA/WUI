@if (!empty(collect($section->section_parameters)->where("section_type_parameter.code","=","multipleFiles")->first()->value ?? ""))
    <?php $files = json_decode(collect($section->section_parameters)->where("section_type_parameter.code","=","multipleFiles")->first()->value); ?>
    @if (count($files)>0)
        <div class="container pad-0">
            @foreach($files as $file)
                <div class="document-container">
                    @include('public.default.sections.multipleFilesSectionObject')
                </div>
            @endforeach
        </div>

    @endif
@endif
