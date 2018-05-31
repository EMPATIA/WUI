
@extends('private.presentation.index')

@section('content')
    <div class="welcome-container">
        <div class="row box-buffer-rap btn-grid">
            <div class="col-lg-3 col-md-4 col-sm-6 col-12 col-lg-offset-1">
                <a href="{{action('PresentationController@show',['page' => 'pbTemplate'])}}" class="text-center">
                    <div class="btn-presentation">
                        <i class="fa fa-sitemap fa-2x" aria-hidden="true"></i>
                        <div class="text-padding">
                            {{trans("privatePresentation.rap_model")}}
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 col-12">
                <a href="#" class="text-center">
                    <div class="btn-presentation">
                        <img  src="{{asset('images/default/apresentacaoBackOffice_icone-02.png')}}"/>
                        <div class="text-padding">
                            {{trans("privatePresentation.public_consultations")}}
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 col-12">
                <a href="#" class="text-center">
                    <div class="btn-presentation">
                        <img src="{{asset('images/default/apresentacaoBackOffice_icone-01.png')}}"/>
                        <div class="text-padding">
                            {{trans("privatePresentation.small_ideas")}}
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-lg-3 col-md-4 col-sm-6 col-12 col-lg-offset-1">
                <a href="#" class="text-center">
                    <div class="btn-presentation">
                        <i class="fa fa-check-square-o fa-2x" aria-hidden="true"></i>
                        <div class="text-padding">
                            {{trans("privatePresentation.survey")}}
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 col-12">
                <a href="#" class="text-center">
                    <div class="btn-presentation">
                        <img src="{{asset('images/default/apresentacaoBackOffice_icone-03.png')}}"/>
                        <div class="text-padding">
                            {{trans("privatePresentation.online_deliberation")}}
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 col-12">
                <a href="#" class="text-center">
                    <div class="btn-presentation">
                        <i class="fa fa-pencil-square-o fa-2x" aria-hidden="true"></i>
                        <div class="text-padding">
                            {{trans("privatePresentation.custom_model")}}
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-lg-3 col-md-4 col-sm-6 col-12 col-lg-offset-1">
                <a href="#" class="text-center" style="color: #bc0000">
                    <div class="btn-presentation red-presentation">
                        <i class="fa fa-drupal fa-2x" aria-hidden="true" style="color: #bc0000"></i>
                        <div class="text-padding" style="color: #bc0000">
                            CMS<br><span style="font-size: 10px">(Drupal, Joomla, Wordpress, etc.)</span>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 col-12">
                <a href="#" class="text-center" style="color: #bc0000">
                    <div class="btn-presentation red-presentation">
                        <i class="fa fa-database fa-2x" aria-hidden="true" style="color: #bc0000"></i>
                        <div class="text-padding" style="color: #bc0000">
                            Open Data
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 col-12">
                <a href="#" class="text-center" style="color: #bc0000">
                    <div class="btn-presentation red-presentation">
                        <i class="fa fa-wrench fa-2x" aria-hidden="true" style="color: #bc0000"></i>
                        <div class="text-padding" style="color: #bc0000">
                            {{trans("privatePresentation.other_third_party_tool")}}
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>

    </script>
@endsection