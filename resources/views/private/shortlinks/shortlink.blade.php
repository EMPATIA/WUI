@extends('private._private.index')

@section('content')

    <div class="row">
        <div class="col-md-12">
            @php $form = ONE::form('shortLinks', trans('privateShortLinks.details'), 'wui', 'shortLinks')
                ->settings(["model" => isset($shortLink) ? $shortLink : null,'id'=>isset($shortLink) ? $shortLink->id : null])
                ->show('ShortLinksController@edit', 'ShortLinksController@delete', ['shortLinkKey' => isset($shortLink) ? $shortLink->short_link_key : null], 'ShortLinksController@index', ['shortLinkKey' => isset($shortLink) ? $shortLink->short_link_key : null])
                ->create('ShortLinksController@store', 'ShortLinksController@index', ['shortLinkKey' => isset($shortLink) ? $shortLink->short_link_key : null])
                ->edit('ShortLinksController@update', 'ShortLinksController@show', ['shortLinkKey' => isset($shortLink) ? $shortLink->short_link_key : null])
                ->open();
            @endphp

            {!! Form::oneText('name', trans('privateShortLinks.private_name'), isset($shortLink) ? $shortLink->name : null, ['class' => 'form-control', 'id' => 'name', 'required']) !!}
            {!! Form::oneText('code', trans('privateShortLinks.short_code'), isset($shortLink) ? $shortLink->code : null, ['class' => 'form-control', 'id' => 'code', 'required']) !!}

            @if(ONE::actionType("shortLinks")!="show")
                {!! Form::oneText('url', trans('privateShortLinks.url'), isset($shortLink) ? $shortLink->url : null, ['class' => 'form-control', 'id' => 'url', 'required']) !!}
            @else
                <dt>{{ trans("privateShortUrls.url") }}</dt>
                <dd>
                    <a target="_blank" href="{{ $shortLink->url }}">
                        {{ $shortLink->url }}
                    </a>
                </dd>
                <hr>
                <dt>{{ trans("privateShortUrls.short_link") }}</dt>
                <dd>
                    <a target="_blank" href="{{ action("ShortLinksController@resolveShortLink",$shortLink->code) }}">
                        {{ action("ShortLinksController@resolveShortLink",$shortLink->code) }}
                    </a>
                </dd>
                <hr>
                <dt>{{ trans("privateShortUrls.times_visited") }}</dt>
                <dd>{{ $shortLink->hits ?? 0 }}</dd>
            @endif

            {!! $form->make() !!}
        </div>
    </div>

@endsection
