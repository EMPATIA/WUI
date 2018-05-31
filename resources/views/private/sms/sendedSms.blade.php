@extends('private._private.index')

<?php
use Carbon\Carbon;
?>

@section('content')
    <div class="box box-primary">

        <div class="row">
            {{--TOTAL VOTES INFORMATION--}}
            <div class="col-md-12">
                <div class="box-info">
                    <div class="box-header voteAnalysis-total">
                        <h3 class="box-title"><i class="fa"></i> {{trans('privateCbsVoteAnalysis.sent_sms')}}</h3>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-sm-3 text-center">
                                <div>
                                    <img src="{{asset('/images/total_voters.png')}}" style="width: 5em">
                                </div>
                                <div>
                                    <strong>{{trans('privateCbsVoteAnalysis.total_sms')}}</strong>
                                </div>
                                <div>
                                    {{ $totalSendedSms ?? null }}
                                </div>
                            </div>
                            <div class="col-sm-3 text-center">
                                <div>
                                    <img src="{{asset('/images/total_votes.png')}}" style="width: 5em">
                                </div>
                                <div>
                                    <strong>{{trans('privateCbsVoteAnalysis.total_last_30d')}}</strong>
                                </div>
                                <div>
                                    {{ $totalSendedSmsLast30D ?? null}}
                                </div>
                            </div>
                            <div class="col-sm-3 text-center">
                                <div>
                                    <img src="{{asset('/images/positive_votes.png')}}" style="width: 5em">
                                </div>
                                <div>
                                    <strong>{{trans('privateCbsVoteAnalysis.total_last_24h')}}</strong>
                                </div>
                                <div>
                                    {{$totalSendedSmsLast24H ?? null}}
                                </div>
                            </div>
                            <div class="col-sm-3 text-center">
                                <div>
                                    <img src="{{asset('/images/negative_votes.png')}}" style="width: 5em">
                                </div>
                                <div>
                                    <strong>{{trans('privateCbsVoteAnalysis.total_last_hour')}}</strong>
                                </div>
                                <div>
                                    {{$totalSendedSmsLastHour ?? null}}
                                </div>
                            </div>
                        </div>

                        {{--FILTERS--}}

                        <div class="row">
                            <div class="col-12">
                                <br>
                                <h5 class="filterBy-title">{!! trans("privateCbs.filter_by") !!}</h5>
                            </div>
                            <div class="col-12 col-sm-6 col-md-3">
                                <label for="date">{!! trans("privateCbs.filterDate") !!}</label>
                                <div class="input-group date">
                        <span class="input-group-addon">
                        <i class="glyphicon glyphicon-th"></i>
                        </span>
                                    <input class="form-control oneDatePicker" style="width:40%" type="text" id="date" name="dateRange" value="" onchange="selectDateFilter()" required/>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-md-3">
                                <div class="form-group">
                                    <label for="sel1">{!! trans("privateSms.sent") !!}</label>
                                    <select class="form-control" id="selectValue" onchange="selectDateFilter()">
                                        <option></option>
                                        <option>{!! trans("privateSms.sent") !!}</option>
                                        <option>{!! trans("privateSms.not_sent") !!}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{--END TOTAL VOTES INFORMATION--}}
        </div>

        <div class="row">
            {{--TOP 10--}}
            <div class="col-md-12">
                <div class="box-info">

                    <div class="box-header">
                        <h3 class="box-title"><i class="fa"></i> {{ trans('privateSms.list') }}</h3>
                        <div class="sendSMS-btn">
                            {{-- {!! ONE::actionButtons(null, ['send' => 'SmsController@create']) !!} --}}
                            <a href="{{ action("SmsController@create") }}" class="btn btn-flat empatia" data-toggle="tooltip" data-delay="{'show':'1000'}" title="" data-original-title="form.send">
                                <i class="fa fa-send"></i> {{ trans('privateSms.send') }}
                            </a>
                        </div>
                    </div>

                    <div class="box-body">
                        <table id="sent_sms_list" class="table table-striped dataTable no-footer table-responsive">
                            <thead>
                            <tr>
                                <th>{{ trans('privateSms.recipient') }}</th>
                                <th>{{ trans('privateSms.created_by') }}</th>
                                <th>{{ trans('privateSms.created_at') }}</th>
                                <th>{{ trans('privateSms.sent') }}</th>
                                <th></th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>

                @endsection


                @section('scripts')

                    <script>
                        function selectDateFilter() {

                            var url = "{{action('SmsController@getSendedDatatableFilter')}}";

                            var start_date = '';
                            var end_date = '';
                            var value = '';
                            var sent ='';

                            if ($('#start_date').val() != '' && $('#end_date').val() != ''){

                                start_date = $("#date").val().split(" - ")[0];
                                end_date   =  $("#date").val().split(" - ")[1];
                                value = $("#selectValue").val();

                                if (start_date < end_date ) {
                                    if (value == '{!! trans("privateSms.sent") !!}'){
                                        sent = 1;
                                    }
                                    else if(value == '{!! trans("privateSms.not_sent") !!}'){
                                        sent = 0;
                                    }else{
                                        sent = 2;
                                    }

                                    url = url + '?start_date=' + start_date + '?end_date=' + end_date + '?value=' + sent;

                                    var tableUsers = $('#sent_sms_list').DataTable({
                                        language: {
                                            url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}'
                                        },
                                        processing: true,
                                        serverSide: true,
                                        bDestroy: true,
                                        ajax:
                                            {
                                                'url': url,
                                                "type": "GET"
                                            },
                                        columns: [
                                            {data: 'recipient', name: 'recipient', searchable: true},
                                            {data: 'created_by', name: 'created_by', searchable: false},
                                            {data: 'created_at', name: 'created_at', searchable: false},
                                            {data: 'sent', name: 'sent', searchable: false},
                                            {
                                                data: 'action',
                                                name: 'action',
                                                searchable: false,
                                                orderable: false,
                                                width: "30px"
                                            },
                                        ],
                                        order: [['1', 'asc']],
                                    });
                                }
                            }
                        }

                    </script>

                    <!-- Date Range Picker - JavaScript -->

                    <script type="text/javascript">
                        $(function() {
                            $('input[name="dateRange"]').daterangepicker({
                                singleDatePicker:false,
                                timePicker: true,
                                timePicker24Hour: true,
                                timePickerIncrement: 5,
                                startDate: "{!! Carbon::now()->subDay()->format('Y-m-d H:i:s') !!}",
                                endDate: "{!! Carbon::now()->format('Y-m-d H:i:s') !!}",
                                locale: {
                                    format: 'YYYY-MM-DD HH:mm:SS'
                                }
                            });
                        });
                    </script>
@endsection
