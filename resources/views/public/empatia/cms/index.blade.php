@extends('public.empatia._layouts.index')


<?php $title =  $content->name; ?>
@section('headingSection')
    <h1> {{ trans("publicEmpatiaCms.defaultType") }}</h1>
@endsection


@section("content")
    @if(!empty($type))
        <div class="container">
            @if(View::exists("public." . ONE::getEntityLayout() . ".cms.".$type))
                @include("public." . ONE::getEntityLayout() . ".cms.".$type)
            @else
                <div class="row">
                    @foreach ($content->sections as $section)
                        @if(View::exists("public." . ONE::getEntityLayout() . ".cms.sections." . $section->section_type->code))
                            <div class="col-xs-12">
                                @include("public." . ONE::getEntityLayout() . ".cms.sections." . $section->section_type->code)
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>
    @endif
@endsection