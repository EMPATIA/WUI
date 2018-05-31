@extends('private._private.index')

@section('content')

    <div class="row">
        <div class="col-md-12">
        @php
        $form = ONE::form('kiosk', trans('privateKiosks.details'))
                ->settings(["model" => isset($kiosk) ? $kiosk : null, 'id' => isset($kiosk) ? $kiosk->kiosk_key : null])
                ->show('KiosksController@edit','KiosksController@delete', ['id' => isset($kiosk) ? $kiosk->kiosk_key : null],'KiosksController@index')
                ->create('KiosksController@store', 'KiosksController@show', ['id' => isset($kiosk) ? $kiosk->kiosk_key : null])
                ->edit('KiosksController@update', 'KiosksController@show', ['id' => isset($kiosk) ? $kiosk->kiosk_key : null])
                ->open();
        @endphp
        {!! Form::oneSelect('kiosk_type_id', trans('kiosk.kiosktype'), isset($kioskTypes) ? $kioskTypes : null, isset($kiosk) ? $kiosk->kiosk_type_id : null, isset($kioskTypeName) ? $kioskTypeName : null, ['class' => 'form-control', 'id' => 'kiosk_type_id', 'required' => 'required']) !!}
        {!! Form::hidden('kiosk_key', isset($kiosk) ? $kiosk->kiosk_key : null, [ 'id' => 'kiosk_key']) !!}

        {!! Form::oneText('title', trans('kiosk.title'), isset($kiosk) ? $kiosk->title : null, ['class' => 'form-control', 'id' => 'title','required' => 'required'] ) !!}

        @if(ONE::actionType('kiosk') == 'show')
            {!! Form::oneSelect('cb_type',
                                                     isset($kiosk->entity_cb->cb_code) ? $kiosk->entity_cb->cb_code : null,
                                                     isset($value) ? $value : null,
                                                     isset($kiosk->entity_cb->cb_key) ? $kiosk->entity_cb->cb_key : null,
                                                     isset($cb) ? $cb->title: null,
                                                     ['class' => 'form-control cbTypesGroup', 'id' => 'cb_type']) !!}

        @else
            <!-- CB type -->
            {!! Form::oneSelect('cbTypeCode',
                                 trans('kiosk.type'),
                                 isset($cbTypeCodes) ? $cbTypeCodes : null,
                                 isset($kiosk->entity_cb->cb_code) ? $kiosk->entity_cb->cb_code : null,
                                 isset($kiosk->entity_cb->cb_code) ? $kiosk->entity_cb->cb_code : null,
                                 ['class' => 'form-control', 'id' => "cbTypeCode",'required' => 'required']) !!}

            <!-- Select CB type -->
                @foreach($cbsData as $cbType => $value)
                    <div id="type_{{ $cbType }}" @if(!isset($kiosk) || ($cbType != $kiosk->entity_cb->cb_code)) style="display:none;" @endif class="cbTypesDiv">
                        {!! Form::oneSelect('cb_key_'.$cbType,
                                             trans('kiosk.'.$cbType),
                                             isset($value) ? $value : null,
                                             isset($kiosk->entity_cb->cb_key) ? $kiosk->entity_cb->cb_key : null,
                                             isset($cb) ? $cb->title: null,
                                             ['class' => 'form-control cbTypesGroup', 'id' => $cbType]) !!}
                    </div>
                @endforeach

                {!! Form::hidden('cb_key', isset($kiosk->entity_cb->cb_key) ? $kiosk->entity_cb->cb_key: null, [ 'id' => 'cb_key']) !!}
            @endif

            {!! Form::oneSelect('event_key', trans('kiosk.event'), isset($votes) ? $votes : null, isset($kiosk->event_key) ? $kiosk->event_key : null, isset($vote) ? $vote: null, ['class' => 'form-control', 'id' => 'event_key'] ) !!}

            {{--@if(isset($kiosk->kiosk_type_id) && $kiosk->kiosk_type_id == 2 )--}}
            {{--@include('private.kiosk.kiosk.no_screen')--}}
            {{--@endif--}}

            {!! $form->make() !!}
        </div>
    </div>


@endsection

@section('scripts')
    <script>
        $('#cbTypeCode').on('change', function() {
            $(".cbTypesDiv").hide();
            $("#type_"+this.value).slideDown();
            $("#event_key").empty();
            var option = '<option value="" selected="selected">' + '{!! trans('form.select_value') !!}' + '</option>';
            $("#event_key").append(option);
            // $(".cbTypesGroup option:selected").removeAttr("selected");
            // $("#cbTypesGroup option:selected").removeAttr("selected");
        });
        $('.cbTypesGroup').change(function() {
            $("#cb_key").val(this.value);

            $.ajax({
                method: 'POST', // Type of response and matches what we said in the route
                url: '{{action('KiosksController@getVoteEvents')}}', // This is the url we gave in the route
                data: {cb_key: this.value, _token: "{{ csrf_token() }}"}, // a JSON object to send back
                success: function (response) { // What to do if we succeed
                    $("#event_key").empty();
                    $("#event_key").append(response);
                },
                error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                }
            });
        });

    </script>
@endsection


