@if(!empty($content->sections))
    @foreach ($content->sections as $section)
        @includeif("public.default.sections." . $section->section_type->code)
    @endforeach
@endif
