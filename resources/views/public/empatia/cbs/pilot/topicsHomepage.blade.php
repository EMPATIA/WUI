@if(count($topics)>0)
    <div class="container-fluid">
        <div class="row align-center">
            @foreach($topics as $topic)
                <div class="col-xs-6 col-sm-2 text-center @if ($loop->iteration==1) col-sm-offset-1 @endif text-center">
                    <a href="{!! action('PublicTopicController@show', ['cbKey' => $cbKey,'topicKey' => $topic->topic_key, 'type' => 'pilot'] )  !!}" class="no-decoration">
                        <div class="pilot-div">
                            <div class="circle-text">
                                @if(isset($filesByType[$topic->topic_key]->images))
                                    <span style="background-image:url('{{ action('FilesController@download', [reset($filesByType[$topic->topic_key]->images)->file_id, reset($filesByType[$topic->topic_key]->images)->file_code, 1])}}')">

                            </span>
                                @else
                                    <span style="background-image:url('{{ asset('/images/empatia/default_img_contents.jpg')}}')">

                            </span>
                                @endif

                            </div>
                            <span class="pilot-topic-name">{{$topic->title}}</span>
                        </div>
                    </a>
                </div>


            @endforeach
        </div>
    </div>
@endif