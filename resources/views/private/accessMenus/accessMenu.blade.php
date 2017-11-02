@extends('private._private.index')

@section('content')

    @php
    $form = ONE::form('accessMenus',trans('privateAccessMenus.details'), 'cm', 'menu')
        ->settings(["model" => isset($accessMenu) ? $accessMenu : null])
        ->show('AccessMenusController@edit', 'AccessMenusController@delete', ['id' => isset($accessMenu) ? $accessMenu->id : null], 'AccessMenusController@index', ['id' => isset($accessMenu) ? $accessMenu->id : null])
        ->create('AccessMenusController@store', 'AccessMenusController@index', ['id' => isset($accessMenu) ? $accessMenu->id : null])
        ->edit('AccessMenusController@update', 'AccessMenusController@show', ['id' => isset($accessMenu) ? $accessMenu->id : null])
        ->open();
    @endphp
    {!! Form::oneText('name', array("name"=>trans('privateAccessMenu.name'),"description"=>trans('privateAccessMenu.nameDescription')), isset($accessMenu) ? $accessMenu->name : null, ['class' => 'form-control', 'id' => 'name']) !!}
    {!! Form::oneText('description', array("name"=>trans('privateAccessMenu.description'),"description"=>trans('privateAccessMenu.descriptionDescription')), isset($accessMenu) ? $accessMenu->description : null, ['class' => 'form-control', 'id' => 'description']) !!}
    {!! Form::oneSelect('site_key',  array("name"=>trans('privateAccessMenu.site'),"description"=>trans('privateAccessMenu.siteDescription')), isset($sites) ? $sites : null, !empty($accessMenu->site->key) ? $accessMenu->site->key : null, null, ['class' => 'form-control', 'id' => 'site_key']) !!}
    {!! Form::oneCheckbox('active', trans('privateAccessMenu.active'), 1, isset($accessMenu->active) ? $accessMenu->active : null, ['id' => 'active']) !!}

    {!! $form->make() !!}

    {{--@if (ONE::actionType('accessMenus') == 'show')--}}
    {{--@include('private.accessMenus.indexTree',["menu" => $menu, "accessM" => $accessM])--}}
    {{--@endif--}}

@endsection


@section('scripts')
    @if (ONE::actionType('accessMenus') == 'show')
        <script>
            $('.dd').nestable({
                dropCallback: function (details) {

                    var order = [];
                    $("li[data-id='" + details.destId + "']").find('ol:first').children().each(function (index, elem) {
                        order[index] = $(elem).attr('data-id');
                    });
                    if (order.length === 0) {
                        var rootOrder = [];
                        $("#nestable > ol > li").each(function (index, elem) {
                            rootOrder[index] = $(elem).attr('data-id');
                        });
                    }

                    $.post('{{ URL::action('MenusController@updateOrder')}}',
                        {
                            _token: "{{ csrf_token() }}",
                            source: details.sourceId,
                            destination: details.destId,
                            order: JSON.stringify(order),
                            rootOrder: JSON.stringify(rootOrder)
                        },
                        function (data) {
                            // console.log('data '+data);
                        })
                        .done(function (result) {
                            if (result == 'true') {
                                $("#menuReorder").fadeIn(100).delay(1500).fadeOut(100);
                            } else {
                                indexFiltered();
                            }
                        })
                        .fail(function () {
                        })
                        .always(function () {
                        });
                }
            });

            $('.dd').nestable('collapseAll');

            $('#menu-buttons').on('click', function (e) {
                var target = $(e.target),
                    action = target.data('action');
                if (action === 'expand-all') {
                    $('.dd').nestable('expandAll');
                }
                if (action === 'collapse-all') {
                    $('.dd').nestable('collapseAll');
                }
            })

            function indexFiltered(){
                window.location = '{{ action('AccessMenusController@show', isset($accessM) ? $accessM : null) }}';
            }

        </script>
    @endif
@endsection
