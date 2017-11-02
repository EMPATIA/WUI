<div class="box">
    <div class="box-header with-border">

        <h3 class="box-title">{{ trans('private.cenas') }}</h3>

        <div class="box-tools pull-right">


            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
        </div>

    </div>
    <!-- /.box-header -->
    <div class="box-body">

        <select name="srngrsfd">
            <option>{{ trans('select') }}</option>
            @foreach($fieldType as $type)
                <option value="{{$type->id}}">{{ $type->name }} </option>
            @endforeach
        </select>
        <br>

        <div>
            <label for="value1">Value 1</label>
            <label for="value2">Value 2</label>
            <input name="value1" type="text"> x <input name="value2" type="text">

            <?php $form->openTabs('tab-translation-'); ?>
            <div style="padding: 10px;">
            </div>
            <?php $form->makeTabs() ?>
        </div>
    </div>
</div>