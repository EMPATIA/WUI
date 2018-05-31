<style>
    @if( empty($filesByType) && count($filesByType) == 0 )

        .idea-image{
        background-color: white!important;
        background-position: center!important;
        background-size: contain;
        background-repeat: no-repeat;
    }
    @endif

</style>

@forelse ($topics as $i => $topic)
    <?php
    $active_status = collect($topic->status)->where('active', '=', 1)->first();
    ?>

    <div class="col-12 col-sm-6 col-md-4 idea-card primary-color color-text-primary">
        <a href="{!! action('PublicTopicController@show', [$cb->cb_key , $topic->topic_key, 'type' => $type] ) !!}" class="a-wrapper">

            @if(ONE::checkCBsOption($configurations, 'SHOW-STATUS'))
                <div class="status-idea green">
                    {{--  {{ONE::getStatusTranslation($translations, $active_status->status_type->code)}}  --}}
                    {{ONE::transCb('cb_moderated', !empty($cb) ? $cb->cb_key : $cbKey)}}
                </div>
            @endif

            <div class="card-img" style="background-image:url(@if(!empty($filesByType) && count($filesByType) >0 && isset($filesByType[$topic->topic_key]) && !empty(reset($filesByType[$topic->topic_key])) ) '{{ action('FilesController@download', [$filesByType[$topic->topic_key]->file_id, $filesByType[$topic->topic_key]->file_code, 'inline' => 1, 'h' => 50, 'extension' => 'jpeg', 'quality' => 55])}}' @else {{ONE::getSiteConfiguration("file_logo_first","/images/demo/LogoEmpatia-l-02.png")}}) @endif;background-position:center;">
            </div>
            <div class="title" style="color:black;">
                {{$topic->title ?? ''}}
            </div>
            <div class="description">
                {!! strip_tags($topic->contents ?? '') ?? '' !!}
            </div>
            <div class="idea-details">
                <hr>

                    {{--  <div class="detail">
                        <div class="row">
                            <div class="col-6">
                                <i class="fa fa-user" aria-hidden="true"></i>   --}}
                    {{--{{ ONE::getStatusTranslation($translations, 'user') }}--}}
                    {{--  {{ONE::transCb('proposal_demo.created_by')}}
                </div>
                <div class="col-6">
                    {{isset($usersNames->{$topic->created_by}->name) ? $usersNames->{$topic->created_by}->name : ONE::transCb('proposal_demo.anonymous') }}
                </div>
            </div>
        </div>  --}}
                    @if(ONE::checkCBsOption($configurations, 'ALLOW-COMMENTS'))
                        <div class="detail">
                            <div class="row">
                                <div class="col-6">
                                    <i class="fa fa-comments" aria-hidden="true"></i>{{--{{ONE::getStatusTranslation($translations, 'comments')}}--}}{{ONE::transCb('cb_comments', !empty($cb) ? $cb->cb_key : $cbKey)}}
                                </div>
                                <div class="col-6">
                                    {{$topic->_count_comments ?? 0}}
                                </div>

                            </div>
                        </div>
                    @endif
                    @if(!empty($topic->parameters))
                        <div style="margin-top: 5px">
                            @foreach($topic->parameters as $parameter)
                                @if($parameter->visible_in_list)
                                    @if(!empty($parameter->pivot->value))
                                        <div class="detail" >
                                            <div class="row">
                                                <div class="col-12 col-sm-6">
                                                    {{$parameter->parameter}}
                                                </div>
                                                <div class="col-12 col-sm-6 topic-parameters" style="width: 200px">
                                                    @if ($parameter->code == 'numeric')
                                                        <i class="fa fa-eur" aria-hidden="true" style="margin-right: 5px; "></i>
                                                        {{ number_format($parameter->pivot->value, 0, ',', '.') }}
                                                    @else
                                                        <?php $options = explode(",",$parameter->pivot->value); ?>
                                                        @foreach($parameter->options ?? [] as $option)
                                                            @if(isset($option) and !empty($option))

                                                                @if(in_array($option->id, $options))
                                                                    {{$option->label ?? ''}}
                                                                @endif
                                                            @endif
                                                        @endforeach
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="vote-container">
                    {{--<div class="row">--}}
                    {{--<div class="col-6 button-dislike">--}}
                    {{--<a href="#">--}}
                    {{--<i class="fa fa-thumbs-down" aria-hidden="true"></i>--}}
                    {{--<span>Dislike</span>--}}
                    {{--</a>--}}
                    {{--</div>--}}
                    {{--<div class="col-6 button-like">--}}
                    {{--<a href="#">--}}
                    {{--<i class="fa fa-thumbs-up" aria-hidden="true"></i>--}}
                    {{--<span>Like</span>--}}
                    {{--</a>--}}
                    {{--</div>--}}
                    {{--</div>--}}
                </div>
            </div>
        </a>
    </div>

@empty
    @if (is_null($originalPageToken))
        <div class="col-12" style="margin-top: 10px">
            {!!  Html::oneMessageInfo(ONE::getStatusTranslation($translations, 'no_proposals_to_display')) !!}
        </div>
    @endif
@endforelse

@if(!isset($noLoop) && !empty($pageToken))
    {{--<div class="row">--}}
    <div class="col-12">
        <a class='jscroll-next'
           href='{{ URL::action('PublicCbsController@show',collect(['cbKey' => $cbKey])->merge(($filterList ?? ['type' => $type]))->merge(['page' => $pageToken, 'layout' => 'demo','topic_status' => 'moderated'])->toArray())}}'>{{ ONE::transCb("cb_next", !empty($cb) ? $cb->cb_key : $cbKey) }}</a>
    </div>{{--
    </div>--}}
@endif











{{--<div class="col-12 col-sm-6 col-md-4 idea-card primary-color color-text-primary">--}}
{{--<a href="#" class="a-wrapper">--}}
{{--<div class="status-idea yellow">--}}
{{--Still in evaluation--}}
{{--</div>--}}
{{--<div class="card-img" style="background-image:url('images/image-2.jpg')">--}}
{{--</div>--}}
{{--<div class="title">--}}
{{--Cras a facilisis sapien, proin accumsan efficitur rutrum--}}
{{--</div>--}}
{{--<div class="description">--}}
{{--Integer ultrices aliquam bibendum. Nulla non arcu eu tortor congue vestibulum sit amet ut diam. Sed hendrerit dui at neque blandit, in venenatis lectus pulvinar--}}
{{--</div>--}}
{{--<div class="idea-details">--}}
{{--<hr>--}}
{{--<div class="detail">--}}
{{--<i class="fa fa-user" aria-hidden="true"></i> Jonh Doe--}}
{{--</div>--}}
{{--<div class="detail">--}}
{{--<i class="fa fa-comments" aria-hidden="true"></i> 225 comments--}}
{{--</div>--}}
{{--</div>--}}
{{--</a>--}}
{{--<div class="vote-container">--}}
{{--<div class="row">--}}
{{--<div class="col-6 button-dislike">--}}
{{--<a href="#">--}}
{{--<i class="fa fa-thumbs-down" aria-hidden="true"></i>--}}
{{--<span>Dislike</span>--}}
{{--</a>--}}
{{--</div>--}}
{{--<div class="col-6 button-like">--}}
{{--<a href="#">--}}
{{--<i class="fa fa-thumbs-up" aria-hidden="true"></i>--}}
{{--<span>Like</span>--}}
{{--</a>--}}
{{--</div>--}}
{{--</div>--}}
{{--</div>--}}
{{--</div>--}}
{{--<div class="col-12 col-sm-6 col-md-4 idea-card primary-color color-text-primary">--}}
{{--<a href="#" class="a-wrapper">--}}
{{--<div class="status-idea medGrey">--}}
{{--Nothing to say--}}
{{--</div>--}}
{{--<div class="card-img" style="background-image:url('images/image-3.jpg')">--}}
{{--</div>--}}
{{--<div class="title">--}}
{{--Orci varius natoque penatibus et magnis dis parturient monte--}}
{{--</div>--}}
{{--<div class="description">--}}
{{--Interdum et malesuada fames ac ante ipsum primis in faucibus. Praesent ultricies laoreet ex, a pharetra nibh cursus ut.--}}
{{--</div>--}}
{{--<div class="idea-details">--}}
{{--<hr>--}}
{{--<div class="detail">--}}
{{--<i class="fa fa-user" aria-hidden="true"></i> Jonh Doe--}}
{{--</div>--}}
{{--<div class="detail">--}}
{{--<i class="fa fa-comments" aria-hidden="true"></i> 225 comments--}}
{{--</div>--}}
{{--</div>--}}
{{--</a>--}}
{{--<div class="vote-container">--}}
{{--<div class="row">--}}
{{--<div class="col-6 button-dislike">--}}
{{--<a href="#">--}}
{{--<i class="fa fa-thumbs-down" aria-hidden="true"></i>--}}
{{--<span>Dislike</span>--}}
{{--</a>--}}
{{--</div>--}}
{{--<div class="col-6 button-like">--}}
{{--<a href="#">--}}
{{--<i class="fa fa-thumbs-up" aria-hidden="true"></i>--}}
{{--<span>Like</span>--}}
{{--</a>--}}
{{--</div>--}}
{{--</div>--}}
{{--</div>--}}
{{--</div>--}}
{{--<div class="col-12 col-sm-6 col-md-4 idea-card primary-color color-text-primary">--}}
{{--<a href="#" class="a-wrapper">--}}
{{--<div class="status-idea medGrey">--}}
{{--Don't know--}}
{{--</div>--}}
{{--<div class="card-img" style="background-image:url('images/image-1.jpg')">--}}
{{--</div>--}}
{{--<div class="title">--}}
{{--Aenean porttitor mauris sed nisi ornare consectetur--}}
{{--</div>--}}
{{--<div class="description">--}}
{{--Curabitur aliquet ex lorem, at volutpat dui aliquet vel. Suspendisse posuere venenatis eros, nec sollicitudin turpis viverra ut. Nulla facilisi.--}}
{{--</div>--}}
{{--<div class="idea-details">--}}
{{--<hr>--}}
{{--<div class="detail">--}}
{{--<i class="fa fa-user" aria-hidden="true"></i> Jonh Doe--}}
{{--</div>--}}
{{--<div class="detail">--}}
{{--<i class="fa fa-comments" aria-hidden="true"></i> 225 comments--}}
{{--</div>--}}
{{--</div>--}}
{{--</a>--}}
{{--<div class="vote-container">--}}
{{--<div class="row">--}}
{{--<div class="col-12 vote-button no-padding">--}}
{{--<a href="#">--}}
{{--Vote--}}
{{--</a>--}}
{{--</div>--}}
{{--</div>--}}
{{--</div>--}}
{{--</div>--}}
{{--<div class="col-12 col-sm-6 col-md-4 idea-card primary-color color-text-primary">--}}
{{--<a href="#" class="a-wrapper">--}}
{{--<div class="status-idea green">--}}
{{--Passed the detail check--}}
{{--</div>--}}
{{--<div class="card-img" style="background-image:url('images/image-2.jpg')">--}}
{{--</div>--}}
{{--<div class="title">--}}
{{--Cras a facilisis sapien, proin accumsan efficitur rutrum--}}
{{--</div>--}}
{{--<div class="description">--}}
{{--Integer ultrices aliquam bibendum. Nulla non arcu eu tortor congue vestibulum sit amet ut diam. Sed hendrerit dui at neque blandit, in venenatis lectus pulvinar--}}
{{--</div>--}}
{{--<div class="idea-details">--}}
{{--<hr>--}}
{{--<div class="detail">--}}
{{--<i class="fa fa-user" aria-hidden="true"></i> Jonh Doe--}}
{{--</div>--}}
{{--<div class="detail">--}}
{{--<i class="fa fa-comments" aria-hidden="true"></i> 225 comments--}}
{{--</div>--}}
{{--</div>--}}
{{--</a>--}}
{{--<div class="vote-container">--}}
{{--<div class="row">--}}
{{--<div class="col-12 vote-button no-padding">--}}
{{--<a href="#">--}}
{{--Vote--}}
{{--</a>--}}
{{--</div>--}}
{{--</div>--}}
{{--</div>--}}
{{--</div>--}}
{{--<div class="col-12 col-sm-6 col-md-4 idea-card primary-color color-text-primary">--}}
{{--<a href="#" class="a-wrapper">--}}
{{--<div class="status-idea yellow">--}}
{{--Still looking--}}
{{--</div>--}}
{{--<div class="card-img" style="background-image:url('images/image-3.jpg')">--}}
{{--</div>--}}
{{--<div class="title">--}}
{{--Orci varius natoque penatibus et magnis dis parturient monte--}}
{{--</div>--}}
{{--<div class="description">--}}
{{--Interdum et malesuada fames ac ante ipsum primis in faucibus. Praesent ultricies laoreet ex, a pharetra nibh cursus ut.--}}
{{--</div>--}}
{{--<div class="idea-details">--}}
{{--<hr>--}}
{{--<div class="detail">--}}
{{--<i class="fa fa-user" aria-hidden="true"></i> Jonh Doe--}}
{{--</div>--}}
{{--<div class="detail">--}}
{{--<i class="fa fa-comments" aria-hidden="true"></i> 225 comments--}}
{{--</div>--}}
{{--</div>--}}
{{--</a>--}}
{{--<div class="vote-container">--}}
{{--<div class="row">--}}
{{--<div class="col-12 vote-button no-padding">--}}
{{--<a href="#">--}}
{{--Vote--}}
{{--</a>--}}
{{--</div>--}}
{{--</div>--}}
{{--</div>--}}
{{--</div>--}}