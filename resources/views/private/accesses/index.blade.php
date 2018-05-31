@extends('private._private.index')

@section('content')
    <?php
    use Carbon\Carbon;
    ?>

    <div class="box box-primary" style="margin-top: 10px">
        <form action="javascript:reloadTable()" id="search_access" class="box-body">
            <div class="row">
                <!-- Date-->
                <div class="col-6 col-sm-3 col-lg-3">
                    <label for="date">{!! trans("privateLogs.filterDate") !!}</label>
                    <div class="input-group date">
                        <span class="input-group-addon">
                            <i class="glyphicon glyphicon-th"></i>
                        </span>
                        <input class="form-control oneDatePicker" style="width:40%" type="text" id="date" name="dateRange" value=""/>
                    </div>
                </div>

                <!-- Entity/Site-->
                <div class="col-6 col-sm-3 col-lg-3">
                    <label for="status_type_code">{{trans('privateLogs.site')}}</label><br>
                    <select id="sites" style="width:100%" class="form-control filters filters_select" name="sites">
                        <option selected="selected" value="">{{trans('privateLogs.select_site')}}</option>
                        @foreach($sites as $key => $site){
                            @foreach($site as $siteKey => $s) {
                                <option value="{{$siteKey}}">{{$s}}</option>
                            @endforeach
                        @endforeach
                    </select>

                </div>
                <!-- Cb -->
                <div class="col-6 col-sm-3 col-lg-3">
                    <label for="status_type_code">{{trans('privateLogs.cb')}}</label><br>
                    <select id="cbs" style="width:100%" class="form-control filters filters_select" name="cbs">
                        <option selected="selected" value="">{{trans('privateLogs.select_cb')}}</option>
                        @foreach($cbs as $cbKey => $cb)
                            <option value="{{$cbKey}}">{{$cb}}</option>
                        @endforeach

                    </select>
                </div>
                <!-- Action -->
                <div class="col-6 col-sm-3 col-lg-3">
                    <label for="status_type_code">{{trans('privateLogs.action')}}</label><br>
                    <select id="actions" style="width:100%" class="form-control filters filters_select" name="actions", multiple = "multiple">
                        @foreach($actions as $actionKey => $action)
                            <option value="{{$actionKey}}">{{$action}}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Result-->
                <div class="col-6 col-sm-3 col-lg-3">
                    <label for="status_type_code">{{trans('privateLogs.result')}}</label><br>
                    <select id="result" style="width:100%" class="form-control filters filters_select" name="result">
                        <option selected="selected" value="">{{trans('privateLogs.select_result')}}</option>

                        @foreach($results as $resultKey => $result)
                            <option value="{{$resultKey}}">{{$result}}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Ip-->
                <div class="col-6 col-sm-3 col-lg-3">
                    <label for="status_type_code">{{trans('privateLogs.ip')}}</label><br>
                    <input type="text" id="ip" name="ip" class="form-control filters filters_input filters_ip">
                </div>

                <!-- Email-->
                <div class="col-6 col-sm-3 col-lg-3">
                    <label for="status_type_code">{{trans('privateLogs.email')}}</label><br>
                    <input type="text" id="email" name="email" class="form-control filters filters_input filters_email">
                </div>

                <div class="col-6 col-md-3 col-lg-2" >
                    <br>
                    <input type="submit" form="search_access" value="{{ trans('privateLogs.search') }}" class="btn-submit" style="float: right; margin-top:13px">
                </div>
            </div>
        </form>
    </div>

    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title"><i class="fa"></i> {{ trans('privateLogs.list_accesses') }}</h3>
        </div>

        <div class="box-body">
            <table id="accesses_list" class="table table-hover table-striped dataTable no-footer table-responsive">
                <thead>
                <tr>
                    <th>{{ trans('privateLogs.date') }}</th>
                    <th>{{ trans('privateLogs.ip') }}</th>
                    {{--<th>{{ trans('privateLogs.url') }}</th>--}}
                    <th>{{ trans('privateLogs.site_Name') }}</th>
                    <th>{{ trans('privateLogs.email') }}</th>
                    <th>{{ trans('privateLogs.action') }}</th>
                    <th>{{ trans('privateLogs.result') }}</th>
                    {{--<th>{{ trans('privateLogs.content_key') }}</th>--}}
                    <th>{{ trans('privateLogs.cb_Title') }}</th>
                    {{--<th>{{ trans('privateLogs.topic_key') }}</th>--}}
                    {{--<th>{{ trans('privateLogs.post_key') }}</th>--}}
                    {{--<th>{{ trans('privateLogs.q_key') }}</th>--}}
                    {{--<th>{{ trans('privateLogs.vote_key') }}</th>--}}
                    <th>{{ trans('privateLogs.details') }}</th>
                    <th>{{ trans('privateLogs.error') }}</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection


@section('scripts')
    <script>
        var table;
        $(function () {
            var start_date = '';
            var end_date = '';

            table = $('#accesses_list').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: '{!! action('AccessesController@tableAccesses') !!}',
                    type: 'get',
                    data: function(d){
                        d.filters_static = buildSearchData();
                        d.select_actions = action();
                        d.start_date = $("#date").val().split(" - ")[0];
                        d.end_date = $("#date").val().split(" - ")[1];
                        _token        : "{{ csrf_token() }}";
                    },
                },
                columns: [
                    { data: 'date', name: 'date' },
                    { data: 'ip', name: 'ip' },
                    { data: 'site_key', name: 'site_key' },
                    { data: 'email', name: 'email' },
                    { data: 'action', name: 'action' },
                    { data: 'result', name: 'result' },
                    { data: 'cb_key', name: 'cb_key' },
                    { data: 'details', name: 'details' },
                    { data: 'error', name: 'error' },
                ],
            });

        });

        function reloadTable(){
            table.ajax.reload();
        }
        
        function buildSearchData(){
            var allValues = {};
            $('.filters').each(function () {
                if(this.classList.contains("filters_select")){
                    if($(this).find(":selected").val()!=""){
                        allValues[$(this).attr('name')] = $(this).find(":selected").val();
                    }
                }else if(this.classList.contains("filters_date")){
                    if($(this).val()!=""){
                        allValues[$(this).attr('name')] = $(this).val();
                    }
                }else if($('.filters').hasClass("filters_input")){
                    if($(".filters_ip").val()!=""){
                        allValues[$(".filters_ip").attr('name')] = $(".filters_ip").val();
                    }
                    if($(".filters_email").val()!=""){
                        allValues[$(".filters_email").attr('name')] = $(".filters_email").val();
                    }
                }
            });
            return allValues;
        }
        function action(){
            var action = $('#actions').select2('val');
            return action;
        }
        <!-- Date Range Picker - JavaScript -->
        $(function() {
            $('input[name="dateRange"]').daterangepicker({
                singleDatePicker:false,
                timePicker: true,
                timePicker24Hour: true,
                timePickerIncrement: 5,
                startDate: "{!! Carbon::now()->subDays(1)->format('Y-m-d H:i:s') !!}",
                endDate: "{!! Carbon::now()->format('Y-m-d H:i:s') !!}",
                locale: {
                    format: 'YYYY-MM-DD HH:mm:SS'
                }
            });
        });

        <!--Select2 -->
        $(document).ready(function() {

            $("#actions").select2({
                placeholder: '{{trans('privateLogs.select_action')}}',
                allowClear: true,
                maximumSelectionLength: 4,
                closeOnSelect: false,
            });
        });

    </script>
@endsection




