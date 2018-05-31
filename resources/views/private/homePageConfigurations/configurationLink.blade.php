{!! Form::oneText('link_'.(isset($child->home_page_type_key) ? $child->home_page_type_key : $homePageType->home_page_type_key),
                (isset($child) ? $child->name : $homePageType->name),
                isset($homePageConfiguration[(isset($child) ? $child->home_page_type_key : $homePageType->home_page_type_key)]) ?
                $homePageConfiguration[(isset($child) ? $child->home_page_type_key : $homePageType->home_page_type_key)]['value'] : null,
                ['class' => 'form-control', 'id' => 'link_'.(isset($child->home_page_type_key) ? $child->home_page_type_key : $homePageType->home_page_type_key),'required']) !!}