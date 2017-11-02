

<div class="new-element-configurations">
    <input type="hidden" name="dash_board_element" value="{{ $dashBoardElement->id }}">
    <a class="show-element-configurations">{!! $dashBoardElement->title !!}</a>
    @include('private.dashBoardElements.configureDashBoardElement',['configurations' => $dashBoardElement->configurations])
    <a class="btn btn-success add-new-element">{{ trans('dashBoardElements.save') }}</a>
</div>



@section('scripts')
    <script>
        $(document).on('click', '.add-new-element', function () {
            var form = $(this).parent('.new-element-configurations');
            var inputs = $(form).find('select,input').serializeArray();
            $.ajax({
                method: 'POST', // Type of response and matches what we said in the route
                url: "{{action('DashBoardElementsController@setUserDashBoardElement')}}", // This is the url we gave in the route
                data: {
                    "_token": "{{ csrf_token() }}",
                    "inputs": inputs
                }, beforeSend: function () {

                }, success: function (response) { // What to do if we succeed
                }
            });
        });
    </script>
@endsection