@extends('private._private.index')

@section('content')

    <div class="row">
        <div class="col-md-12">
            @php $form = ONE::form('newsletterSubscription', trans('privateNewsletterSubscriptions.detail'))
                ->settings(["model" => $newsletterSubscription->newsletter_subscription_key ?? null, 'id' => $newsletterSubscription->newsletter_subscription_key ?? null])
                ->show(null, null, null, 'EmailsController@index')
                ->create(null,null)
                ->edit(null, null)
                ->open();
            @endphp

            {!! Form::oneText('email', trans('privateNewsletterSubscriptions.email'), $newsletterSubscription->email ?? null, ['class' => 'form-control', 'id' => 'email']) !!}
            {!! Form::oneText('created_at', trans('privateNewsletterSubscriptions.created_at'), $newsletterSubscription->created_at ?? null, ['class' => 'form-control', 'id' => 'created_at']) !!}

            @if (ONE::actionType("newsletterSubscription")=="show")
                <dt>{{ trans("privateNewsletterSubscriptions.is_active") }}</dt>
                @if ($newsletterSubscription->active == '1')
                    <span class="btn-sent btn btn-flat btn-success btn-sm">
                        <i class="fa fa-check" aria-hidden="true"></i>
                    </span>
                @else
                    <span class="btn-sent btn btn-flat btn-danger btn-sm">
                        <i class="fa fa-times" aria-hidden="true"></i>
                    </span>
                @endif
            @endif

            {!! $form->make() !!}
        </div>
    </div>

@endsection

