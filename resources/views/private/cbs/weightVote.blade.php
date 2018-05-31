<div class="row">
    <div class="col-12">
        <label for="{{$weightId}}_pos">Position</label>
        <input type="number" class="form-control" name="{{$weightId}}_pos">
        <label for="{{$weightId}}_weight">Vote weight</label>
        <input type="number" class="form-control" name="{{$weightId}}_weight">
        <div style="padding: 10px;">
            {!! Form::oneTabs($langs) !!}
        </div>
    </div>
</div>