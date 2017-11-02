<style>
    #timeline {
        background: url({{ asset('images/timeline-dot.gif') }}) top center repeat-y;
        width: 100%;
        padding: 50px 0;
        margin: 0 auto 50px auto;
        overflow: hidden;
        list-style: none;
        position: relative;
    }

    #timeline:before, /* The dot */
    #timeline:after { /* The arrow */
        content: " ";
        width: 10px;
        height: 10px;
        display: block;
        background: #f43059;
        position: absolute;
        top: 0;
        left: 50%;
        margin-left: -5px;

        -webkit-border-radius: 20px;
        -moz-border-radius: 20px;
        border-radius: 20px;
    }

    #timeline:after {
        margin-left: -7px;
        background: none;
        border: 7px solid transparent;
        border-top-color: #f43059;
        width: 0;
        height: 0;
        top: auto;
        bottom: -7px;

        -webkit-border-radius: 0;
        -moz-border-radius: 0;
        border-radius: 0;
    }

    #timeline li:before,
    #timeline li:after {
        content: " ";
        width: 12%;
        height: 1px;
        background: #f43059;
        position: absolute;
        left: 100%;
        top: 50%;
        background: -moz-linear-gradient(0, #d8d566, #f43059);
        background: -webkit-gradient(linear, left top, right top, from(#d8d566), to(#f43059));
    }

    #timeline li:nth-of-type(even) {
        float: right;
        text-align: left;
    }

    #timeline li:nth-of-type(even):after { /* Move branches */
        background: -moz-linear-gradient(0, #f43059, #d8d566);
        background: -webkit-gradient(linear, left top, right top, from(#f43059), to(#d8d566));
        left: auto;
        right: 100%;
    }

    #timeline li:nth-of-type(odd),
    #timeline li:nth-of-type(even) {
        margin: -10px 0 0 0;
    }

    #timeline li {
        position: relative;
        clear: both;
        float: left;
        width: 45%;
        padding: 10px;
        background: #eee;
        border: 1px solid #ccc;
        text-align: right;
        margin: 0 0 10px 0;
        -webkit-border-radius: 2px;
        -moz-border-radius: 2px;
        border-radius: 2px;
        -webkit-box-shadow: 0 1px #fff inset;
        -moz-box-shadow: 0 1px #fff inset;
        box-shadow: 0 1px #fff inset;
    }
</style>

@if(count($flagHistory)>0)
<ol id="timeline">

    @foreach($flagHistory as $flag)
        <li>

            <p>
                <i class="fa fa-flag" aria-hidden="true"></i>
                <bold>{{ $flag->translations[0]->title }}</bold>
                <small>({{ $flag->translations[0]->description }})</small>
            </p>

            <p>
                <small><i class="fa fa-user" aria-hidden="true"></i>
                    <bold>{{ $flag->pivot->created_by }}</bold>
                </small>
            </p>
            @if(!empty($flag->attachmentDescription))
                <p>
                    <small><i class="fa fa-comment" aria-hidden="true"></i> {{ $flag->attachmentDescription }}</small>
                </p>
            @endif

            <small>
                <time><i class="fa fa-clock-o" aria-hidden="true"></i> {{ $flag->pivot->created_at }}</time>
            </small>
        </li>
    @endforeach


</ol>
@else
<div class="col-12 text-center"><h3>{{trans('privateCbs.no_flag_history_available')}}</h3></div>
@endif