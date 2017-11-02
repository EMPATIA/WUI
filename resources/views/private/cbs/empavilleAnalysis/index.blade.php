@extends('private._private.index')

@section('header_styles')
    <style>
        .btn-select-presentation{
            border-top-left-radius: 0!important;
            border-bottom-left-radius: 0!important;
            min-width: 120px!important;
        }
    </style>
@endsection

@section('content')

    @include('private.cbs.tabs')

    <!-- Language Select to show Empaville Presentation -->
    <form name="topic" accept-charset="UTF-8" method="POST" id="new_post_form" target="_blank" action="{{action('EmpavillePresentationController@index', ['cbKey' =>$cbKey ])}}" class="margin-bottom-20">
        <div class="row">
            <div class="col-12">
                <div class="input-group my-group">
                    <select class="form-control select2-default select-presentation" name="presentationLang" id="presentationLang" required>
                        <option value="" selected >{{ trans('privateCbs.selectLanguage') }}</option>
                        @foreach(ONE::getAllLanguages() as $language)
                            <option value="{{$language->code}}" >{{$language->name}}</option>
                        @endforeach
                    </select>
                    <button type="submit" form="new_post_form" class="btn btn-group empatia btn-select-presentation" title="{{ trans('privateCbs.presentation') }}">
                        <i class="fa fa-eye" aria-hidden="true"></i>&nbsp;<span class="text-truncate">{{ trans('privateCbs.presentation') }}</span>
                    </button>
                    </span>
                </div>
            </div>
        </div>
        <input type="hidden" name="_token" value="{{ csrf_token() }}"/>
    </form>

    <!-- Tabs -->
    <ul class="nav nav-tabs">
        <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#votacao">Votação</a></li>
        <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#resultados">Resultados</a></li>
    </ul>
    <div class="tab-content box-private">
        <div id="votacao" class="tab-pane fade show in active">
            @include('private.cbs.empavilleAnalysis.manageVoteSession')
        </div>
        <div id="resultados" class="tab-pane fade">
            @include('private.cbs.empavilleAnalysis.empavilleAnalysis')
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(function() {
            var array = ["{{ $type }}", "{{$cbKey}}"];
            getSidebar('{{ action("OneController@getSidebar") }}', 'empavilleAnalysis', array, 'padsType' );
        });
    </script>
@endsection