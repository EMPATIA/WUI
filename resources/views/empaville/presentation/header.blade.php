
<header class="main-header" id="header" style="padding-left: 0%">
    <nav class="navbar-default" style="padding-left: 0%">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar" style="padding: 0%">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
        </div>
        <div class="collapse navbar-collapse" id="myNavbar" style="padding: 0%">
            <ul class="nav nav-tabs nav-justified colapse" style="background: linear-gradient(to right, #9cc34e , #d8dd41); box-shadow: rgba(0,0,0,.5) 0 1px 5px; text-align: right;padding: 0">
                <li class="{!! isset($id)? (($id == 1)?'active':''): ''!!}"><a style="padding: 0%;color: black" href="{{ action('EmpavillePresentationController@next',['cbKey'=> $cbKey, 'id' =>1, 'lang' => $lang]) }}">{{trans('empavillePresentation.round')}}</a></li>
                <li class="{!! isset($id)? (($id == 2)?'active':''): ''!!}"><a style="padding: 0%;color: black" href="{{ action('EmpavillePresentationController@next',['cbKey'=> $cbKey, 'id' =>2, 'lang' => $lang]) }}">{{trans('empavillePresentation.vRules')}}</a></li>
                <li class="{!! isset($id)? (($id == 3)?'active':''): ''!!}">
                    <a class="dropdown-toggle" data-toggle="dropdown" style="padding: 0%;color: black">{{trans('empavillePresentation.proposals')}}</a>
                    <ul class="dropdown-menu" style="">
                        <li class="{!! isset($count)? (($count == 1)?'active':''): ''!!}"><a style="padding: 0%;color: black" href="{{ action('EmpavillePresentationController@showProposal',['cbKey'=> $cbKey, 'id'=>'3','count'=>1, 'lang' => $lang]) }}">{{trans('empavillePresentation.mod1')}}</a></li>
                        <li class="{!! isset($count)? (($count == 2)?'active':''): ''!!}"><a style="padding: 0%;color: black" href="{{ action('EmpavillePresentationController@showProposal',['cbKey'=> $cbKey, 'id'=>'3','count'=>2, 'lang' => $lang]) }}">{{trans('empavillePresentation.mod2')}}</a></li>
                        <li class="{!! isset($count)? (($count == 3)?'active':''): ''!!}"><a style="padding: 0%;color: black" href="{{ action('EmpavillePresentationController@showProposal',['cbKey'=> $cbKey, 'id'=>'3','count'=>3, 'lang' => $lang]) }}">{{trans('empavillePresentation.mod3')}}</a></li>
                        <li class="{!! isset($count)? (($count == 4)?'active':''): ''!!}"><a style="padding: 0%;color: black" href="{{ action('EmpavillePresentationController@showProposal',['cbKey'=> $cbKey, 'id'=>'3','count'=>4, 'lang' => $lang]) }}">{{trans('empavillePresentation.mod4')}}</a></li>
                    </ul>
                </li>
                <li class="{!! isset($id)? (($id == 4)?'active':''): ''!!}"><a style="padding: 0%;color: black" href="{{ action('EmpavillePresentationController@next',['cbKey'=> $cbKey, 'id' =>4, 'lang' => $lang]) }}">{{trans('empavillePresentation.voting')}}</a></li>
                <li class="{!! isset($id)? (($id == 5)?'active':''): ''!!}"><a style="padding: 0%;color: black" href="{{ action('EmpavillePresentationController@next',['cbKey'=> $cbKey, 'id' =>5, 'lang' => $lang]) }}">{{trans('empavillePresentation.mData')}}</a></li>
                {{--<li class="{!! isset($id)? (($id == 5)?'active':''): ''!!}"><a style="padding: 0%;color: black" href="{{ action('EmpavillePresentationController@next',['cbKey'=> $cbKey, 'id' =>5, 'lang' => $lang]) }}">Winners</a></li>--}}

                {{--<li class="{!! isset($id)? (($id == 6)?'active':''): ''!!}"><a style="padding: 0%;color: black" href="{{ action('EmpavillePresentationController@next',['cbKey'=> $cbKey, 'id' =>6]) }}">Map</a></li>--}}
                {{--<li class="{!! isset($id)? (($id == 7)?'active':''): ''!!}"><a style="padding: 0%;color: black" href="{{ action('EmpavillePresentationController@next',['cbKey'=> $cbKey, 'id' =>7]) }}">POM</a></li>--}}
                {{--<li class="{!! isset($id)? (($id == 8)?'active':''): ''!!}"><a style="padding: 0%;color: black" href="{{ action('EmpavillePresentationController@next',['cbKey'=> $cbKey, 'id' =>8]) }}">Round</a></li>--}}
                {{--<li class="{!! isset($id)? (($id == 9)?'active':''): ''!!}"><a style="padding: 0%;color: black" href="{{ action('EmpavillePresentationController@next',['cbKey'=> $cbKey, 'id' =>9]) }}">V.Rules</a></li>--}}
                {{--<li class="{!! isset($id)? (($id == 10)?'active':''): ''!!}">--}}
                {{--<a class="dropdown-toggle" data-toggle="dropdown" style="padding: 0%;color: black">Proposals</a>--}}
                {{--<ul class="dropdown-menu" style="">--}}
                {{--<li class="{!! isset($count)? (($count == 1)?'active':''): ''!!}"><a style="padding: 0%;color: black" href="{{ action('EmpavillePresentationController@showProposal',['cbKey'=> $cbKey, 'id'=>'10','count'=>1]) }}">Mod1</a></li>--}}
                {{--<li class="{!! isset($count)? (($count == 2)?'active':''): ''!!}"><a style="padding: 0%;color: black" href="{{ action('EmpavillePresentationController@showProposal',['cbKey'=> $cbKey, 'id'=>'10','count'=>2]) }}">Mod2</a></li>--}}
                {{--<li class="{!! isset($count)? (($count == 3)?'active':''): ''!!}"><a style="padding: 0%;color: black" href="{{ action('EmpavillePresentationController@showProposal',['cbKey'=> $cbKey, 'id'=>'10','count'=>3]) }}">Mod3</a></li>--}}
                {{--<li class="{!! isset($count)? (($count == 4)?'active':''): ''!!}"><a style="padding: 0%;color: black" href="{{ action('EmpavillePresentationController@showProposal',['cbKey'=> $cbKey, 'id'=>'10','count'=>4]) }}">Mod4</a></li>--}}
                {{--</ul>--}}
                {{--</li>--}}
                {{--<li class="{!! isset($id)? (($id == 11)?'active':''): ''!!}"><a style="padding: 0%;color: black" href="{{ action('EmpavillePresentationController@next',['cbKey'=> $cbKey, 'id' =>11]) }}">Voting</a></li>--}}
                {{--<li class="{!! isset($id)? (($id == 12)?'active':''): ''!!}"><a style="padding: 0%;color: black" href="{{ action('EmpavillePresentationController@next',['cbKey'=> $cbKey, 'id' =>12]) }}">M.Data</a></li>--}}
                {{--<li class="{!! isset($id)? (($id == 13)?'active':''): ''!!}"><a style="padding: 0%;color: black" href="{{ action('EmpavillePresentationController@next',['cbKey'=> $cbKey, 'id' =>13]) }}">Winners</a></li>--}}
                {{--<li class="{!! isset($id)? (($id == 14)?'active':''): ''!!}"><a style="padding: 0%;color: black" href="{{ action('EmpavillePresentationController@next',['cbKey'=> $cbKey, 'id' =>14]) }}">Data</a></li>--}}
                {{--<li class="{!! isset($id)? (($id == 15)?'active':''): ''!!}"><a style="padding: 0%;color: black" href="{{ action('EmpavillePresentationController@next',['cbKey'=> $cbKey, 'id' =>15]) }}">Quest</a></li>--}}
                {{--<li class="{!! isset($id)? (($id == 16)?'active':''): ''!!}"><a style="padding: 0%;color: black" href="{{ action('EmpavillePresentationController@next',['cbKey'=> $cbKey, 'id' =>16]) }}">End</a></li>--}}
            </ul>
        </div>
    </nav>
</header>
