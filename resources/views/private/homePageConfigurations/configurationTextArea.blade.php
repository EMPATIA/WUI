@php /*
@if(isset($languages))
    @foreach($languages as $language)
        @php $form->openTabs('tab-textArea-translation-' . $language->code.'-'.(isset($child) ? $child->home_page_type_key : $homePageType->home_page_type_key), $language->name);@endphp
        <div style="padding: 10px;">
            {!! Form::oneTextArea(
            $language->code .'_'.(isset($child) ? $child->home_page_type_key : $homePageType->home_page_type_key),
            (isset($child) ? $child->name : $homePageType->name),
            isset($homePageConfiguration[(isset($child) ? $child->home_page_type_key : $homePageType->home_page_type_key)]['translations'][$language->code]) ?
            $homePageConfiguration[(isset($child) ? $child->home_page_type_key : $homePageType->home_page_type_key)]['translations'][$language->code]['value'] : null,
            ['class' => 'form-control','rows' => '2','id' => 'value_'.$language->code .'_'.(isset($child) ? $child->home_page_type_key : $homePageType->home_page_type_key),($language->default == true ? 'required':null)]) !!}
        </div>
    @endforeach
    @php $form->makeTabs(); @endphp
@endif
 */ @endphp


{!! Form::oneTextArea(
$language->code .'_'.(isset($child) ? $child->home_page_type_key : $homePageType->home_page_type_key),
(isset($child) ? $child->name : $homePageType->name),
isset($homePageConfiguration[(isset($child) ? $child->home_page_type_key : $homePageType->home_page_type_key)]['translations'][$language->code]) ?
$homePageConfiguration[(isset($child) ? $child->home_page_type_key : $homePageType->home_page_type_key)]['translations'][$language->code]['value'] : null,
['class' => 'form-control','rows' => '2','id' => 'value_'.$language->code .'_'.(isset($child) ? $child->home_page_type_key : $homePageType->home_page_type_key),($language->default == true ? 'required':null)]) !!}
