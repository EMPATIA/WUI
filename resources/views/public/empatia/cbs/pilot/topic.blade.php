@extends('public.empatia._layouts.index')

@section('content')
    <div class="container-fluid">
        <div class="row my-row-topic-pilot">
            @foreach($topicData->configurations as $item)
                @if( $item == 'topic_options_allow_pictures' && isset($filesByType->images) )
                    @foreach($filesByType->images as $fileTmp)
                        <?php $src = action('FilesController@download', [$fileTmp->file_id, $fileTmp->file_code] ); ?>
                    @endforeach
                @endif
            @endforeach
            <div class="my-jumboHeader " style="background-image:url('{{ $src }}'); background-position: left bottom">

            </div>
        </div>
    </div>
    <section>
        <div class="container-fluid" style="padding:50px;">
            <div class="row">
                <div class="col-xs-12">

                    <h3 class="pilot-topic-content">
                        @if(!empty($topic->summary))
                            {!! nl2br($topic->summary) !!}
                        @else
                            {!! nl2br($topic->contents) !!}
                        @endif
                    </h3>
                    <br>
                    <p class="pilot-description">
                        @if($topicMessage != null)
                            {!! nl2br($topicMessage->contents) !!}
                        @endif
                    </p>
                </div>
            </div>
        </div>
        <div >

        </div>
        @include('public.empatia.home.toolsRow')
    </section>


@endsection

