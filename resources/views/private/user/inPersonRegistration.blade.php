@extends('private._private.index')
<style>
    .margin-0{
        margin:0!important;
    }
    .translations-table thead tr:first-child td:first-child{
        padding: 8px 0px!important;
        font-size: 14px!important;
    }
    .translations-table thead td{
        font-size: 14px!important;
    }
    .fields td{
        position: relative;
    }
    .fields td .fa{
        position: absolute;
        right: -15px;
        top: 15px;
    }
    .fields td img{
        position: absolute;
        right: -15px;
        top: 15px;
    }
    .color-blue{
        color:#3c8dbc;
    }
    .color-green{
        color: green;
    }
    .my-new-user-btn{
        height: 22px;
        padding-top: 4px!important;
    }
    .new-user-line{
        width: 100%;
        float: left;
        margin-bottom: 5px;
    }
    .hidden{
        display: none;
    }
</style>
@section('content')
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title"><i class="fa fa-user-plus" aria-hidden="true"></i> {{ trans('privateUsers.registerUsers') }}</h3>
        </div>

        <div class="box-body">
            <div class="row margin-0">
                <table class="table translations-table">
                    <thead>
                    <tr>
                        <td class="text-center">{{ trans('privateUsers.code') }}</td>
                        <td class="text-center">{{ trans('privateUsers.name') }}</td>
                        <td class="text-center">{{ trans('privateUsers.surname') }}</td>
                        <td class="text-center">{{ trans('privateUsers.email') }}</td>
                        @foreach($parameters as $parameter)
                            <td class="text-center">{{ $parameter->name }}</td>
                        @endforeach
                    </tr>
                    </thead>
                    <tbody>
                    @include('private.user.inPersonRegistrationLine')
                    </tbody>
                </table>

                <div id="users-list" class="hidden">
                    <div class="row margin-0">
                        <div class="col-6"><b>{{ trans('privateUsers.name') }}</b></div>
                        <div class="col-4"><b>{{ trans('privateUsers.status') }}</b></div>
                        <div class="col-2"><b>{{ trans('privateUsers.operations') }}</b></div>
                    </div>
                    <div class="info-users"></div>
                </div>
            </div>

        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).on('keydown','.fields td:last', function(e){
            var element = $(this);
            if (e.keyCode == 13) {
                errors = false;
                element.parent().find("input[required]").each(function( key, value ) {
                    currentInput = $(this);
                    if (currentInput.val()=="") {
                        currentInput.css({"border": "1px solid #f00"});
                        errors = true;
                    } else
                        currentInput.css({"border":""});
                });
                if (errors==false) {
                    var inputs = element.parent().find(':input').serializeArray();
                    $.ajax({
                        method: 'POST', // Type of response and matches what we said in the route
                        url: "{{action('UsersController@saveInPersonRegistration')}}", // This is the url we gave in the route
                        data: {
                            "_token": "{{ csrf_token() }}",
                            "inputs": inputs,
                            "vote_event_key": "{{ $voteKey }}"
                        }, beforeSend: function () {
                            element.parent('.fields').find("input").prop("disabled", true);
                            element.parent('.fields').find("select").prop("disabled", true);
                            element.append('<img src="{{ asset('images/bluePreLoader.gif') }}" alt="Loading" class="loader pull-right" style="width: 20px; padding-top:2px;"/>');
                            $('.translations-table').find('tbody').append(
                                '<tr class="fields">' +
                                '<td><input type="text" name="code" class="form-control"></td>' +
                                '<td><input type="text" name="name" class="form-control" required></td>' +
                                '<td><input type="text" name="surname" class="form-control"></td>' +
                                '<td><input type="text" name="email" class="form-control"></td>' +
                                    @foreach($parameters as $parameter)
                                            @if($parameter->parameter_type->code == 'dropdown')
                                        '<td><select class="form-control" name="parameter_{{$parameter->parameter_user_type_key}}" @if (isset($parameter->mandatory) && $parameter->mandatory) required @endif>' +
                                '<option value="" selected>{{trans("user.select_option")}}</option>'+
                                    @foreach($parameter->parameter_user_options as $option)
                                        '<option value="{{$option->parameter_user_option_key}}">{{$option->name}}</option>' +
                                    @endforeach
                                        '</select></td>' +
                                    @else
                                        '<td><input type="text" name="parameter_{{$parameter->parameter_user_type_key}}" class="form-control" @if (isset($parameter->mandatory) && $parameter->mandatory) required @endif></td>' +
                                    @endif
                                            @endforeach
                                        '</tr>');
                            $('.translations-table').find('tr:last').find('td:first').find('input').focus();

                        }, success: function (response) { // What to do if we succeed
                            element.find('.loader').remove();
                            if (response.error) {
                                element.append('<i class="fa fa-exclamation-triangle" aria-hidden="true" style="color:red;" title="' + response.error + '"></i>');
                            } else {
                                $("#users-list").removeClass('hidden');
                                $(".info-users").prepend(response);
                                element.append('<i class="fa fa-check" aria-hidden="true" style="color:green;"></i>');
                            }
                        }
                    });
                }
            }
        });
    </script>
@endsection