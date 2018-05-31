@if(isset($title))
    <?php $demoPageTitle = $title; ?>
@endif

@extends('public.default._layouts.index')

@section('content')
    @if(isset($siteEthic) && isset($title))
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="container">
                        <div class="row no-gutters">
                            <div class="col-12">
                                {!! html_entity_decode($siteEthic) !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection