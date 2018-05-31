<!-- CB Details -->
{!! Form::oneText('title', array("name"=>trans('privateCbs.title'),"description"=>trans('privateCbs.helpTitle')), isset($cb) ? $cb->title : null, ['class' => 'form-control', 'id' => 'title', 'required' => 'required']) !!}
{!! Form::oneTextArea('description',  array("name"=>trans('privateCbs.description'),"description"=>trans('privateCbs.helpDescription')), isset($cb) ? $cb->contents : null, ['class' => 'form-control', 'id' => 'description', 'rows' =>3]) !!}
@if (!is_null($parentCbKey) || isset($cb) && $type == "project_2c")
{!! Form::oneText('tag', array("name"=>trans('privateCbs.tag'),"description"=>trans('privateCbs.helpTag')), isset($cb) ? $cb->tag : null, ['class' => 'form-control', 'id' => 'title', 'required' => 'required']) !!}
@endif
{!! Form::oneText('template', array("name"=>trans('privateCbs.template'),"description"=>trans('privateCbs.helpTemplate')), isset($cb) ? $cb->template : null, ['class' => 'form-control', 'id' => 'template']) !!}
{!! Form::oneDate('start_date', array("name"=>trans('privateCbs.startDate'),"description"=>trans('privateCbs.helpStartDate')), isset($cb->start_date) ? $cb->start_date : date('Y-m-d'), ['class' => 'form-control oneDatePicker', 'id' => 'start_date', 'required' => 'required']) !!}
{!! Form::oneDate('end_date',array("name"=>trans('privateCbs.endDate'),"description"=>trans('privateCbs.helpEndDate')), isset($cb->end_date) ? $cb->end_date : "", ['class' => 'form-control oneDatePicker', 'id' => 'end_date']) !!}
{!! Form::oneSelect('page_key', ['name' => trans('privateCbs.page'),'description' =>trans("privateCbs.pageDescription")],
                    !empty($contentListType) ? $contentListType : [],
                    null,
                    null,
                    ['class' => 'form-control'] ) !!}
<!-- cb_key hidden field  -->
{!! Form::hidden('cb_key', isset($cb) ? $cb->cb_key : 0, ['id' => 'cb_key']) !!}
