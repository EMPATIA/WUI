@extends('private._pdf.topic')

@section('content')
    @foreach($allTopics as $topic)
        <div class="container pagesContent">
            <div class="row">
                <div class="col-12 proposalTopic-title" >

                    <table style="width:100%;">
                        <tr>
                            <td>
                                <h3>{{ htmlentities($topic->title) }}</h3></td>
                            <td style="text-align:right;">
                                <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAG8AAABjCAMAAACi2a4pAAAACXBIWXMAABcSAAAXEgFnn9JSAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAADNQTFRF////AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA8YBMDAAAABB0Uk5TABAgMEBQYHCAkKCwwNDg8FTgqMgAAAMcSURBVGje7ZrHmq0gDIAFAQFDef+nvZsp35GSAmfuLCZr5dc0QsJx/Mn/FON8hE/x3pzvQ2kXU20FvHkH7OqxPuV2aivNQUWkxG2aVT5XioDZQyuVKhuINleOgF7zEqhc8StuUipfktRx1F1FUpwId6YqlSjBlSqXxA5/V5eEC1zEcYFjXI7fe4KxHrYAzWiNqw1nE/IqsO8qJYxyh+3/JVDjrvfBxc8+18BCWPTejZhyTC9aSYHvO05iZO8VQvY+O7s3zfJna4eEv9Xq5SIn3MTfLbzMCB8SuRpVhYhTEQDgPjHgzfxAN3+wKGyBqatpsjJhtFziRH0kR+yQ11jE0H9vkgKHvCb5At05z0PAa1YZu2ihB8+EdzziPgxrzUcWU0LeQ6OF6C3uEPKeGd+S1JkPMc+QvNxy8tiU9/jBgULD60Nqgecofp44m/Oc9zBNd4dRNCPTeBFP2obmxDSexV3Pc/YRjPdQlkJVcK3xHs5gUB82i7yAxtZr1jsWeReaiVnugvIMmrJ5tTjGO9Hl9vLQ5fQP8wzygPYvkld5iMLV4Dgv5iEPjI6gv4Wneby8ysPjr0zzC5fn0PwC0y2Zy/No9r+nGXbgn4l2KDfoF8W2ge1buRTJPbsltmVUg8wOTiEcVtba34GQHTOt6idJJhxE4j6FWooTO3mfYO7rhVRTgRynaT2Du7JKJvKx3NG6nkmKe7SohieRZw6RWhCoTYdYOUekg1QKzuyi6waX0YVuFpC26iYNH8dpXJtV3yQey79MyE2jntdebBpoTKDjNs3DEvCqXIO0+3ix8nYrYZex4hGiahv7WUk+kjYltaVWiXt3ut61oIGob/FstTvOyVMr9ofY1PRk+5OxYSTp/sicPrByozsEHXsoNxjzcgIpDGfB92W+P1tbPxzy8hJFnA8vAQBgehOAmwjD2jiVnXfXJrhZUC1b+UA8iQoD8cBfWporkREZ+b3d7zMbdy/dLFKe6Sj2WBQdGar0xwahEucjcxYx4LGR9l4Js9PLNzm84Z6dDd2ALHC971KfuQJ8lSkZbu/eeIHwT3D5B/X97iUtdhljAAAAAElFTkSuQmCC" style="max-height: 10px; margin-top: 2px"/>
                                {{date('Y-m-d', strtotime($topic->created_at))}}
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            @php
            $files = isset($filesByType[$topic->topic_key]) ? $filesByType[$topic->topic_key] : null;
            @endphp

            <span class="param-text-area-title">Beschreibung der Idee</span>
            <div class="row" style="margin-bottom: 10px;">
                <div class="col-sm-12 col-12 col-md-12 proposalTopicSummary" style="margin-bottom: 10px;">
                    {!! nl2br($topic->contents) !!}
                </div>
            </div>

            @php $topic->parameters = collect($topic->parameters)->sortBy('position'); @endphp

            @foreach($topic->parameters as $parameter)

                @if(!empty($parameter->visible) && ($parameter->visible == 1) && (isset($topic->topicVersionId) ? (isset($parameter->pivot->topic_version_id) ? $parameter->pivot->topic_version_id == $topic->topicVersionId : true) : true))

                    @if($parameter->code == 'image_map')
                        <span class="param-text-area-title">
                            {{ trans("cbs.location") }}
                        </span>
                    @elseif($parameter->code == 'google_maps')
                        {{-- Nothing Happens --}}
                    @else
                        @if(!empty($parameter->parameter) && $parameter->code != 'topic_checkpoints_boolean' && $parameter->code != 'topic_checkpoint_phase' && $parameter->code != 'topic_checkpoints')
                            <span class="param-text-area-title">
                                {{$parameter->parameter}}
                            </span>
                            @if(empty($parameter->options) && $parameter->code != 'check_box')
                                <span class="parameter-text">
                                    @if($parameter->code=="text_area")
                                        {!! $parameter->pivot->value !!}
                                    @else
                                        {{ $parameter->pivot->value }}
                                    @endif
                                </span>
                            @endif
                        @endif
                    @endif
                    @php
                        $dropDownOptions = [];
                        if(!empty($parameter->options)){
                            foreach ($parameter->options as $temp) {
                                $dropDownOptions[$temp->id] = $temp->label;
                            }
                        }
                    @endphp
                    <span class="parameter-text">
                        @if($parameter->code == "dropdown" || $parameter->code == "category" || $parameter->code == "budget" || $parameter->code == 'check_box' )
                            @if(explode(",",$parameter->pivot->value)>0)
                                @foreach(explode(",",$parameter->pivot->value) as $value)
                                    @if(!empty($dropDownOptions) && isset($dropDownOptions[$value]) && $value != 0)
                                        {{$dropDownOptions[$value]}}<br>
                                    @endif
                                @endforeach
                            @elseif($parameter->code != 'topic_checkpoint_phase')
                                {{$dropDownOptions[$parameter->pivot->value]}}
                            @endif
                        @elseif(preg_match('/http:\/\/(www\.)*youtube\.com\/.*/', $parameter->pivot->value ) )
                            {{$parameter->pivot->value}}
                        @elseif(preg_match('/http:\/\/(www\.)*vimeo\.com\/.*/',$parameter->pivot->value) )
                            {{$parameter->pivot->value}}
                        @elseif($parameter->code == 'google_maps')
                        @endif
                    </span>
                @endif

            @endforeach

            @if(isset($topic->voteData))
                @foreach($topic->voteData as $voteData)
                    <span class="param-text-area-title"> {{ $voteData->name }}</span>
                    <div class="row" style="margin-bottom: 10px;">
                        {{ trans('privateCbs.votes').': '.$voteData->votes }}
                    </div>
                @endforeach
            @endif
        </div>
        <div style="page-break-before:always">&nbsp;</div>
    @endforeach
@endsection