<div class="box-header with-border">
    <h3 class="box-title">{{trans('public.last_forum')}}</h3>
    <a href="#" class="pull-right" style="display: none">
        {{trans('public.go_to_forum')}} >>
    </a>
</div>
<div class="box-body">
    <div class="row">
        @foreach ($lastForum as $topic)
            <div class="col-sm-6 col-md-3">
                <div class="box box-info" style="background-color: #f5f5f5;">
                    <div class="box-header with-border">
                        <div class="user-block">
                            <!--
                            Use Image From User
                            -->
                            <img class="img-circle" alt="User Image" src="{{ $topic['img_user'] }}"/>
                            <span class="username">
                                <a href="#">{{ $topic['user'] }}</a>
                            </span>
                            <span class="description">{{trans('public.publish_at')}} - {{ $topic['created_at'] }}</span>
                        </div>
                        <div class="box-body">
                            <div class="box-header with-border">
                                <a class="link" href="#"> {{ $topic['title'] }}</a>
                            </div>
                            <div class="blockText">
                                {{ $topic['contents'] }}
                            </div>
                            <a href="#">{{trans("public.more")}}...</a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
