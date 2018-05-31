<div class="box-header with-border">
    <h3 class="box-title">{{trans('public.last_proposals')}}</h3>
    <a href="#" class="pull-right" style="display: none">
        {{trans('public.go_to_proposals')}} >>
    </a>
</div>
<div class="box-body">
    <div class="row">
        @foreach ($lastProposals as $proposal)
            <div class="col-sm-6 col-md-3">
                <div class="box box-info" style="background-color: #f5f5f5;">
                    <div class="box-header with-border">
                        <div class="user-block">
                            <img class="img-thumbnail"  src="https://www.oneclickroot.com/wp-content/uploads/2014/08/maps.jpg"/>
                            <span class="username">
                                <a href="#">{{$proposal['title']}}</a>
                            </span>
                            <span class="description">{{$proposal['created_at']}}</span>
                        </div>
                        <div class="box-body">
                            <div class="blockText">
                                {{$proposal['contents']}}
                            </div>
                            <a href="#">{{trans("public.more")}}...</a>
                        </div>
                        <div class="box-footer">
                            <!--<div style="display:inline;width:90px;height:90px;">
                                <canvas width="90" height="90"></canvas>
                                <input class="knob" type="text" data-readonly="true" data-fgcolor="#3c8dbc" data-height="90" data-width="90" value="30" readonly="readonly" style="width: 49px; height: 30px; position: absolute; vertical-align: middle; margin-top: 30px; margin-left: -69px; border: 0px none; background: transparent none repeat scroll 0% 0%; font: bold 18px Arial; text-align: center; color: rgb(60, 141, 188); padding: 0px;">
                            </div> -->
                            <img style="height:90px;" src="http://i.stack.imgur.com/C7Vva.png" title="jQuery Knob">
                            <div>
                                <span>
                                    {{$proposal['supporters']}} {{trans("public.supporters")}}
                                </span>
                            </div>
                            <div>
                                <span>
                                    {{$proposal['supporters_necessary']}} {{trans("public.necessary_supporters")}}
                                </span>
                            </div>
                            <button class="btn btn-flat btn-primary btn-">Support</button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
