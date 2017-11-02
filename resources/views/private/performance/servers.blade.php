<label>Select a Server</label>
<div class="row">
    <div class="col-12 col-sm-10 col-md-8 col-lg-6">
        <select id="serverFilter" name="serverFilter" class="form-control">
            <option value="not">Select</option>
            @foreach ($servers as $server)
                    <option value="{{$server['ip']}}">{{$server['ip']}}</option>
            @endforeach
        </select>
    </div>
</div>