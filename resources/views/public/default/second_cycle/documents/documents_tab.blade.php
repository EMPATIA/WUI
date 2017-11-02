<div class="margin-top">
<div class="table-responsive">
<table class="table table-documents">
<thead>
<tr>
<th>{{trans("defaultSecondCycle.date")}}</th>
<th>{{trans("defaultSecondCycle.short_description")}}</th>
<th>{{trans("defaultSecondCycle.downloadDocument")}}</th>
<th>{{trans("defaultSecondCycle.responsable")}}</th>
</tr>
</thead>
<tbody>
@foreach($space->getNodes("documents") as $p)
<tr>
<td>{{$space->getAttribute($p,"start_date")}}</td>
<td>{{$space->getAttribute($p,"description")}}</td>
<td>
<?php $tmp = $space->getAttribute($p,"files"); ?>
@if (isset($tmp->docs))
<a href="{!! action('FilesController@download', [$tmp->docs[0]->file_id, $tmp->docs[0]->file_code])!!}" title="{{trans("defaultSecondCycle.downloadDocument")}}"><span class="fa fa-download"></span></a>	
@endif
</td>

<td>{{$space->getAttribute($p,"created_on_behalf")}}</td>
</tr>
@endforeach
</tbody>
</table>
</div>
</div>
