<div class="row">
    <div class="col-sm-12 col-md-6">
        <label for="statlbl">
            Choose Status Filter
                <select id="statlbl" class="statusTypes form-control" style="width: 100%;" name="filters[]" multiple="multiple">
                @foreach($statusTypes as $statusType)
                        <option value="{{$statusType->code}}">{{$statusType->name}}</option>
                @endforeach
            </select>
        </label>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('.statusTypes').select2({
            width: 'resolve',
            placeholder: 'Select Status:',
            allowClear: true
        });
    });
</script>