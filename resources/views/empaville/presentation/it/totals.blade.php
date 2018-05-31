@if(isset($voteSession))
    <div class="row">
        {{--TOTAL VOTES INFORMATION--}}
        <div class="col-md-12">
            <div class="box box-success">
                <div class="box-header">
                    {{trans('dashboard.count_total_votes')}}
                </div>
                <div class="box-body">
                    <div class="col-sm-3 col-md-3 col-xs-3 text-center">

                        <div class="row">
                            <img src="/images/total_voters.png" height="75" width="75" >
                        </div>
                        <div class="row">
                            <strong>{{trans('dashboard.total_voters')}}</strong>
                        </div>
                        <div class="row">
                            {{$voteSession['summary']->total_users_voted}}
                        </div>
                    </div>
                    <div class="col-sm-3 col-md-3 col-xs-3 text-center">
                        <div class="row">
                            <img src="/images/total_votes.png"  height="75" width="75" >
                        </div>
                        <div class="row">
                            <strong>{{trans('dashboard.total_votes')}}</strong>
                        </div>
                        <div class="row">
                            {{$voteSession['summary']->total}}
                        </div>
                    </div>
                    <div class="col-sm-3 col-md-3 col-xs-3 text-center">

                        <div class="row">
                            <img src="/images/positive_votes.png" height="75" width="75" >
                        </div>
                        <div class="row">
                            <strong>{{trans('dashboard.total_positive_votes')}}</strong>
                        </div>
                        <div class="row">
                            {{$voteSession['summary']->total_positives}}
                        </div>
                    </div>
                    <div class="col-sm-3 col-md-3 col-xs-3 text-center">

                        <div class="row">
                            <img src="/images/negative_votes.png" height="75" width="75">
                        </div>
                        <div class="row">
                            <strong>{{trans('dashboard.total_negative_votes')}}</strong>
                        </div>
                        <div class="row">
                            {{$voteSession['summary']->total_negatives}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{--END TOTAL VOTES INFORMATION--}}
    </div>
    <div class="row">
        {{--TOP 10--}}
        <div class="col-md-12 col-lg-12 col-xs-12">
            <div class="box box-success">
                <div class="box-header">
                    <h3 class="box-title">{{trans('empaville.top')}}</h3>
                </div>
                <div class="box-body no-padding">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <tbody>
                            <tr>
                                <th style="width: 10px">#</th>
                                <th>{{trans('empaville.totalsProposal')}}</th>
                                <th style="width: 30px">{{trans('empaville.totalsBudget')}}</th>
                                <th class=" text-center" style="width: 30px">{{trans('empaville.totalsBalance')}}</th>
                                <th class=" text-center" style="width: 10px">{{trans('empaville.totalsPositives')}}</th>
                                <th class=" text-center" style="width: 10px">{{trans('empaville.totalsNegatives')}}</th>
                            </tr>
                            @foreach($voteSession["top"] as $key => $topProposal)
                                <tr style="{{$topProposal->winner ? "background-color: #339966;": ""}}">
                                    <td>{{$key + 1 }} </td>
                                    <td>{{$topProposal->title}}</td>
                                    <td>{{$topProposal->budget}}</td>
                                    <td class=" text-center">
                                        @if($topProposal->balance >= 0 )
                                            <span class="badge bg-green"> {{$topProposal->balance}}</span>
                                        @else
                                            <span class="badge bg-red"> {{$topProposal->balance}}</span>
                                        @endif
                                    </td>
                                    <td class=" text-center">{{$topProposal->positives}}</td>
                                    <td class=" text-center">{{$topProposal->negatives}}</td>

                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        {{--TOP 10 END--}}
    </div>
@else
    <div class="col-sm-8 col-md-8 col-lg-8">
        <div class="alert alert-warning">
            <h4><i class="icon fa fa-warning"></i> Alert!</h4>

            <p>No data to display...</p>
        </div>
    </div>
@endif