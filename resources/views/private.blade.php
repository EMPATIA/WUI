@extends('private._private.index')

@section('header_styles')
    <link href="{!! asset(elixir('css/bootstrap-datetimepicker/bootstrap-datetimepicker.css')) !!}" rel="stylesheet" type="text/css"/>
@endsection

@section('content')
    @if(!$isEntityInSession)
        @include('_layouts.dashboard')
    @else
        <!-- Plupload Javascript fix and bootstrap fix -->
        <script src="/bootstrap/plupload-fix/bootstrap.js"></script>

        <div class="box-private">
        <div class="box-header">
            <div title="Summary">
                <h3 class="box-title">Performance</h3>
            </div>
        </div>


        <div class="box-body">
            <div id="div-server-filter">
                <div class="" title="Summary">
                    <h4> Analyze statistics per day:</h4>
                </div>
            </div>
        </div>
        <br>
        <div class="box-body">
            <div class="row">
                <div class='col-md-5'>
                    <label>{{ trans('private.datetimepicker_start') }}</label>
                    <div class="form-group">
                        <div class='input-group date' id='datetimepicker1'  >
                            <input type='text' class="form-control" value="" />
                            <span class="input-group-addon">
                        <i class="fa fa-calendar" aria-hidden="true"></i>
                    </span>
                        </div>
                    </div>
                </div>
                <div class='col-md-5'>
                    <label>{{ trans('private.datetimepicker_end') }}</label>
                    <div class="form-group">
                        <div class='input-group date' id='datetimepicker2'  >
                            <input type='text' class="form-control" />
                            <span class="input-group-addon">
                        <i class="fa fa-calendar" aria-hidden="true"></i>
                    </span>
                        </div>
                    </div>
                </div>
                <div class='col-md-5'>
                    <button id="buttonOk2" type="button" class="btn btn-flat btn-submit" onclick="showBarGraphs()">Ok</button>
                </div>
                <div class='col-12'>
                    <div id="div_graphics_bars"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="box-private">

        <div class="box-header">
            <div title="Summary">
                <h3 class="box-title">Component Analysis</h3>
            </div>
        </div>

        <div class="box-body">
            <label>Time range to analyze:</label>

            <div class="row">
                <div class="col-12 col-sm-10 col-md-8 col-lg-6">
                    <select id="timeFilter" name="timeFilter" onchange="getTime()" class="form-control" >
                        <option value="not">Select</option>
                        <option value="5mins">Last 5 minutes</option>
                        <option value="15mins">Last 15 minutes</option>
                        <option value="1h">Last hour</option>
                        <option value="1d">Last day</option>
                        <option value="1w">Last week</option>
                        <option value="1m">Last month</option>
                        <option value="1y">Last year</option>
                        <option value="range">Time range</option>
                    </select>
                </div>
            </div>

            <br>
            <br>
            <div class="row">
                <div class='col-md-5'>
                    <div class="form-group">
                        <div class='input-group date' id='datetimepicker6'  style="visibility: hidden">
                            <input type='text' class="form-control" />
                            <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                        </span>
                    </div>
                </div>
            </div>
            <div class='col-md-5'>
                <div class="form-group">
                    <div class='input-group date' id='datetimepicker7'  style="visibility: hidden">
                        <input type='text' class="form-control" />
                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                        </span>
                    </div>
                </div>
            </div>
            <div class='col-md-5'>
                <button id="button1" type="button" class="btn btn-flat btn-submit" onclick="loadData()">Ok</button>
            </div>
            <div class='col-12'>
                <div id="div_graphics" ></div>
            </div>
        <br><br><br>
        </div>
        </div>
    </div>
    @endif
@endsection


@section('scripts')
    @if(!$isEntityInSession)
        <script>

            $( document ).ready(function() {
                // Ini
                localStorage.setItem('sidebarPosition', 0);
                localStorage.setItem('currentSidebar', 'private');
                localStorage.removeItem('nextSidebar');
                localStorage.removeItem('previousSidebar');
                $(".pager").css("display", "none");

                $('#updateStatusModal').on('show.bs.modal', function (event) {
                    $('#updateStatus').off();
                    $('#updateStatus').on('click', function (evt) {
                        var allVals = {};
                        var isValid = true;

                        //get inputs to update status
                        allVals['topicKey'] = $('#topicKeyStatus').val();
                        $('#updateStatusModal input:text').each(function () {
                            if($(this).val().length > 0){
                                allVals[$(this).attr('name')] = $(this).val();
                            }
                        });
                        $('#updateStatusModal textarea').each(function () {
                            if($(this).val().length > 0){
                                allVals[$(this).attr('name')] = $(this).val();
                            }
                        });

                        //all values ok to update
                        if (isValid) {
                            $('#updateStatusModal input:text').each(function () {
                                $(this).val('');
                            });
                            $('#updateStatusModal textarea').each(function () {
                                $(this).val('');
                            });

                            allVals.type =  $('#type_hidden').val();
                            allVals.cbKey = $('#cb_key_hidden').val();

                            $.ajax({
                                method: 'POST', // Type of response and matches what we said in the route
                                url: "{{action('TopicController@updateStatusTopic')}}", // This is the url we gave in the route
                                data: allVals, // a JSON object to send back
                                success: function (response) { // What to do if we succeed

                                    if (response != 'false') {
                                        window.location.reload();
                                        toastr.success('{{ trans('private.update_topic_status_ok') }}', '', {timeOut: 3000,positionClass: "toast-bottom-right"});
                                    }else{
                                        toastr.error('{{ trans('private.error_updating_state_or_sending_email_to_user') }}', '', {timeOut: 3000,positionClass: "toast-bottom-right"});
                                    }

                                    $('#updateStatusModal').modal('hide');
                                },
                                error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                                    $('#updateStatusModal').modal('hide');
                                }
                            });
                        }
                    });
                    //clear inputs and close update status modal
                    $('#closeUpdateStatus').on('click', function (evt) {
                        $('#updateStatusModal input:text').each(function () {
                            $(this).val('');
                        });
                        $('#updateStatusModal textarea').each(function () {
                            $(this).val('');
                        });

                        $('#updateStatusModal').modal('hide');
                    });

                });

                $.ajax({
                    'url': '{{ action('DashboardController@getRegisteredUsers') }}',
                    'method': 'get',
                    success: function(response) {
                        $(".registered").html(response);
                    },
                    error: function(error){
                        console.log(error);
                    }
                })
                $.ajax({
                    'url': '{{ action('DashboardController@getLoggedInUsers') }}',
                    'method': 'get',
                    success: function(response) {
                        $(".logged_users").html(response);
                    },
                    error: function(error){
                        console.log(error);
                    }
                })
                $.ajax({
                    'url': '{{ action('DashboardController@ideas') }}',
                    'method': 'get',
                    success: function(response) {
                        console.log('ideas: ' + response);
                        $(".ideas").html(response);
                    },
                    error: function(error){
                        console.log(error);
                    }
                })
                $.ajax({
                    'url': '{{ action('DashboardController@comments') }}',
                    'method': 'get',
                    success: function(response) {
                        console.log('ideas: ' + response);
                        $(".comments").html(response);
                    },
                    error: function(error){
                        console.log(error);
                    }
                })
            });

            var type;
            var cb;
            var topic;
            var post;

            function showAbuses(type, cbKey, topicKey, postKey){
                post = postKey;
                cb = cbKey;
                topic = topicKey;
                post = postKey;
                $('#showAbuses').modal('show');
            }

            $('#showAbuses').on('show.bs.modal', function (event) {

                $.ajax({
                    'url': '{{ action('TopicController@getAbusesPrivate') }}',
                    'method': 'get',
                    'data': { postKey: post, cbKey: cb, topicKey: topic, type: type},
                    success: function(response){
                        $("#abuses-body").html(response);
                    },
                    error: function(){
                        console.log("erro");
                    }
                })

                $('#closeShowAbuses').on('click', function (evt) {

                    $('#showAbuses').modal('hide');
                });

            });

            // ------------------- Modal related function
            function updateStatus(topicKey,status,cbKey,type){

                $('#topicKeyStatus').val(topicKey);
                $('#status_type_code').val(status);
                $('#cb_key_hidden').val(cbKey);
                $('#type_hidden').val(type);

                $('#updateStatusModal').modal('show');
            }

            //Auxiliary variable/counter. When equals to 2 triggers checkbox function to level datatables heigth
            var nTablesLoaded = 0;

            // ------------------- Datatables functions
            function databableUser(){

                var userSite = '';
                var site = "{{Session::get('X-SITE-KEY', null)}}";
                var url = "{{action('UsersController@tableUsersCompleted',['home' => true])}}";

                url = url+'&&site_key='+site;

                $('#users_list').DataTable({
                    language: {
                        url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}'
                    },
                    length: 10,
                    processing: true,
                    serverSide: true,
                    bDestroy: true,
                    paging: false,
                    bFilter: false,
                    bInfo: false,
                    bSort: false,
                    aaSorting: [[]],
                    ajax: {
                        url: url,
                        data: {
                            "length": 10,
                            "order": [{
                                column: '1',
                                dir:'asc'
                            }]
                        }
                    },
                    columns: [
                        { data: 'name', name: 'name' },
                        { data: 'email', name: 'email' },
                        { data: 'authorize', name: 'authorize' },
                        { data: 'action', name: 'action' }

                    ],
                    initComplete: function(settings, json) {
                        //when table completly loaded sums one value to nTablesLoaded
                        sumloadedTable();
                    },
                    order: [['1', 'asc']]
                });
            }

            function databableTopics() {
                $('#topics_list').DataTable({
                    language: {
                        url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}'
                    },
                    responsive: true,
                    processing: true,
                    serverSide: true,
                    paging: false,
                    bFilter: false,
                    bInfo: false,
                    bSort: false,
                    aaSorting: [[]],
                    ajax: '{!! action('TopicController@getFullTopicsTable','topic') !!}',
                    columns: [
                        {data: 'title', name: 'title'},
                        {data: 'action', name: 'action', searchable: false, orderable: false, width: "80px"}
                    ],
                    order: [['1', 'asc']],
                    pageLength: 5,
                    initComplete: function(settings, json) {
                        //when table completly loaded sums one value to nTablesLoaded
                        sumloadedTable();
                    }
                });
            }

            @if(ONE::verifyModuleAccess('orchestrator', "technical_evaluation"))
                function databableTopicsTecnicalEvaluation(){
                    $('#topics_in_technical_evaluation_list').DataTable({
                        language: {
                            url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}'
                        },
                        responsive: true,
                        processing: true,
                        serverSide: true,
                        paging: false,
                        bFilter: false,
                        bInfo: false,
                        bSort: false,
                        ajax: '{!! action('TopicController@getTopicsTechnicalEvaluation','topic') !!}',
                        columns: [
                            {data: 'title', name: 'title'},
                        ],
                        pageLength: 5,
                        initComplete: function(settings, json) {
                            //when table completly loaded sums one value to nTablesLoaded
                            sumloadedTable();
                        }
                    });
                }
            @endif

            function databableActivePads() {
                $('#active_pads').DataTable({
                    language: {
                        url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}'
                    },
                    responsive: true,
                    processing: true,
                    serverSide: true,
                    paging: false,
                    bFilter: false,
                    bInfo: false,
                    bSort: false,
                    aaSorting: [[]],
                    ajax: '{!! action('QuickAccessController@getActivePads', 'getActivePads') !!}',
                    columns: [
                        {data: 'title', name: 'title'}
                    ],
                    order: [['0', 'desc']],
                    pageLength: 5
                });

            }

            function datatablePostsToModerate(){
                $('#moderation_posts').DataTable({
                    language: {
                        url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}'
                    },
                    responsive: true,
                    processing: true,
                    serverSide: true,
                    paging: false,
                    bFilter: false,
                    bInfo: false,
                    bSort: false,
                    aaSorting: [[]],
                    ajax: '{!! action('QuickAccessController@getPostsToModerate') !!}',
                    columns: [
                        {data: 'topic', name: 'topic'},
                        {data: 'created_by', name: 'created_by'},
                        {data: 'content', name: 'content'},
                        { data: 'action', name: 'action', width: '5%' }
                    ],
                    order: [['0', 'desc']],
                    pageLength: 5
                });
            }

            //Auxiliary function. When nTablesLoaded equals to 2 triggers checkbox function to level datatables heigth
            var sumloadedTable = function () {
                nTablesLoaded++;
                if(nTablesLoaded == 2){
                    checkBoxes();
                }
            }

            function checkBoxes(){
                $(document).ready(function () {
// Setting equal heights for div's with jQuery
                    if ($(window).width() > 768) {
                        var highestBox = 0;
                        $('.box-side-by-side').each(function () {
                            console.log($(this).height());
                            if ($(this).height() > highestBox) {
                                highestBox = $(this).height();
                            }
                        });
                        $('.box-side-by-side').height(highestBox);
                    }

                });
            }

            function databableUnreadMessages() {
                $('#users_messages_list').DataTable({
                    language: {
                        url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}'
                    },
                    responsive: true,
                    processing: true,
                    serverSide: true,
                    paging: false,
                    bFilter: false,
                    bInfo: false,
                    bSort: false,
                    aaSorting: [[]],
                    ajax: '{!! action('QuickAccessController@getUsersWithUnreadMessages') !!}',
                    columns: [
                        {data: 'name', name: 'name'},
                        {data: 'email', name: 'email'},
                        {data: 'created_at', name: 'created_at'}
                    ],
                    order: [['0', 'desc']],
                    pageLength: 5
                });

            }

            $(document).on("click", ".manual-login-level", function(event) {
                var manualLoginLevel = $(this).attr('href');
                var table = $('#users_list').DataTable();
                $.ajax({
                    method: 'POST',
                    url: manualLoginLevel,
                    data: {
                        page: 'private_home',
                        moderation: false
                    },
                    success: function (response) {
                        table.ajax.reload();
                        toastr.success('{{trans('privateEntityLoginLevels.manual_login_level_ok') }}', '', {positionClass: "toast-bottom-full-width"});
                    },
                    error: function () {
                        table.ajax.reload();
                        toastr.error('{{trans('privateEntityLoginLevels.manual_login_level_failed') }}', '', {positionClass: "toast-bottom-full-width"});
                    }
                });
                return false;
            });

        </script>

    @endif

    <script src="{!! asset(elixir('js/bootstrap-datetimepicker/moment-with-locales.js')) !!}"></script>
    <script src="{!! asset(elixir('js/bootstrap-datetimepicker/bootstrap-datetimepicker.js')) !!}"></script>
    <script>
        $(function () {
            $('#datetimepicker6').datetimepicker();
            $('#datetimepicker7').datetimepicker({
                useCurrent: false //Important! See issue #1075
            });
            $("#datetimepicker6").on("dp.change", function (e) {
                $('#datetimepicker7').data("DateTimePicker").minDate(e.date);
            });
            $("#datetimepicker7").on("dp.change", function (e) {
                $('#datetimepicker6').data("DateTimePicker").maxDate(e.date);
            });
            var d = new Date();
            var month = d.getMonth()+1;
            var day = d.getDate();

            var output = (month<10 ? '0' : '') + month + '/' +
                (day<10 ? '0' : '') + day + '/' +
                d.getFullYear();

            $("#datetimepicker6 > input").val(output + " 00:01 AM");
        });
    </script>
    <script>

        function getTime(){


            if(  $("#timeFilter").val() != null || $("#timeFilter").val() !="not"){
                if(  $("#timeFilter").val() == "range"){
                    var e = document.getElementById("datetimepicker6");
                    e.style.visibility = 'visible';
                    var e = document.getElementById("datetimepicker7");
                    e.style.visibility = 'visible';

                } else{
                    var e = document.getElementById("datetimepicker6");
                    e.style.visibility = 'hidden';
                    var e = document.getElementById("datetimepicker7");
                    e.style.visibility = 'hidden';
                }
            }
        }

    </script>
    <script>

        $.ajax({
            method: 'POST', // Type of response and matches what we said in the route
            url: "{{action('PerformanceController@loadAllServers')}}", // This is the url we gave in the route
            data: {

            }, // a JSON object to send back
            success: function (response) { // What to do if we succeed
                if (response != 'false') {
                    $('#div-server-filter').empty();
                    $('#div-server-filter').append(response);
                    if(response == 'No data to show.')
                        $('#div_graphics').empty();
                }
            },
            error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                console.log("error sending ajax request");
            }
        });
    </script>
    <script>

        function loadData(){

            var timeFilter=$("#timeFilter").val();
            var startRange = null;
            var endRange = null;
            var flag=0;

            if(timeFilter=="range"){
                var startRangeAux= new Date($("#datetimepicker6 >input").val());
                startRange =  startRangeAux.getFullYear() + "-" +(startRangeAux.getMonth()+1) + "-" + startRangeAux.getDate() + " 00:00:01";
                var endRangeAux = new Date($("#datetimepicker7 >input").val());
                endRange =  endRangeAux.getFullYear() + "-" +(endRangeAux.getMonth()+1) + "-" + endRangeAux.getDate() + " 23:59:59";
            }
            if(timeFilter=="range"){
                if(startRangeAux == 'Invalid Date'){ alert("Select a start time range to analyze"); flag=1;}
                if(endRangeAux == 'Invalid Date'){ alert("Select a end time range to analyze"); flag=1;}
            }
            if(timeFilter=="not"){
                alert("Select a time range to analyze"); flag=1;
            }
            var server = $("#serverFilter").val();
            if(server == "not") {alert("Chose a Server"); flag=1;}



            if( flag==0) {
                $.ajax({
                    method: 'POST', // Type of response and matches what we said in the route
                    url: "{{action('PerformanceController@loadDataPerformance')}}", // This is the url we gave in the route
                    data: {
                        serverIp: server,
                        timeFilter: timeFilter,
                        startRange: startRange,
                        endRange: endRange,
                    }, // a JSON object to send back
                    success: function (response) { // What to do if we succeed
                        if (response != 'false') {
                            console.log("ajax success")
                            console.log(response)
                            $('#div_graphics').empty();
                            $('#div_graphics').append(response);

                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                        console.log("error sending ajax request");
                    }
                });
            }
        };
    </script>
    <script>
        // sample data array
        var sample_data = [
            {"Dia": 1, "name":"Utilizadores registados", "total": 1},
            {"Dia": 2, "name":"Utilizadores registados", "total": 2},
            {"Dia": 3, "name":"Utilizadores registados", "total": 4},
            {"Dia": 4, "name":"Utilizadores registados", "total": 0},
            {"Dia": 5, "name":"Utilizadores registados", "total": 4},
            {"Dia": 6, "name":"Utilizadores registados", "total": 5},
            {"Dia": 7, "name":"Utilizadores registados", "total": 1},
            {"Dia": 8, "name":"Utilizadores registados", "total": 0},
            {"Dia": 9, "name":"Utilizadores registados", "total": 3},
            {"Dia": 10, "name":"Utilizadores registados", "total": 10},
            {"Dia": 11, "name":"Utilizadores registados", "total": 1},
            {"Dia": 12, "name":"Utilizadores registados", "total": 2},
            {"Dia": 13, "name":"Utilizadores registados", "total": 0},
            {"Dia": 14, "name":"Utilizadores registados", "total": 3},
            {"Dia": 15, "name":"Utilizadores registados", "total": 0},
            {"Dia": 16, "name":"Utilizadores registados", "total": 0}
        ];
        // instantiate d3plus
        var visualization = d3plus.viz()
            .container("#registeredUsers")  // container DIV to hold the visualization
            .data(sample_data)  // data to use with the visualization
            .type("line")       // visualization type
            .id("name")         // key for which our data is unique on
            .text("name")       // key to use for display text
            .y("total")         // key to use for y-axis
            .x("Dia")          // key to use for x-axis
            .resize(true)
            .draw();            // finally, draw the visualization!
    </script>
    <script>

        function getAvgPage(){
            $.ajax({
                method: 'GET', // Type of response and matches what we said in the route
                url: "{{action('PerformanceController@loadAvgGraphPage')}}", // This is the url we gave in the route
                success: function (response) { // What to do if we succeed
                    if (response != 'false') {
                        console.log("ajax success")
                        console.log(response)


                    }
                },
                error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                    console.log("error sending ajax request");
                }
            });


        }
    </script>
    <script>

        $(function () {
            $('#datetimepicker1').datetimepicker();
            $('#datetimepicker2').datetimepicker({
                useCurrent: false //Important! See issue #1075
            });
            $("#datetimepicker1").on("dp.change", function (e) {
                $('#datetimepicker2').data("DateTimePicker").minDate(e.date);
            });
            $("#datetimepicker2").on("dp.change", function (e) {
                $('#datetimepicker1').data("DateTimePicker").maxDate(e.date);
            });

            var d = new Date();
            var month = d.getMonth()+1;
            var day = d.getDate();

            var output = (month<10 ? '0' : '') + month + '/' +
                (day<10 ? '0' : '') + day + '/' +
                d.getFullYear();

            $("#datetimepicker1 > input").val(output + " 00:01 AM");
        });

    </script>
    <script>

        function showBarGraphs() {

            flag=0;
            var serverIp = $("#serverFilter").val();

            if(serverIp == "not") {alert("Chose a Server"); flag=1;}
            var startRangeAux= new Date($("#datetimepicker1 >input").val());
            startRange =  startRangeAux.getFullYear() + "-" +(startRangeAux.getMonth()+1) + "-" + startRangeAux.getDate() + " 00:00:01";
            var endRangeAux = new Date($("#datetimepicker2 >input").val());
            endRange =  endRangeAux.getFullYear() + "-" +(endRangeAux.getMonth()+1) + "-" + endRangeAux.getDate() + " 23:59:59";

            if(startRangeAux == 'Invalid Date'){ alert("Select a start time range to analyze"); flag=1;}
            if(endRangeAux == 'Invalid Date'){ alert("Select a end time range to analyze"); flag=1;}
            if(flag==0) {
                $.ajax({
                    method: 'POST', // Type of response and matches what we said in the route
                    url: "{{action('PerformanceController@loadDataPerformanceBars')}}", // This is the url we gave in the route
                    data: {
                        serverIp: serverIp,
                        startRange: startRange,
                        endRange: endRange,
                    }, // a JSON object to send back
                    success: function (response) { // What to do if we succeed
                        if (response != 'false') {
                            console.log("ajax success")
                            console.log(response)
                            $('#div_graphics_bars').empty();
                            $('#div_graphics_bars').append(response);

                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                        console.log("error sending ajax request");
                    }
                });
            }
        }
    </script>
@endsection
