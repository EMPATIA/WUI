<option selected="selected" disabled>
    {{trans('privateCbsMenuTranslations.select_value')}}
</option>
@foreach($cbsEntity as $iteratedCbKey=>$iteratedCbTitle)
    @if($iteratedCbKey!=$cbKey)
        <option value="{{$iteratedCbKey}}">{{$iteratedCbTitle}}</option>
    @endif
@endforeach