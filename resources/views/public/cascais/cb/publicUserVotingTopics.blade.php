@forelse(!empty($topics) ? $topics : [] as $i => $topic)
    <div class="col-6 idea topic-description">
        <a class="topic-key a-wrapper" data-topic-title="{{ $topic->title }}" data-topic-key="{{ $topic->topic_key }}">
                <div class="img" style="background-image:url('@if(!empty($filesByType) && count($filesByType) >0 && isset($filesByType[$topic->topic_key]) && !empty(reset($filesByType[$topic->topic_key])) ){{ action('FilesController@download', [$filesByType[$topic->topic_key]->file_id, $filesByType[$topic->topic_key]->file_code, 'inline' => 1, 'h' => 150, 'extension' => 'jpeg', 'quality' => 65])}} @else {{ONE::getSiteConfiguration("file_logo_first","/images/demo/LogoEmpatia-l-02.png")}}@endif');background-position:center;"></div>
                <div class="idea-title">
                    <p>{{ $topic->title }}</p>
                </div>
            </a>
    </div>

@empty
    @if (is_null($originalPageToken))
        <div class="col-12" style="margin-top: 10px">
            {!!  Html::oneMessageInfo(ONE::getStatusTranslation($translations, 'no_cbs_to_display')) !!}
        </div>
    @endif
@endforelse

@if(!isset($noLoop) && !empty($pageToken))
    {{--<div class="row">--}}
    <div class="col-12">
        <a class='jscroll-next'
           href='{{ URL::action('PublicCbsController@show',collect(['cbKey' => $cbKey])->merge(($filterList ?? ['type' => $type]))->merge(['page' => $pageToken, 'layout' => 'demo','topic_status' => 'moderated'])->toArray())}}'>{{ ONE::transCb('cb_next', !empty($cb) ? $cb->cb_key : $cbKey) }}</a>
    </div>{{--
    </div>--}}
@endif