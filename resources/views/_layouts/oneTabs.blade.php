<div class="panel panel-default">
    <div class="panel-body">
        <ul class="nav nav-tabs" role="tablist" >
            @foreach ($contents as $key => $row)
            <li role="presentation" class="nav-item" >
                <a href="#{{$key}}" aria-controls="affa" role="tab" data-toggle="tab" class="nav-link @if($row == reset($contents)) active @endif">{{$row['title']}}</a>
            </li>
            @endforeach
        </ul>
        <!-- Tab panes -->
        <div class="tab-content">
            @foreach ($contents as $key => $row)
                <div role="tabpanel" class="tab-pane @if($row == reset($contents)) fade show active @else fade @endif nav-link" id="{{$key}}">{!! html_entity_decode($row['html']) !!}</div>
            @endforeach
        </div>
    </div>
</div>