
    <div class="row">
        {{--TOTAL VOTES INFORMATION--}}
        <div class="col-md-12">
            <div class="">
                <div class="box-header">
                    <h3 class="box-title"><i class="fa"></i> {{trans('dashboard.count_total_votes')}}</h3>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-3 text-center">
                            <div class="">
                                <img src="/images/total_voters.png" height="100" width="100" >
                            </div>
                            <div class="">
                                <strong>{{trans('dashboard.total_voters')}}</strong>
                            </div>
                            <div class="">
                                {{isset($voteSession['summary']->total_users_voted) ? $voteSession['summary']->total_users_voted : null}}
                            </div>
                        </div>
                        <div class="col-sm-3 text-center">
                            <div class="">
                                <img src="/images/total_votes.png"  height="100" width="100" >
                            </div>
                            <div class="">
                                <strong>{{trans('dashboard.total_votes')}}</strong>
                            </div>
                            <div class="">
                                {{isset($voteSession['summary']->total) ? $voteSession['summary']->total : null}}
                            </div>
                        </div>
                        <div class="col-sm-3 text-center">

                            <div class="">
                                <img src="/images/positive_votes.png" height="100" width="100" >
                            </div>
                            <div class="">
                                <strong>{{trans('dashboard.total_positive_votes')}}</strong>
                            </div>
                            <div class="">
                                {{isset($voteSession['summary']->total_positives) ? $voteSession['summary']->total_positives : null}}
                            </div>
                        </div>
                        <div class="col-sm-3 text-center">

                            <div class="">
                                <img src="/images/negative_votes.png" height="100" width="100">
                            </div>
                            <div class="">
                                <strong>{{trans('dashboard.total_negative_votes')}}</strong>
                            </div>
                            <div class="">
                                {{isset($voteSession['summary']->total_negatives) ? $voteSession['summary']->total_negatives : null}}
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
            <div class="box box-success">
                <div class="box-header">
                    <h3 class="box-title">{{trans('empaville.top')}}</h3>
                </div>
                <div class="box-body">
                    <table class="table table-responsive  table-striped">
                        <tbody>
                        <tr>
                            <th style="width: 10px">#</th>
                            <th>{{trans('empaville.totalsProposal')}}</th>
                            <th class=" text-center" style="width: 30px">{{trans('empaville.totalsBalance')}}</th>
                            <th class=" text-center" style="width: 10px">{{trans('empaville.totalsPositives')}}</th>
                            <th class=" text-center" style="width: 10px">{{trans('empaville.totalsNegatives')}}</th>
                        </tr>
                        @if(isset($voteSession["top"]))
                            @foreach($voteSession["top"] as $key => $topProposal)
                                <tr style="{{$topProposal->winner ? "background-color: #c2dcf1;": ""}}">
                                    <td>{{$key + 1 }} </td>
                                    <td>{{$topProposal->title}}</td>
                                    <td class=" text-center">
                                        @if($topProposal->balance >= 0 )
                                            <span class="label bg-green"> {{$topProposal->balance}}</span>
                                        @else
                                            <span class="label bg-red"> {{$topProposal->balance}}</span>
                                        @endif
                                    </td>
                                    <td class=" text-center">{{$topProposal->positives}}</td>
                                    <td class=" text-center">{{$topProposal->negatives}}</td>

                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>