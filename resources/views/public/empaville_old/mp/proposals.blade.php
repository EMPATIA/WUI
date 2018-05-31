@extends('public.empaville._layouts.index')

@section('content')


    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="glyphicon glyphicon-question-sign"></i> {{ trans('mp.proposals') }}</h3>
                </div>
                <div class="box-body">

                    <!-- Advanced Search -->
                    <div class="col-sm-12 col-md-6">
                        <form role="search">
                            <input type="text" class="form-control " placeholder="{{ trans('form.search') }}">
                        </form>
                    </div>
                    <div class="col-sm-12 col-md-3">
                        <select class="form-control">
                            <option>Select Area</option>
                            <option>Environment</option>
                            <option>Health</option>
                            <option>Sports</option>
                            <option>Food</option>
                            <option>Pets</option>
                        </select>
                    </div>
                    <div class="col-sm-12 col-md-3">
                        <select class="form-control">
                            <option>Select Geographic Area</option>
                            <option>Coimbra</option>
                            <option>Milano</option>
                            <option>New York</option>
                            <option>Paris</option>
                            <option>London</option>
                        </select>
                    </div>
                    
                    <div class="row BP-tabBox">
                        <ul class="nav nav-tabs">
                            <li class="btn-tab active"><a href="#recent" data-toggle="tab">+ recent</a></li>
                            <li class="btn-tab"><a href="#near" data-toggle="tab">near you</a></li>
                            <li class="btn-tab"><a href="#commented" data-toggle="tab">+ commented</a></li>
                        </ul>
                        <!-- TAB visualization -->
                        <div class="tab-content">
                            <div class="tab-pane active" id="recent">
                                <!-- TAB content -->
                                <div class="row BP-thumbContent">
                                    <div class="col-md-12 BP-thumbContent">
                                        @foreach ($lastProposals as $topic)
                                        <div class="col-sm-6 col-md-3">
                                            <div class="box box-secundary">
                                                <div class="box-body">
                                                    <div class="BP-thumbIcon"><img src="http://t-l.it/default/prova/image/ico-ambiente.png" alt="Ambiente" class="img-responsive"></div>
                                                    <div class="BP-breadCrumbs">
                                                        <div class="BP-property">
                                                            {{ $topic['title'] }}<span>»</span>
                                                        </div>
                                                        <div class="BP-category">
                                                            {{ $topic['area'] }}
                                                        </div>
                                                    </div>
                                                    <div class="BP-postHeading">
                                                        <a href="#">{{ $topic['created_by'] }} - {{ $topic['created_at'] }}</a>
                                                    </div>
                                                    <div class="BP-postDescription">
                                                        {{ $topic['contents'] }}
                                                    </div>
                                                    <div class="BP-postAction">
                                                        <a href="fase0-viewIdea.php" class="link activelink"><i class="fa fa-users"></i> {{ $topic['supporters'] }}</a>
                                                        <a href="fase0-viewIdea.php" class="link activelink"><i class="fa fa-check"></i> {{ trans('form.following') }} </a>
                                                        <a href="fase0-viewIdea.php" class="link"><i class="fa fa-search"></i> {{ trans('form.see') }}</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                <hr>
                            </div>
                            <div class="tab-content">
                                <div class="tab-pane active" id="followed">
                                    <!-- TAB content -->
                                    <div class="row BP-thumbContent">
                                        <div class="col-md-12 BP-thumbContent">
                                            @foreach ($nearYouProposals as $topic)
                                            <div class="col-sm-6 col-md-3">
                                                <div class="box box-secundary">
                                                    <div class="box-body">
                                                        <div class="BP-thumbIcon"><img src="http://t-l.it/default/prova/image/ico-ambiente.png" alt="Ambiente" class="img-responsive"></div>
                                                        <div class="BP-breadCrumbs">
                                                            <div class="BP-property">
                                                                {{ $topic['title'] }}<span>»</span>
                                                            </div>
                                                            <div class="BP-category">
                                                                {{ $topic['area'] }}
                                                            </div>
                                                        </div>
                                                        <div class="BP-postHeading">
                                                            <a href="#">{{ $topic['created_by'] }} - {{ $topic['created_at'] }}</a>
                                                        </div>
                                                        <div class="BP-postDescription">
                                                            {{ $topic['contents'] }}
                                                        </div>
                                                        <div class="BP-postAction">
                                                            <a href="fase0-viewIdea.php" class="link activelink"><i class="fa fa-users"></i> {{ $topic['supporters'] }}</a>
                                                            <a href="fase0-viewIdea.php" class="link activelink"><i class="fa fa-check"></i> {{ trans('form.following') }} </a>
                                                            <a href="fase0-viewIdea.php" class="link"><i class="fa fa-search"></i> {{ trans('form.see') }}</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <hr>
                                </div>
                            </div>
                            <div class="tab-content">
                                <div class="tab-pane active" id="commented">
                                    <!-- TAB content -->
                                    <div class="row BP-thumbContent">
                                        <div class="col-md-12 BP-thumbContent">
                                            @foreach ($commentedProposals as $topic)
                                            <div class="col-sm-6 col-md-3">
                                                <div class="box box-secundary">
                                                    <div class="box-body">
                                                        <div class="BP-thumbIcon"><img src="http://t-l.it/default/prova/image/ico-ambiente.png" alt="Ambiente" class="img-responsive"></div>
                                                        <div class="BP-breadCrumbs">
                                                            <div class="BP-property">
                                                                {{ $topic['title'] }}<span>»</span>
                                                            </div>
                                                            <div class="BP-category">
                                                                {{ $topic['area'] }}
                                                            </div>
                                                        </div>
                                                        <div class="BP-postHeading">
                                                            <a href="#">{{ $topic['created_by'] }} - {{ $topic['created_at'] }}</a>
                                                        </div>
                                                        <div class="BP-postDescription">
                                                            {{ $topic['contents'] }}
                                                        </div>
                                                        <div class="BP-postAction">
                                                            <a href="fase0-viewIdea.php" class="link activelink"><i class="fa fa-users"></i> {{ $topic['supporters'] }}</a>
                                                            <a href="fase0-viewIdea.php" class="link activelink"><i class="fa fa-check"></i> {{ trans('form.following') }} </a>
                                                            <a href="fase0-viewIdea.php" class="link"><i class="fa fa-search"></i> {{ trans('form.see') }}</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <hr>
                                </div>
                            </div>
                        </div>
                    </div>
                    

                    <!-- Proposals -->
                    @foreach($lastProposals as $proposals)
                    <div class="media">
                        <div class="media-left">
                            <a href="#">
                                <img src="https://www.oneclickroot.com/wp-content/uploads/2014/08/maps.jpg" style="float: right;margin: 0 0 10px 10px;width:20px;"/>
                            </a>
                        </div>
                        <div class="media-body">
                            <div class="row">
                                <div class="col-md-10">
                                    <h4 class="media-heading">{{ $proposals['title'] }}</h4>
                                    {{ $proposals['contents'] }}
                                    <br/><br/>
                                    <br/><br/>
                                    <a href=''>Details »</a>
                                </div>
                                <div class="col-md-2 text-center">
                                    <img title="jQuery Knob" src="http://i.stack.imgur.com/C7Vva.png" style="height:100px;">
                                    <br/>
                                    {{ $proposals['supporters'] }} {{ trans('form.support') }} <br/> {{ $proposals['supporters_necessary'] }} {{ trans('form.necessary_support') }}
                                    <br/>
                                    <br/>
                                    <button class="btn btn-flat btn-primary btn-xs">
                                        {{--&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;--}}
                                        {{ trans('form.support') }}
                                        {{--&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;--}}
                                    </button>
                                    <br/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr/>
                    @endforeach

                    
                    <div style="clear:both;">&nbsp;</div>
                    <div class="row">
                        <div class="col-sm-5">
                            <div aria-live="polite" role="status" id="users_list_info" class="dataTables_info">{{ trans('showing') }} 1 {{ trans('to') }} 4 {{ trans('of') }}
                                4 {{ trans('entries') }}
                            </div>
                        </div>
                        <div class="col-sm-7">
                            <div id="users_list_paginate" class="dataTables_paginate paging_simple_numbers">
                                <ul class="pagination">
                                    <li id="users_list_previous" class="paginate_button previous disabled"><a tabindex="0" data-dt-idx="0" aria-controls="users_list" href="#">{{ trans('mp.previous') }}</a>
                                    </li>
                                    <li class="paginate_button active"><a tabindex="0" data-dt-idx="1" aria-controls="users_list" href="#">1</a></li>
                                    <li id="users_list_next" class="paginate_button next disabled"><a tabindex="0" data-dt-idx="2" aria-controls="users_list" href="#">{{ trans('mp.next') }}</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="glyphicon glyphicon-question-sign"></i> {{ trans('mp.proposals_create') }}</h3>
                </div>
                <div class="box-body text-center">
                    <h6>Here you can create your own proposal<br>Lorem ipsum...</h6>
                    <button class="btn btn-flat btn-primary center-block" onclick="location.href='{{action("MPController@createProposal")}}'">{{ trans('mp.proposals_create') }}</button>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="glyphicon glyphicon-question-sign"></i> {{ trans('mp.proposals_tags') }}</h3>
                </div>
                <div class="box-body">
                    <div id="tag-cloud" class="tag-cloud">
                        <h5 class="text-center">{{ trans('mp.trending') }}</h5>
                        <br>
                        <a class="l" href="/proposals?tag=movilidad"> mobility <span class="label round info">1580</span> </a>
                        <a class="l" href="/proposals?tag=medio+ambiente"> environment <span class="label round info">1468</span> </a>
                        <a class="m" href="/proposals?tag=sostenibilidad"> sustainability <span class="label round info">889</span> </a>
                        <a class="m" href="/proposals?tag=salud"> health <span class="label round info">752</span> </a>
                        <a class="m" href="/proposals?tag=econom%C3%ADa"> economy <span class="label round info">652</span> </a>
                        <a class="m" href="/proposals?tag=derechos+sociales"> social rights <span class="label round info">645</span> </a>
                        <a class="m" href="/proposals?tag=participaci%C3%B3n"> participation <span class="label round info">544</span></a>
                        <a class="m" href="/proposals?tag=cultura"> culture <span class="label round info">506</span></a>
                        <a class="m" href="/proposals?tag=empleo"> employment <span class="label round info">398</span> </a>
                        <a class="m" href="/proposals?tag=equidad"> equity <span class="label round info">376</span> </a>
                        <a class="m" href="/proposals?tag=seguridad+y+emergencias"> security and emergency <span class="label round info">347</span></a>
                        <a class="s" href="/proposals?tag=deportes"> sports <span class="label round info">318</span> </a>
                        <a class="m" href="/proposals?tag=transparencia"> transparency <span class="label round info">284</span> </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection