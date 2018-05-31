
<div class="col-6 col-md-8">
</br>
    <label for="copy_translation">{{trans('privateCbsTranslations.cbs')}}</label><br>
    <select id="cbs" style="width:100%;" class="form-control" name="cbs">
        @foreach($cbsEntity as  $cbs)
            <option value="{{$cbs->id}}">{{$cbs->title}}</option>
        @endforeach
    </select>
</div>
<a type="button" id="button_confirm" class="btn btn-flat btn-success pull-left" style="margin-top:45px;" href="javascript:confirm()">{{trans('privateCbsTranslations.confirm')}}</a>
