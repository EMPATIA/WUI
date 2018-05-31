@extends('public.empatia._layouts.index')
@section('content')
    @if(isset($siteEthic) && isset($title))
        <!-- Site Ethics section -->
        <section>
            <div class="container padding-top-bottom-35">
                <div class="row">
                    <div class="col-sm-6 col-xs-12 col-sm-offset-3 text-center site-ethic-title">
                        <i class="fa fa-file-text-o" aria-hidden="true" style="color: #b3b3b3"></i>
                        {!!$title!!}
                    </div>
                    <br><br>

                    <div class="col-xs-12">
                        {!! html_entity_decode($siteEthic) !!}
                    </div>
                </div>
            </div>
        </section>
    @endif
@endsection

@section('scripts')

@endsection