@extends('private._private.index')

@section('content')
       @if(ONE::isEntity())

        @php $form = ONE::form('entitiesDivided', trans('privateEntities.details'), 'orchestrator', 'entity')
                ->settings(["model" => isset($entity) ? $entity : null])
                ->show('EntitiesDividedController@edit', null, ['id' => isset($entity) ? $entity->entity_key : null])
                ->create('EntitiesDividedController@store', 'EntitiesDividedController@index', ['id' => isset($entity) ? $entity->entity_key : null])
                ->edit('EntitiesDividedController@update', 'EntitiesDividedController@showEntity', ['id' => isset($entity) ? $entity->entity_key : null])
                ->open();

        @endphp
    @else
        @php $form = ONE::form('entitiesDivided', trans('privateEntities.details'))
                ->settings(["model" => isset($entity) ? $entity : null])
                ->show('EntitiesController@edit', ['id' => isset($entity) ? $entity->entity_key : null], 'EntitiesController@showEntityM')
                ->create('EntitiesController@store', 'EntitiesController@index', ['id' => isset($entity) ? $entity->entity_key : null])
                ->edit('EntitiesController@update', 'EntitiesController@show', ['id' => isset($entity) ? $entity->entity_key : null])
                ->open();
        @endphp
    @endif

    {!! Form::oneText('name', trans('privateEntities.name'), isset($entity) ? $entity->name : null, ['class' => 'form-control', 'id' => 'name']) !!}
    {{--@endif--}}

    {!! Form::oneText('designation', trans('privateEntities.designation'), isset($entity) ? $entity->designation : null, ['class' => 'form-control', 'id' => 'designation']) !!}
    {!! Form::oneText('description', trans('privateEntities.description'), isset($entity) ? $entity->description : null, ['class' => 'form-control', 'id' => 'description']) !!}
    {!! Form::oneText('url', trans('privateEntities.url'), isset($entity) ? $entity->url : null, ['class' => 'form-control', 'id' => 'url']) !!}
    {!! Form::oneSelect('country_id', trans('privateEntities.country'), isset($country) ? $country : null, isset($entity) ? $entity->country_id : null, isset($entity->country->name) ? $entity->country->name: null, ['class' => 'form-control', 'id' => 'country_id']) !!}
    {!! Form::oneSelect('timezone_id', trans('privateEntities.timezone'), isset($timezone) ? $timezone : null, isset($entity) ? $entity->timezone_id : null, isset($entity->timezone->code) ? $entity->timezone->code : null, ['class' => 'form-control', 'id' => 'timezone_id']) !!}
    {!! Form::oneSelect('currency_id', trans('privateEntities.currency'), isset($currency) ? $currency : null, isset($entity) ? $entity->currency_id : null, isset($entity->currency->currency) ? $entity->currency->currency : null, ['class' => 'form-control', 'id' => 'currency_id']) !!}
    {!! $form->make() !!}

       <div class="row">
           <div class="col-xl-12 ">
               <div class="col info-box">
                   <!-- Cb Check list -->
                   @include('private.cbs.cbCheckList')
               </div>
           </div>
       </div>


@endsection

@section('scripts')
    <script>
        //Check List
        $( "#submitCheckList, #line" ).hide();
        //update checkbox
        function checkChanged(checkboxElem, checklist_key) {
            var checked = false;
            var state = 'none';
            if (checkboxElem.checked) {
                $('#check_' + checklist_key).css('text-decoration','line-through');
                checked = true;
                state = 'done';
            }
            else {
                $('#check_' + checklist_key).css('text-decoration','none');
                $('#check').attr('value','none');
            }

            updateChecklistItem(checked, checklist_key, state);
        }

        function  updateChecklistItem(checked, checklist_key, state) {
            $.ajax({
                method: 'get', // Type of response and matches what we said in the route
                url: "{{action('CbsController@updateChecklistItem')}}", // This is the url we gave in the route
                data: {
                    'checklist_key': checklist_key,
                    'checked': checked,
                    'state': state
                }, // a JSON object to send back
                success: function (response) { // What to do if we succeed
                    if (response != 'false') {
                        toastr.success('{{ trans('privateCbs.update_checkList_state_ok') }}', '', {timeOut: 3000,positionClass: "toast-bottom-right"});
                        location.reload();
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                    $('#updateStatusModal').modal('hide');
                    toastr.error('{{ trans('privateCbs.error_update_checklist_state_ok') }}', '', {timeOut: 3000,positionClass: "toast-bottom-right"});

                }
            });
        }

        //add Cb Check list
        function addChecklist() {
            $.ajax({
                type: "GET",
                url: '{{action("CbsController@addCheckList")}}',
                data: {
                    "_token"  : "{{ csrf_token() }}",
                },
                success: function (response) {
                    $('#addCheckListRow').append(response);
                    $( "#submitCheckList, #line" ).show();
                },
                error: function (response) {
                }
            });
        };

        //create checklist
        function checkChangedNewItem(checkboxElem) {
            if (checkboxElem.checked) {
                checkboxElem.value = 'done';
            }else{
                checkboxElem.value = 'none';
            }
        }

        $( "#checkList" ).submit(function()
        {
            var checked = [];
            $("input[name='checkList_checkbox[]']").each(function ()
            {
                checked.push(($(this).val()));
            });

            var state = [];
            $(".append_state").each( function() {
                state.push(($(this).attr('name')));
            });

            var text = $('input[name="checkList_text[]"]').map(function () {
                return this.value;
            }).get();

            $.ajax({
                method: 'GET',
                url: "{{action('CbsController@createChecklistItem')}}",
                data:{
                    "_token"    : "{{ csrf_token() }}",
                    "text"      : text,
                    "checked"   : checked,
                    "state"     : state,
                    "cbKey"     : null,
                    "entityKey" : '{{ $entityKey }}'
                },
                success: function (response) {
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    toastr.error('{{ trans('privateCbs.error_updating_state_or_sending_email_to_user') }}', '', {timeOut: 3000,positionClass: "toast-bottom-right"});
                }
            });
        });

        function selectNewItem(element){
            element.parentElement.parentElement.firstElementChild.innerText = element.text;
            element.parentElement.parentElement.firstElementChild.name = element.getAttribute("name");
        }

        //remove new check list
        function removeNewCheckList(element) {
            $(element).closest('#addCheckList').remove();
        }

        //remove check list from data base
        function removeCheckList(checklist_key) {
            $.ajax({
                method: 'get', // Type of response and matches what we said in the route
                url: "{{action('CbsController@removeCheckListItem')}}", // This is the url we gave in the route
                data: {
                    "_token"    : "{{ csrf_token() }}",
                    'checklist_key': checklist_key,
                }, // a JSON object to send back
                success: function (response) { // What to do if we succeed
                    toastr.success('{{ trans('privateCbs.remove_checklist_ok') }}', '', {timeOut: 3000,positionClass: "toast-bottom-right"});
                    location.reload();
                },
                error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail

                    toastr.error('{{ trans('privateCbs.error_remove_checklist_ko') }}', '', {timeOut: 3000,positionClass: "toast-bottom-right"});
                }
            });
        }


    </script>
@endsection

