@extends('private._private.index')

@section('content')
    @php
    $form = ONE::form('menus', trans('privateMenus.details'), 'cm', 'menu')
            ->settings(["model" => isset($menu) ? $menu : null])
            ->show('MenusController@edit', 'MenusController@delete', ['id' => isset($menu) ? $menu->menu_key : null], 'AccessMenusController@showMenus', ['id' => isset($accessMenu) ? $accessMenu->id : null])
            ->create('MenusController@store', 'AccessMenusController@show', ['id' =>  isset($accessMenu) ? $accessMenu->id : null])
            ->edit('MenusController@update', 'MenusController@show', ['id' =>  isset($menu) ? $menu->menu_key : null])
            ->open();
    @endphp
    {{-- Access details and parent --}}
    {!! Form::oneText('access_name', array("name"=>trans('menus.access_name'),"description"=>trans('menus.access_nameDescription')), isset($accessMenu) ? $accessMenu->name : null, ['class' => 'form-control', 'id' => 'access_id', 'readonly' => 'readonly']) !!}
  {!! Form::oneCheckbox('private', trans('menus.private'), 1, isset($menu) ? ($menu->type === 'private' ? 1:0) : null, ['id' => 'private']) !!}
  {!! Form::oneSelect('parent_id', array("name"=>trans('menus.parent'),"description"=>trans('menus.parentDescription')), isset($parents) ? $parents : null, isset($menu) ? $menu->parent_id : null, isset($parent) ? $parent["title"] : null, ['class' => 'form-control', 'id' => 'parent_id']) !!}

  {{-- Choose Type: Pages, News, Events, Forums, Discussions, Proposals, Questionnaires, Polls --}}
  {!! Form::oneSelect('type_id', array("name"=>trans('menus.menutype'),"description"=>trans('menus.menutypeDescription')), isset($menuTypes) ? $menuTypes : null, isset($menu) ? $menu->type_id : null, isset($menuType) ? $menuType : null, ['class' => 'form-control', 'id' => 'menutypeid']) !!}
    {!! Form::hidden('value', isset($menu) ?  $menu->value : null, [ 'id' => 'value']) !!}
    {{-- CM --}}
    <div id="typeId2" style="@if(!empty($menu->type_id) && $menu->type_id == 2 ) display:block; @else display:none; @endif" class="types">
        {!! Form::oneSelect('page', trans('menus.page'), isset($pages) ? $pages : null, isset($menu) ? $menu->value : null, isset($page) ? $page["title"] : null, ['class' => 'form-control', 'id' => 'pages']) !!}
    </div>
    <div id="typeId3" style="@if(!empty($menu->type_id) && $menu->type_id == 3 ) display:block; @else display:none; @endif" class="types">
        {!! Form::oneSelect('news', trans('menus.news'), isset($news) ? $news : null, isset($menu) ? $menu->value : null, isset($page) ?  $page["title"] : null, ['class' => 'form-control', 'id' => 'news']) !!}
    </div>
    <div id="typeId4" style="@if(!empty($menu->type_id) && $menu->type_id == 4 ) display:block; @else display:none; @endif" class="types">
        {!! Form::oneSelect('events', trans('menus.event'), isset($events) ? $events : null, isset($menu) ? $menu->value : null, isset($page) ?  $page["title"] : null, ['class' => 'form-control', 'id' => 'events']) !!}
    </div>
    {{--     <div id="typeId7" style="@if(!empty($menu->type_id) && $menu->type_id == 7 ) display:block; @else display:none; @endif" class="types">
        {!! Form::oneSelect('proposals', trans('menus.proposals'), isset($proposals) ? $proposals : null, isset($menu) ? $menu->value : null,  isset($cb) ? $cb["title"] : null, ['class' => 'form-control', 'id' => 'proposals']) !!}
    </div> --}}
    <div id="typeId5" style="@if(!empty($menu->type_id) && $menu->type_id == 5 ) display:block; @else display:none; @endif" class="types">
        {!! Form::oneSelect('forums', trans('menus.forums'), isset($forums) ? $forums : null, isset($menu) ? $menu->value : null, isset($cb) ? $cb["title"] : null, ['class' => 'form-control', 'id' => 'forums']) !!}
    </div>
    <div id="typeId6" style="@if(!empty($menu->type_id) && $menu->type_id == 6 ) display:block; @else display:none; @endif" class="types">
        {!! Form::oneSelect('discussions', trans('menus.discussions'), isset($discussions) ? $discussions : null, isset($menu) ? $menu->value : null,  isset($cb) ? $cb["title"] : null, ['class' => 'form-control', 'id' => 'discussions']) !!}
    </div>
    <div id="typeId7" style="@if(!empty($menu->type_id) && $menu->type_id == 7 ) display:block; @else display:none; @endif" class="types">
        {!! Form::oneSelect('proposals', trans('menus.proposals'), isset($proposals) ? $proposals : null, isset($menu) ? $menu->value : null,  isset($cb) ? $cb["title"] : null, ['class' => 'form-control', 'id' => 'proposals']) !!}
    </div>
    {{-- Q --}}
    <div id="typeId8" style="@if(!empty($menu->type_id) && $menu->type_id == 8 ) display:block; @else display:none; @endif" class="types">
        {!! Form::oneSelect('questionnaires', trans('menus.questionnaires'), isset($questionnaires) ? $questionnaires : null, isset($menu) ? $menu->value : null, isset($questionnaire) ? $questionnaire['title'] : null, ['class' => 'form-control', 'id' => 'questionnaires']) !!}
    </div>
    <div id="typeId9" style="@if(!empty($menu->type_id) && $menu->type_id == 9 ) display:block; @else display:none; @endif" class="types">
        {!! Form::oneSelect('polls', trans('menus.polls'), isset($polls) ? $polls : null, isset($menu) ? $menu->value : null, isset($eventSchedule) ? $eventSchedule["title"] : null, ['class' => 'form-control', 'id' => 'polls']) !!}
    </div>
    {{-- Events --}}
    <div id="typeId10" style="@if(!empty($menu->type_id) && $menu->type_id == 10 ) display:block; @else display:none; @endif" class="types">
        <!--  -->
    </div>
    {{-- CB - Ideas --}}
    <div id="typeId11" style="@if(!empty($menu->type_id) && $menu->type_id == 11 ) display:block; @else display:none; @endif" class="types">
        {!! Form::oneSelect('ideas', trans('menus.ideas'), isset($ideas) ? $ideas : null, isset($menu) ? $menu->value : null,  isset($cb) ? $cb["title"] : null, ['class' => 'form-control', 'id' => 'ideas']) !!}
    </div>

    {{-- CB - Public Consultation --}}
    <div id="typeId12" style="@if(!empty($menu->type_id) && $menu->type_id == 12 ) display:block; @else display:none; @endif" class="types">
        {!! Form::oneSelect('publicConsultation', trans('menus.publicConsultations'), isset($publicConsultations) ? $publicConsultations : null, isset($menu) ? $menu->value : null,  isset($cb) ? $cb["title"] : null, ['class' => 'form-control', 'id' => 'publicConsultation']) !!}
    </div>

    {{-- CB - Tematic Consultation --}}
    <div id="typeId13" style="@if(!empty($menu->type_id) && $menu->type_id == 13 ) display:block; @else display:none; @endif" class="types">
        {!! Form::oneSelect('tematicConsultation', trans('menus.tematicConsultations'), isset($tematicConsultations) ? $tematicConsultations : null, isset($menu) ? $menu->value : null,  isset($cb) ? $cb["title"] : null, ['class' => 'form-control', 'id' => 'tematicConsultation']) !!}
    </div>

    {{-- CB - Survey --}}
    <div id="typeId14" style="@if(!empty($menu->type_id) && $menu->type_id == 14 ) display:block; @else display:none; @endif" class="types">
        {!! Form::oneSelect('survey', trans('menus.surveys'), isset($surveys) ? $surveys : null, isset($menu) ? $menu->value : null,  isset($cb) ? $cb["title"] : null, ['class' => 'form-control', 'id' => 'survey']) !!}
    </div>

    {{-- CB - phase1 --}}
    <div id="typeId15" style="@if(!empty($menu->type_id) && $menu->type_id == 15 ) display:block; @else display:none; @endif" class="types">
        {!! Form::oneSelect('phase1', trans('menus.phase1'), isset($phase1) ? $phase1 : null, isset($menu) ? $menu->value : null,  isset($cb) ? $cb["title"] : null, ['class' => 'form-control', 'id' => 'phase1']) !!}
    </div>

    {{-- CB - phase2 --}}
    <div id="typeId16" style="@if(!empty($menu->type_id) && $menu->type_id == 16 ) display:block; @else display:none; @endif" class="types">
        {!! Form::oneSelect('phase2', trans('menus.phase2'), isset($phase2) ? $phase2 : null, isset($menu) ? $menu->value : null,  isset($cb) ? $cb["title"] : null, ['class' => 'form-control', 'id' => 'phase2']) !!}
    </div>

    {{-- CB - phase3 --}}
    <div id="typeId17" style="@if(!empty($menu->type_id) && $menu->type_id == 17 ) display:block; @else display:none; @endif" class="types">
        {!! Form::oneSelect('phase3', trans('menus.phase3'), isset($phase3) ? $phase3 : null, isset($menu) ? $menu->value : null,  isset($cb) ? $cb["title"] : null, ['class' => 'form-control', 'id' => 'phase3']) !!}
    </div>

    {{-- New CMS - pages --}}
    <div id="typeId18" style="@if(!empty($menu->type_id) && $menu->type_id == 18 ) display:block; @else display:none; @endif" class="types">
        {!! Form::oneSelect('pagesNew', trans('menus.pages_new'), isset($pagesNew) ? $pagesNew : null, isset($menu) ? $menu->value : null,  $pageNew->name ?? null, ['class' => 'form-control', 'id' => 'pagesNew']) !!}
    </div>


    @php $i = 0; @endphp
    @foreach($languages as $language)
        @php $form->openTabs('tab-translation-' . $language->code, $language->name); @endphp
        {!! Form::oneText($language->default == true ? 'required_title_'.$language->code : 'title_'.$language->code, array("name"=>trans('menus.title_'.$language->code),"description"=>trans('menus.title_'.$language->code)), isset($menuTranslation[$language->code]) ? $menuTranslation[$language->code]->title : null, ['class' => 'form-control', 'id' => 'title_'.$language->code]) !!}

         <div id="typeId1" style="@if(!empty($menu->type_id) && $menu->type_id == 1  ) display:block; @else display:none; @endif" class="types typeId1">
             {!! Form::oneText('link_'.$language->code, array("name"=>trans('menus.link_'.$language->code),"description"=>trans('menus.link_'.$language->code)), isset($menuTranslation[$language->code]) ? $menuTranslation[$language->code]->link : null, ['class' => 'form-control', 'id' => 'link']) !!}
         </div>
      @endforeach

    @php $form->makeTabs(); @endphp

    {{-- Hidden values --}}
    {!! Form::hidden('access_id', isset($accessMenu) ?  $accessMenu->id : null) !!}
    {!! Form::hidden('position', isset($menu) ? $menu->position : null) !!}

    {!! $form->make() !!}
@endsection

@section('scripts')
    <script>
        /* ------------ Menu Type ------------ */
        $('#menutypeid').on('change', function() {
            $(".types").hide();
            $("#typeId"+this.value).slideDown();
            $(".typeId"+this.value).slideDown();
            $("#typeId"+this.value+" select").select2();
        });
        /* ------------ CM ------------ */
        $( "#news" ).change(function() {
            $("#value").val(this.value);
        });
        $( "#pages" ).change(function() {
            $("#value").val(this.value);
        });
        $( "#events" ).change(function() {
            $("#value").val(this.value);
        });
        /* ------------ CB ------------ */
        $( "#forums" ).change(function() {
            $("#value").val(this.value);
        });
        $( "#discussions" ).change(function() {
            $("#value").val(this.value);
        });
        $( "#proposals" ).change(function() {
            $("#value").val(this.value);
        });
        $( "#ideas" ).change(function() {
            $("#value").val(this.value);
        });
        $( "#publicConsultation" ).change(function() {
            $("#value").val(this.value);
        });
        $( "#tematicConsultation" ).change(function() {
            $("#value").val(this.value);
        });

        $( "#survey" ).change(function() {
            $("#value").val(this.value);
        });

        $( "#phase1" ).change(function() {
            $("#value").val(this.value);
        });
        $( "#phase2" ).change(function() {
            $("#value").val(this.value);
        });
        $( "#phase3" ).change(function() {
            $("#value").val(this.value);
        });
        $( "#pagesNew" ).change(function() {
            $("#value").val(this.value);
        });
        /* ------------ Q ------------ */
        $( "#questionnaires" ).change(function() {
            $("#value").val(this.value);
        });
        $( "#polls" ).change(function() {
            $("#value").val(this.value);
        });
        $( "#conferenceEvents" ).change(function() {
            $("#value").val(this.value);
        });

        $("#parent_id").select2();
        $("#menutypeid").select2();

    </script>
@endsection
