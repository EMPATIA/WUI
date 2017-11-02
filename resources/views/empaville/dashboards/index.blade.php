@extends('empaville._layout.index')

@section('content')
    <div class="row">
        <section class="col-lg-6 connectedSortable ui-sortable">
            <div class="row">
            {{--TOTAL VOTES INFORMATION--}}
                <div class="col-md-12">
                    <div class="box box-success">
                        <div class="box-header">
                            {{trans('dashboard.count_total_votes')}}
                        </div>
                        <div class="box-body">
                            <div class="col-sm-3 text-center">
                                <div class="row">
                                    {{trans('dashboard.total_votes')}}
                                </div>
                                <div class="row">
                                    <img src="/images/total_votes.png"  >
                                </div>
                                <div class="row">
                                    {{$voteSession['summary']->total}}
                                </div>
                            </div>
                            <div class="col-sm-3 text-center">
                                <div class="row">
                                    {{trans('dashboard.total_positive_votes')}}
                                </div>
                                <div class="row">
                                    <img src="/images/positive_votes.png"  >
                                </div>
                                <div class="row">
                                    {{$voteSession['summary']->total_positives}}
                                </div>
                            </div>
                            <div class="col-sm-3 text-center">
                                <div class="row">
                                    {{trans('dashboard.total_negative_votes')}}
                                </div>
                                <div class="row">
                                    <img src="/images/negative_votes.png" >
                                </div>
                                <div class="row">
                                    {{$voteSession['summary']->total_negatives}}
                                </div>
                            </div>
                            <div class="col-sm-3 text-center">
                                <div class="row">
                                    {{trans('dashboard.total_voters')}}
                                </div>
                                <div class="row">
                                    <img src="/images/total_voters.png" >
                                </div>
                                <div class="row">
                                    {{$voteSession['summary']->total_users_voted}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            {{--END TOTAL VOTES INFORMATION--}}
            </div>
            <div class="row">
                {{--CountByGender--}}
                <div class="col-md-12">
                    <div class="box box-success">
                        <div class="box-header">
                            {{trans('dashboard.count_total_votes_by_gender')}}
                        </div>
                        <div class="box-body">
                            <div class="col-md-11">
                                <canvas id="countByGender" style="height:230px"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{--First/second Votes by Gender --}}
            <div class="row">
                {{--FirstByGender--}}
                <div class="col-md-12">
                    <div class="box box-success">
                        <div class="box-header">
                            {{trans('dashboard.count_first_by_gender')}}
                        </div>
                        <div class="box-body">
                            <div class="col-md-11">
                                <canvas id="firstByGender" style="height:230px"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{--First/second Votes by Gender --}}
            <div class="row">
                {{--SecondByGender--}}
                <div class="col-md-12">
                    <div class="box box-success">
                        <div class="box-header">
                            {{trans('dashboard.count_second_by_gender')}}
                        </div>
                        <div class="box-body">
                            <div class="col-md-11">
                                <canvas id="secondByGender" style="height:230px"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                {{--CountByAge--}}
                <div class="col-md-12">
                    <div class="box box-success">
                        <div class="box-header">
                            {{trans('dashboard.count_total_votes_by_age')}}
                        </div>
                        <div class="box-body">
                            <div class="col-md-11">
                                <canvas id="countByAge" style="height:230px"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                {{--FirstByAge--}}
                <div class="col-md-12">
                    <div class="box box-success">
                        <div class="box-header">
                            {{trans('dashboard.count_first_by_gender')}}
                        </div>
                        <div class="box-body">
                            <div class="col-md-11">
                                <canvas id="firstByAge" style="height:230px"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{--First/second Votes by Age --}}
            <div class="row">
                {{--SecondByGender--}}
                <div class="col-md-12">
                    <div class="box box-success">
                        <div class="box-header">
                            {{trans('dashboard.count_second_by_gender')}}
                        </div>
                        <div class="box-body">
                            <div class="col-md-11">
                                <canvas id="secondByAge" style="height:230px"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </section>

        <section class="col-lg-6 connectedSortable ui-sortable">
            <div class="row">
                {{--TOP 10--}}
                <div class="col-md-12">
                    <div class="box box-success">
                        <div class="box-header">
                            <h3 class="box-title">{{trans('empaville.top')}}</h3>
                        </div>
                        <div class="box-body no-padding">
                            <table class="table table-condensed">
                                <tbody>
                                <tr>
                                    <th style="width: 10px">#</th>
                                    <th>{{trans('dashboard.proposal')}}</th>
                                    <th>{{trans('dashboard.balance')}}</th>
                                </tr>
                                @foreach($voteSession["top"] as $key => $topProposal)
                                    <tr>
                                        <td>{{$key + 1 }} </td>
                                        <td>{{$topProposal->title}}</td>
                                        </td>
                                        <td>
                                            @if($topProposal->balance >= 0 )
                                                <span class="badge bg-green"> {{ $topProposal->balance}}</span>
                                            @else
                                                <span class="badge bg-red"> {{ $topProposal->balance}}</span>
                                            @endif
                                        </td>

                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                {{--TOP 10 END--}}
            </div>
            <div class="row">
                {{--CountByNb--}}
                <div class="col-md-12">
                    <div class="box box-success">
                        <div class="box-header">
                            {{trans('dashboard.count_total_votes_by_nb')}}
                        </div>
                        <div class="box-body">
                            <div class="col-md-11">
                                <canvas id="countByNb" style="height:230px"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{--First/second Votes by Nb --}}
            <div class="row">
                {{--FirstByNb--}}
                <div class="col-md-12">
                    <div class="box box-success">
                        <div class="box-header">
                            {{trans('dashboard.count_first_by_nb')}}
                        </div>
                        <div class="box-body">
                            <div class="col-md-11">
                                <canvas id="firstByNb" style="height:230px"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{--First/second Votes by Gender --}}
            <div class="row">
                {{--SecondByNb--}}
                <div class="col-md-12">
                    <div class="box box-success">
                        <div class="box-header">
                            {{trans('dashboard.count_second_by_nb')}}
                        </div>
                        <div class="box-body">
                            <div class="col-md-11">
                                <canvas id="secondByNb" style="height:230px"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


        </section>


    </div>


    <script>
        $(function () {



            var barChartOptions = {
                //Boolean - Whether the scale should start at zero, or an order of magnitude down from the lowest value
                scaleBeginAtZero: true,
                //Boolean - Whether grid lines are shown across the chart
                scaleShowGridLines: true,
                //String - Colour of the grid lines
                scaleGridLineColor: "rgba(0,0,0,.2)",
                //Number - Width of the grid lines
                scaleGridLineWidth: 1,
                //Boolean - Whether to show horizontal lines (except X axis)
                scaleShowHorizontalLines: true,
                //Boolean - Whether to show vertical lines (except Y axis)
                scaleShowVerticalLines: true,
                //Boolean - If there is a stroke on each bar
                barShowStroke: true,
                //Number - Pixel width of the bar stroke
                barStrokeWidth: 2,
                //Number - Spacing between each of the X value sets
                barValueSpacing: 5,
                //Number - Spacing between data sets within X values
                barDatasetSpacing: 1,
              //Boolean - whether to make the chart responsive
              responsive: true,
              maintainAspectRatio: false
        };
            barChartOptions.datasetFill = false;
            //--------------
            //- CountByGender -
            //--------------
            // Get context with jQuery - using jQuery's .get() method.
            var countByGender = $("#countByGender").get(0).getContext("2d");
            // This will get the first returned node in the jQuery collection.
            var countByGenderBarChart = new Chart(countByGender);

            var countByGender = {
                labels: ["Male", "Female"],
                datasets: [
                    {
                        label: "Positives",
                        fillColor: "rgba(0, 255, 0, 1)",
                        strokeColor: "rgba(0, 255, 0, 1)",
                        pointColor: "rgba(0, 255, 0, 1)",
                        pointStrokeColor: "#c1c7d1",
                        pointHighlightFill: "#fff",
                        pointHighlightStroke: "rgba(220,220,220,1)",
                        data:[
                            {!! $votesByGender->Male->Positives !!},
                            {!! $votesByGender->Female->Positives !!}
                        ]
                    },
                    {
                        label: "Negatives",
                        fillColor: "rgba(255,0,0,0.9)",
                        strokeColor: "rgba(255,0,0,0.8)",
                        pointColor: "#3b8bba",
                        pointStrokeColor: "rgba(255,0,0,1)",
                        pointHighlightFill: "#fff",
                        pointHighlightStroke: "rgba(60,141,188,1)",
                        data: [
                            {!! $votesByGender->Male->Negatives !!},
                            {!! $votesByGender->Female->Negatives !!}
                        ]
                    }
                ]
            };

            countByGenderBarChart.Bar(countByGender, barChartOptions);


            //--------------
            //- FirstByGender -
            //--------------
            // Get context with jQuery - using jQuery's .get() method.
            var firstByGender = $("#firstByGender").get(0).getContext("2d");
            // This will get the first returned node in the jQuery collection.
            var firstByGenderBarChart = new Chart(firstByGender);

            var firstByGender = {
                labels: ["Male", "Female"],
                datasets: [
                    {
                        label: "Positives",
                        fillColor: "rgba(0, 255, 0, 1)",
                        strokeColor: "rgba(0, 255, 0, 1)",
                        pointColor: "rgba(0, 255, 0, 1)",
                        pointStrokeColor: "#c1c7d1",
                        pointHighlightFill: "#fff",
                        pointHighlightStroke: "rgba(220,220,220,1)",
                        data:[
                            {!! $firstByGender->Male->Positives !!},
                            {!! $firstByGender->Female->Positives !!}
                        ]
                    },
                    {
                        label: "Negatives",
                        fillColor: "rgba(255,0,0,0.9)",
                        strokeColor: "rgba(255,0,0,0.8)",
                        pointColor: "#3b8bba",
                        pointStrokeColor: "rgba(255,0,0,1)",
                        pointHighlightFill: "#fff",
                        pointHighlightStroke: "rgba(60,141,188,1)",
                        data: [
                            {!! $firstByGender->Male->Negatives !!},
                            {!! $firstByGender->Female->Negatives !!}
                        ]
                    }
                ]
            };
            firstByGenderBarChart.Bar(firstByGender, barChartOptions);

            //--------------
            //- SecondByGender -
            //--------------
            // Get context with jQuery - using jQuery's .get() method.
            var secondByGender = $("#secondByGender").get(0).getContext("2d");
            // This will get the first returned node in the jQuery collection.
            var secondByGenderBarChart = new Chart(secondByGender);

            var secondByGender = {
                labels: ["Male", "Female"],
                datasets: [
                    {
                        label: "Positives",
                        fillColor: "rgba(0, 255, 0, 1)",
                        strokeColor: "rgba(0, 255, 0, 1)",
                        pointColor: "rgba(0, 255, 0, 1)",
                        pointStrokeColor: "#c1c7d1",
                        pointHighlightFill: "#fff",
                        pointHighlightStroke: "rgba(220,220,220,1)",
                        data:[
                            {!! $secondByGender->Male->Positives !!},
                            {!! $secondByGender->Female->Positives !!}
                        ]
                    },
                    {
                        label: "Negatives",
                        fillColor: "rgba(255,0,0,0.9)",
                        strokeColor: "rgba(255,0,0,0.8)",
                        pointColor: "#3b8bba",
                        pointStrokeColor: "rgba(255,0,0,1)",
                        pointHighlightFill: "#fff",
                        pointHighlightStroke: "rgba(60,141,188,1)",
                        data: [
                            {!! $secondByGender->Male->Negatives !!},
                            {!! $secondByGender->Female->Negatives !!}
                        ]
                    }
                ]
            };
            secondByGenderBarChart.Bar(secondByGender, barChartOptions);


            //--------------
            //- CountByNb -
            //--------------
            // Get context with jQuery - using jQuery's .get() method.
            var countByNb = $("#countByNb").get(0).getContext("2d");
            // This will get the first returned node in the jQuery collection.
            var countByNbBarChart = new Chart(countByNb);

            var countByNb = {
                labels: ["Uptown", "Middletown", "Downtown"],
                datasets: [
                    {
                        label: "Positives",
                        fillColor: "rgba(0, 255, 0, 1)",
                        strokeColor: "rgba(0, 255, 0, 1)",
                        pointColor: "rgba(0, 255, 0, 1)",
                        pointStrokeColor: "#c1c7d1",
                        pointHighlightFill: "#fff",
                        pointHighlightStroke: "rgba(220,220,220,1)",
                        data:[
                            {!! empty($countByNb->Uptown->Positives)? 0 : $countByNb->Uptown->Positives!!},
                            {!! empty($countByNb->Middletown->Positives)? 0 : $countByNb->Middletown->Positives!!},
                            {!! empty($countByNb->Downtown->Positives)? 0 : $countByNb->Downtown->Positives !!}
                        ]
                    },
                    {
                        label: "Negatives",
                        fillColor: "rgba(255,0,0,0.9)",
                        strokeColor: "rgba(255,0,0,0.8)",
                        pointColor: "#3b8bba",
                        pointStrokeColor: "rgba(255,0,0,1)",
                        pointHighlightFill: "#fff",
                        pointHighlightStroke: "rgba(60,141,188,1)",
                        data: [
                            {!! empty($countByNb->Uptown->Negatives)? 0 : $countByNb->Uptown->Negatives!!},
                            {!! empty($countByNb->Middletown->Negatives)? 0 : $countByNb->Middletown->Negatives!!},
                            {!! empty($countByNb->Downtown->Negatives)? 0 : $countByNb->Downtown->Negatives !!}
                        ]
                    }
                ]
            };

            countByNbBarChart.Bar(countByNb, barChartOptions);


            //--------------
            //- FirstByGender -
            //--------------
            // Get context with jQuery - using jQuery's .get() method.
            var firstByNb = $("#firstByNb").get(0).getContext("2d");
            // This will get the first returned node in the jQuery collection.
            var firstByNbBarChart = new Chart(firstByNb);

            var firstByNb = {
                labels: ["Uptown", "Middletown", "Downtown"],
                datasets: [
                    {
                        label: "Positives",
                        fillColor: "rgba(0, 255, 0, 1)",
                        strokeColor: "rgba(0, 255, 0, 1)",
                        pointColor: "rgba(0, 255, 0, 1)",
                        pointStrokeColor: "#c1c7d1",
                        pointHighlightFill: "#fff",
                        pointHighlightStroke: "rgba(220,220,220,1)",
                        data:[
                            {!! empty($firstByNb->Uptown->Positives)? 0 : $firstByNb->Uptown->Positives!!},
                            {!! empty($firstByNb->Middletown->Positives)? 0 : $firstByNb->Middletown->Positives!!},
                            {!! empty($firstByNb->Downtown->Positives)? 0 : $firstByNb->Downtown->Positives !!}
                        ]
                    },
                    {
                        label: "Negatives",
                        fillColor: "rgba(255,0,0,0.9)",
                        strokeColor: "rgba(255,0,0,0.8)",
                        pointColor: "#3b8bba",
                        pointStrokeColor: "rgba(255,0,0,1)",
                        pointHighlightFill: "#fff",
                        pointHighlightStroke: "rgba(60,141,188,1)",
                        data: [
                            {!! empty($firstByNb->Uptown->Negatives)? 0 : $firstByNb->Uptown->Negatives!!},
                            {!! empty($firstByNb->Middletown->Negatives)? 0 : $firstByNb->Middletown->Negatives!!},
                            {!! empty($firstByNb->Downtown->Negatives)? 0 : $firstByNb->Downtown->Negatives !!}
                        ]
                    }
                ]
            };
            firstByNbBarChart.Bar(firstByNb, barChartOptions);

            //--------------
            //- SecondByGNb -
            //--------------
            // Get context with jQuery - using jQuery's .get() method.
            var secondByNb = $("#secondByNb").get(0).getContext("2d");
            // This will get the first returned node in the jQuery collection.
            var secondByNbBarChart = new Chart(secondByNb);

            var secondByNb = {
                labels: ["Uptown", "Middletown", "Downtown"],
                datasets: [
                    {
                        label: "Positives",
                        fillColor: "rgba(0, 255, 0, 1)",
                        strokeColor: "rgba(0, 255, 0, 1)",
                        pointColor: "rgba(0, 255, 0, 1)",
                        pointStrokeColor: "#c1c7d1",
                        pointHighlightFill: "#fff",
                        pointHighlightStroke: "rgba(220,220,220,1)",
                        data:[
                            {!! empty($secondByNb->Uptown->Positives)? 0 : $secondByNb->Uptown->Positives!!},
                            {!! empty($secondByNb->Middletown->Positives)? 0 : $secondByNb->Middletown->Positives!!},
                            {!! empty($secondByNb->Downtown->Positives)? 0 : $secondByNb->Downtown->Positives !!}
                        ]
                    },
                    {
                        label: "Negatives",
                        fillColor: "rgba(255,0,0,0.9)",
                        strokeColor: "rgba(255,0,0,0.8)",
                        pointColor: "#3b8bba",
                        pointStrokeColor: "rgba(255,0,0,1)",
                        pointHighlightFill: "#fff",
                        pointHighlightStroke: "rgba(60,141,188,1)",
                        data: [
                            {!! empty($secondByNb->Uptown->Negatives)? 0 : $secondByNb->Uptown->Negatives!!},
                            {!! empty($secondByNb->Middletown->Negatives)? 0 : $secondByNb->Middletown->Negatives!!},
                            {!! empty($secondByNb->Downtown->Negatives)? 0 : $secondByNb->Downtown->Negatives !!}
                        ]
                    }
                ]
            };
            secondByNbBarChart.Bar(secondByNb, barChartOptions);

            //--------------
            //- CountByAge -
            //--------------
            // Get context with jQuery - using jQuery's .get() method.
            var countByAge = $("#countByAge").get(0).getContext("2d");
            // This will get the first returned node in the jQuery collection.
            var countByAgeBarChart = new Chart(countByAge);

            var countByAge = {
                labels: ["< 19", "20 - 29", "30 - 39", "40 - 49", "50 - 59", "60 - 69","70 - 79"],
                datasets: [
                    {
                        label: "Positives",
                        fillColor: "rgba(0, 255, 0, 1)",
                        strokeColor: "rgba(0, 255, 0, 1)",
                        pointColor: "rgba(0, 255, 0, 1)",
                        pointStrokeColor: "#c1c7d1",
                        pointHighlightFill: "#fff",
                        pointHighlightStroke: "rgba(220,220,220,1)",
                        data:[
                            {!! empty($countByAge[20]['Positives'])? 0 : $countByAge[20]['Positives']!!},
                            {!! empty($countByAge[30]['Positives'])? 0 : $countByAge[30]['Positives']!!},
                            {!! empty($countByAge[40]['Positives'])? 0 : $countByAge[40]['Positives'] !!},
                            {!! empty($countByAge[50]['Positives'])? 0 : $countByAge[50]['Positives']!!},
                            {!! empty($countByAge[60]['Positives'])? 0 : $countByAge[60]['Positives']!!},
                            {!! empty($countByAge[70]['Positives'])? 0 : $countByAge[70]['Positives'] !!},
                            {!! empty($countByAge[80]['Positives'])? 0 : $countByAge[80]['Positives']!!}
                        ]
                    },
                    {
                        label: "Negatives",
                        fillColor: "rgba(255,0,0,0.9)",
                        strokeColor: "rgba(255,0,0,0.8)",
                        pointColor: "#3b8bba",
                        pointStrokeColor: "rgba(255,0,0,1)",
                        pointHighlightFill: "#fff",
                        pointHighlightStroke: "rgba(60,141,188,1)",
                        data: [
                            {!! empty($countByAge[20]['Negatives'])? 0 : $countByAge[20]['Negatives']!!},
                            {!! empty($countByAge[30]['Negatives'])? 0 : $countByAge[30]['Negatives']!!},
                            {!! empty($countByAge[40]['Negatives'])? 0 : $countByAge[40]['Negatives'] !!},
                            {!! empty($countByAge[50]['Negatives'])? 0 : $countByAge[50]['Negatives']!!},
                            {!! empty($countByAge[60]['Negatives'])? 0 : $countByAge[60]['Negatives']!!},
                            {!! empty($countByAge[70]['Negatives'])? 0 : $countByAge[70]['Negatives'] !!},
                            {!! empty($countByAge[80]['Negatives'])? 0 : $countByAge[80]['Negatives']!!}
                        ]
                    }
                ]
            };

            countByAgeBarChart.Bar(countByAge, barChartOptions);

            //--------------
            //- FirstByAge -
            //--------------
            // Get context with jQuery - using jQuery's .get() method.
            var firstByAge = $("#firstByAge").get(0).getContext("2d");
            // This will get the first returned node in the jQuery collection.
            var firstByAgeBarChart = new Chart(firstByAge);

            var firstByAge = {
                labels: ["< 19", "20 - 29", "30 - 39", "40 - 49", "50 - 59", "60 - 69","70 - 79"],
                datasets: [
                    {
                        label: "Positives",
                        fillColor: "rgba(0, 255, 0, 1)",
                        strokeColor: "rgba(0, 255, 0, 1)",
                        pointColor: "rgba(0, 255, 0, 1)",
                        pointStrokeColor: "#c1c7d1",
                        pointHighlightFill: "#fff",
                        pointHighlightStroke: "rgba(220,220,220,1)",
                        data:[
                            {!! empty($firstByAge[20]['Positives'])? 0 : $firstByAge[20]['Positives']!!},
                            {!! empty($firstByAge[30]['Positives'])? 0 : $firstByAge[30]['Positives']!!},
                            {!! empty($firstByAge[40]['Positives'])? 0 : $firstByAge[40]['Positives'] !!},
                            {!! empty($firstByAge[50]['Positives'])? 0 : $firstByAge[50]['Positives']!!},
                            {!! empty($firstByAge[60]['Positives'])? 0 : $firstByAge[60]['Positives']!!},
                            {!! empty($firstByAge[70]['Positives'])? 0 : $firstByAge[70]['Positives'] !!},
                            {!! empty($firstByAge[80]['Positives'])? 0 : $firstByAge[80]['Positives']!!}
                        ]
                    },
                    {
                        label: "Negatives",
                        fillColor: "rgba(255,0,0,0.9)",
                        strokeColor: "rgba(255,0,0,0.8)",
                        pointColor: "#3b8bba",
                        pointStrokeColor: "rgba(255,0,0,1)",
                        pointHighlightFill: "#fff",
                        pointHighlightStroke: "rgba(60,141,188,1)",
                        data: [
                            {!! empty($firstByAge[20]['Negatives'])? 0 : $firstByAge[20]['Negatives']!!},
                            {!! empty($firstByAge[30]['Negatives'])? 0 : $firstByAge[30]['Negatives']!!},
                            {!! empty($firstByAge[40]['Negatives'])? 0 : $firstByAge[40]['Negatives'] !!},
                            {!! empty($firstByAge[50]['Negatives'])? 0 : $firstByAge[50]['Negatives']!!},
                            {!! empty($firstByAge[60]['Negatives'])? 0 : $firstByAge[60]['Negatives']!!},
                            {!! empty($firstByAge[70]['Negatives'])? 0 : $firstByAge[70]['Negatives'] !!},
                            {!! empty($firstByAge[80]['Negatives'])? 0 : $firstByAge[80]['Negatives']!!}
                        ]
                    }
                ]
            };
            firstByAgeBarChart.Bar(firstByAge, barChartOptions);

            //--------------
            //- SecondByGNb -
            //--------------
            // Get context with jQuery - using jQuery's .get() method.
            var secondByAge = $("#secondByAge").get(0).getContext("2d");
            // This will get the first returned node in the jQuery collection.
            var secondByAgeBarChart = new Chart(secondByAge);

            var secondByAge = {
                labels: ["< 19", "20 - 29", "30 - 39", "40 - 49", "50 - 59", "60 - 69","70 - 79"],
                datasets: [
                    {
                        label: "Positives",
                        fillColor: "rgba(0, 255, 0, 1)",
                        strokeColor: "rgba(0, 255, 0, 1)",
                        pointColor: "rgba(0, 255, 0, 1)",
                        pointStrokeColor: "#c1c7d1",
                        pointHighlightFill: "#fff",
                        pointHighlightStroke: "rgba(220,220,220,1)",
                        data:[
                            {!! empty($secondByAge[20]['Positives'])? 0 : $secondByAge[20]['Positives']!!},
                            {!! empty($secondByAge[30]['Positives'])? 0 : $secondByAge[30]['Positives']!!},
                            {!! empty($secondByAge[40]['Positives'])? 0 : $secondByAge[40]['Positives']!!},
                            {!! empty($secondByAge[50]['Positives'])? 0 : $secondByAge[50]['Positives']!!},
                            {!! empty($secondByAge[60]['Positives'])? 0 : $secondByAge[60]['Positives']!!},
                            {!! empty($secondByAge[70]['Positives'])? 0 : $secondByAge[70]['Positives']!!},
                            {!! empty($secondByAge[80]['Positives'])? 0 : $secondByAge[80]['Positives']!!}
                        ]
                    },
                    {
                        label: "Negatives",
                        fillColor: "rgba(255,0,0,0.9)",
                        strokeColor: "rgba(255,0,0,0.8)",
                        pointColor: "#3b8bba",
                        pointStrokeColor: "rgba(255,0,0,1)",
                        pointHighlightFill: "#fff",
                        pointHighlightStroke: "rgba(60,141,188,1)",
                        data: [
                            {!! empty($secondByAge[20]['Negatives'])? 0 : $secondByAge[20]['Negatives']!!},
                            {!! empty($secondByAge[30]['Negatives'])? 0 : $secondByAge[30]['Negatives']!!},
                            {!! empty($secondByAge[40]['Negatives'])? 0 : $secondByAge[40]['Negatives']!!},
                            {!! empty($secondByAge[50]['Negatives'])? 0 : $secondByAge[50]['Negatives']!!},
                            {!! empty($secondByAge[60]['Negatives'])? 0 : $secondByAge[60]['Negatives']!!},
                            {!! empty($secondByAge[70]['Negatives'])? 0 : $secondByAge[70]['Negatives']!!},
                            {!! empty($secondByAge[80]['Negatives'])? 0 : $secondByAge[80]['Negatives']!!}
                        ]
                    }
                ]
            };
            secondByAgeBarChart.Bar(secondByAge, barChartOptions);


        });

    </script>


@endsection