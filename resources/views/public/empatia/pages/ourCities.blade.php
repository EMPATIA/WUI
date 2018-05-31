@extends('public.empatia._layouts.index')

@section('content')

    <section class="background-white padding-top-bottom-35">
        <br><br>
        <div class="container color-black">
            @php
                // Getting layout sections
                $layoutSections = App\Http\Controllers\PublicContentManagerController::getSections("pages-ourCities");
            @endphp

            @foreach(!empty($layoutSections) ? $layoutSections : [] as $layoutSection)
                @if($layoutSection)
                    @includeif("public." . ONE::getEntityLayout() . ".cms.sections." . $layoutSection->section_type->code, ['section' => $layoutSection])
                @endif
            @endforeach
            <div id="my-list-pilots">
                <div id="my-list-pilots-error" class="hidden">{!! Html::oneMessageInfo(trans("empatiaHome.no_pilots_to_display") )!!}</div>
            </div>
        </div>
    </section>
@endsection

@section("scripts")
    <script>
        $(document).ready(function () {
            $.ajax({
                method: 'POST', // Type of response and matches what we said in the route
                url: "{{action('PublicCbsController@getPilotsForHomePage')}}", // This is the url we gave in the route
                data: {
                    "_token": "{{ csrf_token() }}",
                    'cb_key': "{{ env("EMPATIA_HC_CB_KEY","nYPzSZ8YaLCe9OCdjvO37maqas941jCW") }}",
                    'type': "idea",
                    'filter': "commented",
                }, beforeSend: function () {
                    $("#my-list-pilots").append('<div class="col-md-12 col-xs-12 loader"><div class="text-center"><img src="{{ asset('images/preloader.gif') }}" alt="Loading"  style="width: 40px;"/></div></div>');
                    $(".loader").show();
                }, success: function (response) { // What to do if we succeed
                    console.debug(response);
                    $("#my-list-pilots").html(response);
                    $('.loader').remove();

                }, error: function () { // What to do if we succeed
                    $("#my-list-pilots").removeClass('hidden');
                    $('.loader').remove();

                }
            });
        });
    </script>
@endsection