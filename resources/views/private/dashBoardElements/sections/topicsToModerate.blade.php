<div class="dashboard-scrollable">
    {{--{{ dd($collection) }}--}}
    <div class='dashboard-item-wrapper'>
        <div class='row'>
            <div class='col-12 col-md-4 col-lg-6 ellipis'><b>{{ trans('private.proposition_title') }}</b></div>
            <div class='col-12 col-md-4 col-lg-3 ellipis'><b>{{ trans('private.created_by') }}</b></div>
            <div class='col-12 col-md-4 col-lg-3 text-right'></div>
        </div>
    </div>

    @foreach(!empty($collection) ? $collection :[] as $topic)
    <div class='dashboard-item-wrapper'>
        <div class="row">
            <div class='col-12 col-md-4 col-lg-6 ellipis'><div class='dashboard-text ellipis'><a href='{{ action('TopicController@show', [$arguments['padType'], $arguments['cbKey'], $topic->topic_key]) }}'>{{ $topic->title }}</a></div></div>
            <div class='col-12 col-md-4 col-lg-3 ellipis'><div class='dashboard-text ellipis'> {{ $topic->created_by != 'anonymous' ? $usersKeysNames[$topic->created_by] : trans('privateUser.anonymous')}}</div></div>
            <div class='col-12 col-md-4 col-lg-3 text-right'>
                <a class='btn-blue pull-right'
                   href="javascript:updateStatus('{{ $topic->topic_key }}','{{ $updateToStatus }}', '{{ $arguments['cbKey'] }}', '{{ $arguments['padType'] }}')"
                   data-toggle="tooltip" title="{{ trans('privatePropositionModeration.moderate') }}" data-original-title="{{ trans('privatePropositionModeration.moderate') }}">
                    <i class='fa fa-tachometer' aria-hidden='true'></i>
                    <span class="moderate-button"> {{ trans('privatePropositionModeration.moderate') }}</span>
                </a>
            </div>
        </div>
    </div>
    @endforeach

    @if($collection->count() == 0)
        @include("private.dashBoardElements.sections._emptyListMessage")
    @endif
</div>

<div class="view_full_list">
    <div class="row" >
        <div class='col-12'>
            <a href="{{ action('CbsController@showTopics', [$arguments['padType'], $arguments['cbKey']]) }}" class="btn-seemore pull-right">{{ trans('private.view_full_list') }}</a>
        </div>
    </div>
</div>