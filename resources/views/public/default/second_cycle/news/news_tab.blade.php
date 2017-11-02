<?php
	$news = $space->getNodes("news");
	usort($news,function($a,$b) use($space){
		$dA = $space->getAttribute($a,"start_date");
		$dB = $space->getAttribute($b,"start_date");
		if($dA == $dB)
			return 0;
		
		if($dA > $dB)
			return -1;

		return 1;

	});	
?>
<div class="margin-top">
<div class="row">
<div class="@if($canfollow && (ONE::isAuth() || !empty($followers))) col-sm-8 @endif col-xs-12">
<div class="table-responsive">
<table class="table table-news">
<thead>
<tr>
<th>{{trans("defaultSecondCycle.type")}}</th>
<th>{{trans("defaultSecondCycle.date")}}</th>
<th>{{trans("defaultSecondCycle.descriptionNews")}}</th>
</tr>
</thead>
<tbody>
@foreach($news as $n)
<tr>
<td>
	@if ($space->getAttribute($n,'code_type') == "type1")
	<span title="{{$space->getAttribute($n,'type')}}" class="fa fa-flag"></span>
	@elseif ($space->getAttribute($n,'code_type') == "type2")
		<span title="{{$space->getAttribute($n,'type')}}" class="fa fa-check"></span> 
	@else
		<span title="{{$space->getAttribute($n,'type')}}" class="fa fa-remove"></span>
	@endif

</td>
<td>{{$space->getAttribute($n,"start_date")}}</td>
<td>{{$space->getAttribute($n,"description")}}</td>
</tr>
@endforeach
</tbody>
</table>
</div>
</div>
@if($canfollow && (ONE::isAuth() || !empty($followers)))
<div class="col-sm-4 col-xs-12">
<div class="follow-bar">
@if(!ONE::isAuth() || !isset($followers[ONE::getUserKey()]->topic_follower_key))
<button id="followTopicBtn" class="btn btn-default followBtn backButton" onclick="followTopic();">
                    <span>{{trans('defaultSecondCycle.follow')}}</span>
                </button>
@else
<button id="unfollowTopicBtn" class="btn btn-default followBtn backButton" onclick="unfollowTopic();">
                    <span>{{trans('defaultSecondCycle.unfollow')}}</span>
                </button>
@endif
</div>
@if (count($followers) == 0)
<div class="followers-text">{{trans("defaultSecondCycle.noFollowers")}}</div>
@else
<div class="followers-text">{{count($followers)}} {{trans("defaultSecondCycle.followers")}}</div>
<div class="row list-followers">
@foreach($followers as $follower)
<div class="col-lg-3 col-md-4 col-sm-6 col-xs-12 follower-buffer margin-bottom-15 min-height-80">
                                                    @if (isset($usersNames[$follower->user_key]['photo_id']) && ($usersNames[$follower->user_key]['photo_id'] > 0))
                                                        <img class="img-sm img-circle img-user-follow"
                                                             src="{{URL::action('FilesController@download',[$usersNames[$follower->user_key]['photo_id'], $usersNames[$follower->user_key]['photo_code'], 1])}}">
                                                    @else
                                                        <img class="img-sm img-circle img-user-follow"
                                                             src="{{ asset('images/icon-user-default-160x160.png') }}">
                                                    @endif
      						</div>
@endforeach
</div>
@endif
</div>
@endif
</div>
</div>

<script>
       function followTopic() {
            @if (ONE::isAuth())
		$('.followBtn').addClass('disabled');       
                $.ajax({
                method: 'POST', // Type of response and matches what we said in the route
                url: '{{action('PublicTopicController@followTopic')}}', // This is the url we gave in the route
                data: {
                    topic_key: '{{$topicKey}}',
                    action_type: 'follow_topic',
                    _token: "{{ csrf_token() }}"
                }, // a JSON object to send back
                success: function (response) { // What to do if we succeed
                    if (response == 'true') {
                        window.location.reload(true);

                    }
                    return false;
                },
                error: function () { // What to do if we fail
                }
            });
            @else
                window.location.href = "{{ action("AuthController@login") }}";
            @endif
                return false;
        }

       function unfollowTopic() {
	       @if (ONE::isAuth())
		$('.followBtn').addClass('disabled');       
                $.ajax({
                method: 'POST', // Type of response and matches what we said in the route
                url: '{{action('PublicTopicController@followTopic')}}', // This is the url we gave in the route
                data: {
                    topic_key: '{{$topicKey}}',
                    action_type: 'unfollow_topic',
                    _token: "{{ csrf_token() }}"
                }, // a JSON object to send back
                success: function (response) { // What to do if we succeed
                    if (response == 'true') {
                        window.location.reload(true);

                    }
                    return false;
                },
                error: function () { // What to do if we fail
                }
            });
            @else
                window.location.href = "{{ action("AuthController@login") }}";
            @endif
                return false;
        }

</script>
