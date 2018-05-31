@extends('public.empaville_new._layouts.homeIndex')

@section('content')
    @include('public.empaville_new._layouts.registrationSection')
    @include('public.empaville_new.home.lastNews')
@endsection

@section('scripts')
    <script>
        $.each([$(".news-content-box")], function (index, value) {
            $(document).ready(function () {
                value.dotdotdot({
                    ellipsis: '... ',
                    wrap: 'word',
                    aft: null,
                    watch: 'window'
                });
            });
        });
    </script>
@endsection
