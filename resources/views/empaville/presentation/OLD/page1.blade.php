@extends('empaville.presentation.index')

@section('content')
    <div class="content" id="box1" style="background-image:url('{{ asset('images/AI_Image.jpg') }}');background-repeat: no-repeat;background-size: 90%;background-position: center">

        <div class="col-lg-12 col-md-12 col-sm-12 text-center" style="position: absolute;bottom: 6%;">
            <h3 href="#" style="font-size: 3em">http://empatia-project.eu</h3>
        </div>
    </div>
    @include('empaville.presentation.carouselRight')

@endsection
@section('scripts')
    <script>
        $(document).ready(function() {
            resizeBox();
        });
        $( window ).resize(function() {
            resizeBox();
        });
        function resizeBox(){
            var box1 = $('#box1');
            var boxMin = box1.parent('div').css( "min-height" );
            box1.css('height',''+boxMin);
        }
    </script>

@endsection