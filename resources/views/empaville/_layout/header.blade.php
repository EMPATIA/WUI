<header class="main-header">
    <nav class="navbar navbar-static-top" role="navigation" style=" background: linear-gradient(to right, #9cc34e , #d8dd41); box-shadow: rgba(0,0,0,.5) 0 1px 5px;">
        <div class="navbar-header" style="padding-left: 10px;">
            <a href="/">
                <span class="logo-lg"><img src="{{ asset('images/logo_white.png') }}" style="width: 120px"/></span>
            </a>
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                    data-target="#navbar-collapse">
                <i class="fa fa-bars"></i>
            </button>
        </div>
        <div id="navbar-collapse" class="navbar-collapse pull-right collapse" aria-expanded="false" >
            <ul class="nav navbar-nav">
                <li><a href="{{ action('EmpavilleDashboardController@totals',['cbKey' => $cbKey, 'lang' => $lang]) }}" style="color: black!important;padding-top: 17px;"><i class="fa fa-plus-square"></i> {{trans('empaville.total')}}</a></li>
                <li><a href="{{ action('EmpavilleDashboardController@byGender',['cbKey' => $cbKey, 'lang' => $lang]) }}" style="color: black!important;padding-top: 17px;"><i class="fa fa-venus-mars"></i> {{trans('empaville.byGender')}}</a></li>
                <li><a href="{{ action('EmpavilleDashboardController@proposalsByProfession',['cbKey' => $cbKey, 'lang' => $lang]) }}" style="color: black!important;padding-top: 17px;"><i class="fa fa-suitcase"></i> {{trans('empaville.byProfession')}}</a></li>
                <li><a href="{{ action('EmpavilleDashboardController@byAge',['cbKey' => $cbKey, 'lang' => $lang]) }}" style="color: black!important;padding-top: 17px;"><i class="fa fa-hourglass-half"></i> {{trans('empaville.byAge')}}</a></li>
                <li><a href="{{ action('EmpavilleDashboardController@byChannel',['cbKey' => $cbKey, 'lang' => $lang]) }}" style="color: black!important;padding-top: 17px;"><i class="fa fa-television"></i> {{trans('empaville.byChannel')}}</a></li>
                <li><a href="{{ action('EmpavilleDashboardController@byGeoArea',['cbKey' => $cbKey, 'lang' => $lang]) }}" style="color: black!important;padding-top: 17px;"><i class="fa fa-users"></i> {{trans('empaville.byNeighbourhood')}}</a></li>
            </ul>
        </div>
    </nav>
</header>