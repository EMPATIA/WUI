@extends('public.empaville_new._layouts.index')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h2 class="box-title" style="color:green"><i class="icon fa fa-check"></i> {!! trans("publicQuestionnaire.form_submitted") !!}</h2>
                </div>
                @if($message== 0)
                    <div class="box-body" style="min-height: 250px; margin:10px;">
                        <div style="margin-left: 50px;margin-top: 30px; font-size: 18px">
                            @if(Session::has('user'))
                                <p>{!! trans("publicQuestionnaire.dear") !!} {{Session::get('user')->name}},</p>
                            @endif
                            <p>{!! trans("publicQuestionnaire.form_submitted_message") !!}</p>

                        </div>
                    </div>
                @elseif($message == 3)
                    <div class="box-body" style="min-height: 250px; margin:10px;">
                        <div style="margin-left: 50px;margin-top: 30px; font-size: 18px">

                            <p>{!! trans("publicQuestionnaire.form_submitted_message") !!}</p>
                        </div>
                    </div>
                @else
                    <div class="box-body" style="min-height: 250px;text-align: center">
                        <div class="alert alert-warning " style="width: 80%; margin:auto; margin-top: 50px;">
                            <h4><i class="icon fa fa-warning"></i> {!! trans("publicQuestionnaire.warning") !!}!</h4>
                            <p>{!! trans("publicQuestionnaire.already_form_submitted") !!}</p>
                        </div>
                    </div>
                @endif
            </div>
            <!-- /.box-body -->
        </div>
    </div>
@endsection
