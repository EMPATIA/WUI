<!-- CB Details -->
{!! Form::oneText('title', array("name"=>trans('privateCbs.title'),"description"=>trans('privateCbs.help_title')), isset($cb) ? $cb->title : null, ['class' => 'form-control', 'id' => 'title', 'required' => 'required']) !!}
{!! Form::oneTextArea('description',  array("name"=>trans('privateCbs.description'),"description"=>trans('privateCbs.help_description')), isset($cb) ? $cb->contents : null, ['class' => 'form-control', 'id' => 'description', 'rows' =>3]) !!}
{!! Form::oneDate('start_date', array("name"=>trans('privateCbs.startDate'),"description"=>trans('privateCbs.help_start_date')), isset($cb) ? $cb->start_date : date('Y-m-d'), ['class' => 'form-control oneDatePicker', 'id' => 'start_date', 'required' => 'required']) !!}
{!! Form::oneDate('end_date',array("name"=>trans('privateCbs.endDate'),"description"=>trans('privateCbs.help_end_date')), isset($cb) && $cb->end_date!=null ? $cb->end_date  : '', ['class' => 'form-control oneDatePicker', 'id' => 'end_date']) !!}
<!-- cb_key hidden field  -->
{!! Form::hidden('cb_key', isset($cb) ? $cb->cb_key : 0, ['id' => 'cb_key']) !!}