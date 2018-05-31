@extends('private._private.index')

@section('content')
    <div class="row">
        <div class="col-md-12">
            @php $form = ONE::form('emails', trans('privateEmails.details'), 'wui', 'email')
                ->settings(["model" => isset($email) ? $email->email_key : null, 'id' => isset($email) ? $email->email_key : null])
                ->show(null, null, null, 'EmailsController@index')
                ->create('EmailsController@store','EmailsController@index')
                ->edit(null, null)
                ->open();
            @endphp

            @if(ONE::actionType('emails') == 'show')

                {!! Form::oneText('recipient', trans('privateEmails.recipient'), $email->recipient ?? trans('privateEmails.recipient'), ['class' => 'form-control', 'id' => 'recipient',]) !!}
                {!! Form::oneText('subject', trans('privateEmails.subject'), $email->subject ?? trans('privateEmails.no_subject'), ['class' => 'form-control', 'id' => 'subject',]) !!}
                {!! Form::oneText('created_by', trans('privateEmails.created_by'), $userData ?? null, ['class' => 'form-control', 'id' => 'created_by', 'required']) !!}
                {!! Form::oneText('created_at', trans('privateEmails.created_at'), isset($email) ? $email->created_at : null, ['class' => 'form-control', 'id' => 'created_at']) !!}
                {!! Form::oneText('sent', trans('privateEmails.sent'), $email->sent ? trans('privateEmails.sent_affirmative') : trans('privateEmails.sent_negative'), ['class' => 'form-control', 'id' => 'sent']) !!}

                @if( $email->sent)
                    {!! Form::oneText('sent_at', trans('privateEmails.sent_at'), isset($email) ? $email->updated_at : null, ['class' => 'form-control', 'id' => 'sent_at']) !!}
                @endif

                <dt><i class="fa fa-eye"></i> Preview</dt>
                <div style="border:1px solid #999999;width:100%;height:350px;overflow:auto;">{!! $email->content !!}</div>
            @endif

            @if(ONE::actionType('emails') != 'show')
                <div class="form-group">
                    <label for="users">{{trans('form.to')}}</label>
                    <select id="users" multiple="multiple" style="width:100%" class="form-control filters filters_select" name="users[]">
                        @if(!empty($users))
                            @foreach($users as $user)
                                <option value="{{$user->email}}">{{$user->name }} | {{ $user->email}}</option>
                            @endforeach
                        @endif
                    </select>
                    <label for="send_to_all"><small>{{trans('form.send_to_all')}}</small></label>
                    <input type="checkbox" id="send_to_all" name="send_to_all">
                </div>
               {!! Form::oneText('subject', trans('form.subject'), null, ['class' => 'form-control', 'id' => 'subject']) !!}

                {!! Form::oneTextArea('message', trans('form.message'), isset($email) ? $email->content : null, ['class' => 'form-control tinyMCE', 'id' => 'message']) !!}
            @endif

            {!! $form->make() !!}
        </div>
    </div>

@endsection


@section('scripts')
    <!-- Plupload Javascript fix and bootstrap fix @ start -->
    <link rel="stylesheet" href="/bootstrap/plupload-fix/bootstrap.css">
    <script src="/bootstrap/plupload-fix/bootstrap.min.js"></script>
    <!-- Plupload Javascript fix and bootstrap fix @ End -->
    <script src="{{ asset('vendor/jildertmiedema/laravel-plupload/js/plupload.full.min.js') }}"></script>
    <script src="{{ asset("js/cropper.min.js") }}"></script>
    <script src="{{ asset("js/tinymce/tinymce.min.js") }}"></script>
    <script>
        $(".filters_select").select2();
        {!! ONE::addTinyMCE(".tinyMCE", ['action' => action('ContentManagerController@getTinyMCE')]) !!}

        $(document).on('change', '#send_to_all', function () {
            if(this.checked) {
                $("#users").prop('disabled',true);
            }else{
                $("#users").prop('disabled',false);
            }
        });
    </script>
@endsection
