{{-- Choose Type: Pages, News, Events, Forums, Discussions, Proposals, Questionnaires, Polls --}}
{!! Form::oneSelect('menuTypeId_'.(isset($child->home_page_type_key) ? $child->home_page_type_key : $homePageType->home_page_type_key),
    (isset($child) ? $child->name : $homePageType->name).' '.trans('menus.menutype'),
    isset($menuTypes) ? $menuTypes : null, null, null,
    ['class' => 'form-control','onchange' =>'changeType(this)', 'id' => 'menuTypeId_'.(isset($child->home_page_type_key) ? $child->home_page_type_key : $homePageType->home_page_type_key)]) !!}

{!! Form::hidden('value_'.(isset($child->home_page_type_key) ? $child->home_page_type_key : $homePageType->home_page_type_key),
    null,
    [ 'id' => 'value_'.(isset($child->home_page_type_key) ? $child->home_page_type_key : $homePageType->home_page_type_key)]) !!}

{{--Update--}}
{!! Form::hidden('valueUpdate_'.(isset($child->home_page_type_key) ? $child->home_page_type_key : $homePageType->home_page_type_key),
    isset($homePageConfiguration[(isset($child) ? $child->home_page_type_key : $homePageType->home_page_type_key)]) ?
    $homePageConfiguration[(isset($child) ? $child->home_page_type_key : $homePageType->home_page_type_key)]['value'] : null,
    [ 'id' => 'valueUpdate_'.(isset($child->home_page_type_key) ? $child->home_page_type_key : $homePageType->home_page_type_key)]) !!}

{{-- CM --}}
<div id="typeId2_{{isset($child->home_page_type_key) ? $child->home_page_type_key : $homePageType->home_page_type_key}}" style="@if(!empty($menu->type_id) && $menu->type_id == 2 ) display:block; @else display:none; @endif" class="types">
    {!! Form::oneSelect('page_'.(isset($child->home_page_type_key) ? $child->home_page_type_key : $homePageType->home_page_type_key),
     (isset($child) ? $child->name : $homePageType->name).' '.trans('menus.page'),
     isset($pages) ? $pages : null, isset($menu) ? $menu->value : null, isset($page) ? $page["title"] : null,
     ['class' => 'form-control','onchange' =>'changeTypeValue(this)', 'id' => 'pages_'.(isset($child->home_page_type_key) ? $child->home_page_type_key : $homePageType->home_page_type_key)]) !!}
</div>
<div id="typeId3_{{isset($child->home_page_type_key) ? $child->home_page_type_key : $homePageType->home_page_type_key}}" style="@if(!empty($menu->type_id) && $menu->type_id == 3 ) display:block; @else display:none; @endif" class="types">
    {!! Form::oneSelect('news_'.(isset($child->home_page_type_key) ? $child->home_page_type_key : $homePageType->home_page_type_key),
     (isset($child) ? $child->name : $homePageType->name).' '.trans('menus.news'),
     isset($news) ? $news : null, isset($menu) ? $menu->value : null, isset($page) ?  $page["title"] : null,
     ['class' => 'form-control','onchange' =>'changeTypeValue(this)', 'id' => 'news_'.(isset($child->home_page_type_key) ? $child->home_page_type_key : $homePageType->home_page_type_key)]) !!}
</div>
<div id="typeId4_{{isset($child->home_page_type_key) ? $child->home_page_type_key : $homePageType->home_page_type_key}}" style="@if(!empty($menu->type_id) && $menu->type_id == 4 ) display:block; @else display:none; @endif" class="types">
    {!! Form::oneSelect('events_'.(isset($child->home_page_type_key) ? $child->home_page_type_key : $homePageType->home_page_type_key),
     (isset($child) ? $child->name : $homePageType->name).' '.trans('menus.event'),
     isset($events) ? $events : null, isset($menu) ? $menu->value : null, isset($page) ?  $page["title"] : null,
     ['class' => 'form-control','onchange' =>'changeTypeValue(this)', 'id' => 'events_'.(isset($child->home_page_type_key) ? $child->home_page_type_key : $homePageType->home_page_type_key)]) !!}
</div>
<div id="typeId5_{{isset($child->home_page_type_key) ? $child->home_page_type_key : $homePageType->home_page_type_key}}" style="@if(!empty($menu->type_id) && $menu->type_id == 5 ) display:block; @else display:none; @endif" class="types">
    {!! Form::oneSelect('forums_'.(isset($child->home_page_type_key) ? $child->home_page_type_key : $homePageType->home_page_type_key),
     (isset($child) ? $child->name : $homePageType->name).' '.trans('menus.forums'),
     isset($forums) ? $forums : null, isset($menu) ? $menu->value : null, isset($cb) ? $cb["title"] : null,
     ['class' => 'form-control','onchange' =>'changeTypeValue(this)', 'id' => 'forums_'.(isset($child->home_page_type_key) ? $child->home_page_type_key : $homePageType->home_page_type_key)]) !!}
</div>
<div id="typeId6_{{isset($child->home_page_type_key) ? $child->home_page_type_key : $homePageType->home_page_type_key}}" style="@if(!empty($menu->type_id) && $menu->type_id == 6 ) display:block; @else display:none; @endif" class="types">
    {!! Form::oneSelect('discussions_'.(isset($child->home_page_type_key) ? $child->home_page_type_key : $homePageType->home_page_type_key),
     (isset($child) ? $child->name : $homePageType->name).' '.trans('menus.discussions'),
     isset($discussions) ? $discussions : null, isset($menu) ? $menu->value : null,  isset($cb) ? $cb["title"] : null,
     ['class' => 'form-control','onchange' =>'changeTypeValue(this)', 'id' => 'discussions_'.(isset($child->home_page_type_key) ? $child->home_page_type_key : $homePageType->home_page_type_key)]) !!}
</div>
<div id="typeId7_{{isset($child->home_page_type_key) ? $child->home_page_type_key : $homePageType->home_page_type_key}}" style="@if(!empty($menu->type_id) && $menu->type_id == 7 ) display:block; @else display:none; @endif" class="types">
    {!! Form::oneSelect('proposals_'.(isset($child->home_page_type_key) ? $child->home_page_type_key : $homePageType->home_page_type_key),
     (isset($child) ? $child->name : $homePageType->name).' '.trans('menus.proposals'),
     isset($proposals) ? $proposals : null, isset($menu) ? $menu->value : null,  isset($cb) ? $cb["title"] : null,
     ['class' => 'form-control','onchange' =>'changeTypeValue(this)', 'id' => 'proposals_'.(isset($child->home_page_type_key) ? $child->home_page_type_key : $homePageType->home_page_type_key)]) !!}
</div>
{{-- Q --}}
<div id="typeId8_{{isset($child->home_page_type_key) ? $child->home_page_type_key : $homePageType->home_page_type_key}}" style="@if(!empty($menu->type_id) && $menu->type_id == 8 ) display:block; @else display:none; @endif" class="types">
    {!! Form::oneSelect('questionnaires_'.(isset($child->home_page_type_key) ? $child->home_page_type_key : $homePageType->home_page_type_key),
     (isset($child) ? $child->name : $homePageType->name).' '.trans('menus.questionnaires'),
     isset($questionnaires) ? $questionnaires : null, isset($menu) ? $menu->value : null, isset($questionnaire) ? $questionnaire['title'] : null,
     ['class' => 'form-control','onchange' =>'changeTypeValue(this)', 'id' => 'questionnaires_'.(isset($child->home_page_type_key) ? $child->home_page_type_key : $homePageType->home_page_type_key)]) !!}
</div>
<div id="typeId9_{{isset($child->home_page_type_key) ? $child->home_page_type_key : $homePageType->home_page_type_key}}" style="@if(!empty($menu->type_id) && $menu->type_id == 9 ) display:block; @else display:none; @endif" class="types">
    {!! Form::oneSelect('polls_'.(isset($child->home_page_type_key) ? $child->home_page_type_key : $homePageType->home_page_type_key),
    (isset($child) ? $child->name : $homePageType->name).' '.trans('menus.polls'),
    isset($polls) ? $polls : null, isset($menu) ? $menu->value : null, isset($eventSchedule) ? $eventSchedule["title"] : null,
    ['class' => 'form-control','onchange' =>'changeTypeValue(this)', 'id' => 'polls_'.(isset($child->home_page_type_key) ? $child->home_page_type_key : $homePageType->home_page_type_key)]) !!}
</div>
{{-- Events --}}
<div id="typeId10_{{isset($child->home_page_type_key) ? $child->home_page_type_key : $homePageType->home_page_type_key}}" style="@if(!empty($menu->type_id) && $menu->type_id == 10 ) display:block; @else display:none; @endif" class="types">
    <!--  -->
</div>
{{-- CB - Ideas --}}
<div id="typeId11_{{isset($child->home_page_type_key) ? $child->home_page_type_key : $homePageType->home_page_type_key}}" style="@if(!empty($menu->type_id) && $menu->type_id == 11 ) display:block; @else display:none; @endif" class="types">
    {!! Form::oneSelect('ideas_'.(isset($child->home_page_type_key) ? $child->home_page_type_key : $homePageType->home_page_type_key),
    (isset($child) ? $child->name : $homePageType->name).' '.trans('menus.ideas'),
    isset($ideas) ? $ideas : null, isset($menu) ? $menu->value : null,  isset($cb) ? $cb["title"] : null,
    ['class' => 'form-control','onchange' =>'changeTypeValue(this)', 'id' => 'ideas_'.(isset($child->home_page_type_key) ? $child->home_page_type_key : $homePageType->home_page_type_key)]) !!}
</div>

{{-- CB - Public Consultation --}}
<div id="typeId12_{{isset($child->home_page_type_key) ? $child->home_page_type_key : $homePageType->home_page_type_key}}" style="@if(!empty($menu->type_id) && $menu->type_id == 12 ) display:block; @else display:none; @endif" class="types">
    {!! Form::oneSelect('publicConsultation_'.(isset($child->home_page_type_key) ? $child->home_page_type_key : $homePageType->home_page_type_key),
    (isset($child) ? $child->name : $homePageType->name).' '.trans('menus.publicConsultations'),
    isset($publicConsultations) ? $publicConsultations : null, isset($menu) ? $menu->value : null,  isset($cb) ? $cb["title"] : null,
    ['class' => 'form-control','onchange' =>'changeTypeValue(this)', 'id' => 'publicConsultation_'.(isset($child->home_page_type_key) ? $child->home_page_type_key : $homePageType->home_page_type_key)]) !!}
</div>

{{-- CB - Tematic Consultation --}}
<div id="typeId13_{{isset($child->home_page_type_key) ? $child->home_page_type_key : $homePageType->home_page_type_key}}" style="@if(!empty($menu->type_id) && $menu->type_id == 13 ) display:block; @else display:none; @endif" class="types">
    {!! Form::oneSelect('tematicConsultation_'.(isset($child->home_page_type_key) ? $child->home_page_type_key : $homePageType->home_page_type_key),
    (isset($child) ? $child->name : $homePageType->name).' '.trans('menus.tematicConsultations'),
    isset($tematicConsultations) ? $tematicConsultations : null, isset($menu) ? $menu->value : null,  isset($cb) ? $cb["title"] : null,
    ['class' => 'form-control','onchange' =>'changeTypeValue(this)', 'id' => 'tematicConsultation_'.(isset($child->home_page_type_key) ? $child->home_page_type_key : $homePageType->home_page_type_key)]) !!}
</div>