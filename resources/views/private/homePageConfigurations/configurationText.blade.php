
@php /*
@if(isset($languages))
    @foreach($languages as $language)
        @php $form->openTabs('tab-text-translation-' . $language->code.'-'.(isset($child) ? $child->home_page_type_key : $homePageType->home_page_type_key), $language->name);@endphp
        <div style="padding: 10px;">
            {!! Form::oneText($language->code .'_'.(isset($child) ? $child->home_page_type_key : $homePageType->home_page_type_key),
            (isset($child) ? $child->name : $homePageType->name),
            isset($homePageConfiguration[(isset($child) ? $child->home_page_type_key : $homePageType->home_page_type_key)]['translations'][$language->code]) ?
            $homePageConfiguration[(isset($child) ? $child->home_page_type_key : $homePageType->home_page_type_key)]['translations'][$language->code]['value'] : null,
            ['class' => 'form-control', 'id' => $language->code .'_'.(isset($child) ? $child->home_page_type_key : $homePageType->home_page_type_key),($language->default == true ? 'required':null)]) !!}
        </div>
    @endforeach
    @php $form->makeTabs(); @endphp
@endif
*/ @endphp

{!! Form::oneText($language->code .'_'.(isset($child) ? $child->home_page_type_key : $homePageType->home_page_type_key),
(isset($child) ? $child->name : $homePageType->name),
isset($homePageConfiguration[(isset($child) ? $child->home_page_type_key : $homePageType->home_page_type_key)]['translations'][$language->code]) ?
$homePageConfiguration[(isset($child) ? $child->home_page_type_key : $homePageType->home_page_type_key)]['translations'][$language->code]['value'] : null,
['class' => 'form-control', 'id' => $language->code .'_'.(isset($child) ? $child->home_page_type_key : $homePageType->home_page_type_key),($language->default == true ? 'required':null)]) !!}


