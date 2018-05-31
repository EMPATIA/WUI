@if(Session::get("can-show-start-modal",true))
    @php
        Session::put("can-show-start-modal",false);
        $layoutSections = \App\Http\Controllers\PublicContentManagerController::getSections('popup');
    @endphp
    @if(!empty($layoutSections))
        <!-- popup modal -->
        <div id="start-modal" class="modal fade show">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                        {{-- $pageContent->title --}}
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="{{ trans("home.close") }}">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        @php
                            $layoutSections = collect($layoutSections)->keyBy("id");
                        @endphp
                        @foreach(!empty($layoutSections) ? $layoutSections : [] as $layoutSection)
                            @if($layoutSection)
                                @includeif("public.default.sections." . $layoutSection->section_type->code, ['section' => $layoutSection])
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <script>
            $(document).ready(function() {
                <?php Session::put("can-show-start-modal",false); ?>
                $('#start-modal').modal('show');
            });
        </script>
    @endif
@endif